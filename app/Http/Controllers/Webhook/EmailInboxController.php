<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\EmailInboxForward;
use App\Models\Expense;
use App\Models\Workspace;
use App\Services\Ai\Features\OcrReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Inbound email webhook receiver. Compatible with Postmark / Mailgun / SES SNS payload formats.
 * Inbound address pattern: expenses+ws{workspace_id}@invocely.app → workspace routing
 */
class EmailInboxController extends Controller
{
    public function handle(Request $request)
    {
        // Provider-agnostic: extract common fields
        $payload = $request->all();
        $toAddress = $payload['ToFull'][0]['Email'] ?? $payload['recipient'] ?? $payload['mail']['destination'][0] ?? '';
        $fromAddress = $payload['FromFull']['Email'] ?? $payload['sender'] ?? $payload['mail']['source'] ?? '';
        $subject = $payload['Subject'] ?? $payload['subject'] ?? '';
        $attachments = $payload['Attachments'] ?? $payload['attachments'] ?? [];

        // Parse workspace from address: expenses+ws{id}@invocely.app
        if (! preg_match('/expenses\+ws(\d+)@/', $toAddress, $matches)) {
            return response()->json(['ok' => false, 'reason' => 'Invalid inbound address']);
        }
        $workspaceId = (int) $matches[1];

        $workspace = Workspace::find($workspaceId);
        if (! $workspace) {
            return response()->json(['ok' => false, 'reason' => 'Workspace not found']);
        }

        // Save raw email
        $rawPath = 'inbox-emails/' . $workspaceId . '/' . Str::random(20) . '.json';
        Storage::disk('local')->put($rawPath, json_encode($payload));

        $forward = EmailInboxForward::create([
            'workspace_id'      => $workspaceId,
            'inbound_address'   => $toAddress,
            'forwarded_at'      => now(),
            'from_email'        => $fromAddress,
            'subject'           => substr($subject, 0, 500),
            'attachments_count' => count($attachments),
            'status'            => 'processing',
            'raw_email_path'    => $rawPath,
        ]);

        // Process first attachment via OCR if image/pdf
        $extracted = null;
        $expenseId = null;

        if (! empty($attachments)) {
            $first = $attachments[0];
            $contentBase64 = $first['Content'] ?? $first['content'] ?? null;
            $contentType = $first['ContentType'] ?? $first['content-type'] ?? '';
            $name = $first['Name'] ?? $first['filename'] ?? 'attachment';

            if ($contentBase64 && (str_starts_with($contentType, 'image/') || str_contains($contentType, 'pdf'))) {
                try {
                    $ext = pathinfo($name, PATHINFO_EXTENSION) ?: 'jpg';
                    $publicPath = 'receipts/inbox/' . $workspaceId . '/' . Str::random(16) . '.' . $ext;
                    Storage::disk('public')->put($publicPath, base64_decode($contentBase64));

                    $ocr = app(OcrReceipt::class);
                    $extracted = $ocr->extract($publicPath);

                    $expense = Expense::create([
                        'workspace_id' => $workspaceId,
                        'description'  => substr($extracted['merchant_name'] ?? $subject, 0, 500),
                        'amount'       => (float) ($extracted['total'] ?? 0),
                        'currency'     => $extracted['currency'] ?? 'IDR',
                        'expense_date' => $extracted['date'] ?? now()->toDateString(),
                        'category'     => $extracted['category_suggested'] ?? 'Other',
                        'billable'     => false,
                        'receipt_path' => $publicPath,
                        'vendor_name'  => $extracted['merchant_name'] ?? null,
                        'ocr_status'   => 'done',
                        'ocr_extracted' => $extracted,
                        'notes'        => "Auto-imported via email forward from {$fromAddress}",
                    ]);
                    $expenseId = $expense->id;
                } catch (\Throwable $e) {
                    $forward->update([
                        'status'        => 'failed',
                        'error_message' => substr($e->getMessage(), 0, 500),
                    ]);
                    return response()->json(['ok' => false, 'reason' => $e->getMessage()]);
                }
            }
        }

        $forward->update([
            'status'            => $expenseId ? 'converted' : 'manual_review',
            'expense_id'        => $expenseId,
            'ai_extracted_data' => $extracted,
        ]);

        return response()->json(['ok' => true, 'forward_id' => $forward->id, 'expense_id' => $expenseId]);
    }
}

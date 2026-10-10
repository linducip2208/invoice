<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Idempotent quote -> invoice conversion.
 *
 * - Conversion identity: (quote_id, revision). Re-running for the same
 *   revision returns the existing invoice; a new revision converts anew.
 * - Line items keep their PER-LINE tax rates; totals are recomputed
 *   server-side from those lines (never copied blindly).
 * - Never records a payment. Never converts before acceptance evidence.
 */
class QuoteConversionService
{
    /**
     * @return array{invoice?: Invoice, error?: string, duplicate?: bool}
     */
    public function convert(Quote $quote, string $actor): array
    {
        return DB::transaction(function () use ($quote, $actor) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();

            if (! in_array($locked->status, ['accepted', 'converted'], true)) {
                return ['error' => 'quote_not_accepted'];
            }

            // Idempotency: this exact revision already produced an invoice.
            $existing = Invoice::where('quote_id', $locked->id)
                ->where('quote_revision', $locked->revision)
                ->first();
            if ($existing) {
                return ['invoice' => $existing, 'duplicate' => true];
            }
            // Legacy path: accepted before revision tracking — one invoice max.
            if ($locked->converted_invoice_id) {
                $legacy = Invoice::find($locked->converted_invoice_id);
                if ($legacy) {
                    return ['invoice' => $legacy, 'duplicate' => true];
                }
            }

            $workspace = $locked->workspace;
            if (! $workspace) {
                return ['error' => 'no_workspace'];
            }

            $invoiceNo = app(InvoiceService::class)->generateInvoiceNo($workspace);

            $lines = [];
            $subtotal = 0.0;
            $taxTotal = 0.0;
            foreach ($locked->items ?? [] as $i => $item) {
                $qty = (float) ($item['quantity'] ?? 0);
                $price = (float) ($item['unit_price'] ?? 0);
                $rate = (float) ($item['tax_rate'] ?? 0);
                $lineSub = round($qty * $price, 2);
                $lineTax = round($lineSub * $rate / 100, 2);
                $subtotal = round($subtotal + $lineSub, 2);
                $taxTotal = round($taxTotal + $lineTax, 2);
                $lines[] = [
                    'description' => (string) ($item['description'] ?? ''),
                    'quantity' => $qty, 'unit_price' => $price,
                    'tax_rate' => $rate, 'tax_amount' => $lineTax,
                    'subtotal' => $lineSub, 'total' => round($lineSub + $lineTax, 2),
                    'sort_order' => $i,
                ];
            }
            $total = round($subtotal + $taxTotal, 2);

            $invoice = Invoice::create([
                'workspace_id' => $locked->workspace_id,
                'client_id' => $locked->client_id,
                'quote_id' => $locked->id,
                'quote_revision' => $locked->revision,
                'invoice_no' => $invoiceNo,
                'status' => 'draft',
                'template' => $workspace->default_template ?? 'classic',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $subtotal,
                'tax_rate' => $locked->tax_rate,
                'tax_amount' => $taxTotal,
                'discount_type' => $locked->discount_type ?? 'none',
                'discount_value' => $locked->discount_value ?? 0,
                'discount_amount' => $locked->discount_amount ?? 0,
                'total' => $total,
                'amount_paid' => 0,
                'balance_due' => $total,
                'currency' => $locked->currency,
                'notes' => $locked->notes,
                'terms' => $locked->terms,
            ]);

            foreach ($lines as $line) {
                $invoice->items()->create($line);
            }

            $locked->update([
                'status' => 'converted',
                'converted_invoice_id' => $invoice->id,
            ]);

            Log::info('Quote converted', [
                'quote_id' => $locked->id, 'revision' => $locked->revision,
                'invoice_id' => $invoice->id, 'actor' => $actor,
            ]);

            return ['invoice' => $invoice];
        });
    }
}

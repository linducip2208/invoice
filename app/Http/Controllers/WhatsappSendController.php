<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\WhatsappLog;
use App\Services\Whatsapp\WhatsappFactory;
use Illuminate\Http\Request;

class WhatsappSendController extends Controller
{
    public function preview(Invoice $invoice)
    {
        $invoice->load('client', 'workspace');
        $defaultMessage = $this->buildDefaultMessage($invoice);
        return view('invoices.whatsapp-preview', compact('invoice', 'defaultMessage'));
    }

    public function send(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'to_phone' => 'required|string|max:20',
            'message'  => 'required|string|max:4000',
        ]);

        $adapter = WhatsappFactory::resolve($invoice->workspace);
        if (! $adapter) {
            return back()->with('error', 'WhatsApp belum dikonfigurasi. Setup di /workspace/settings.');
        }

        $log = WhatsappLog::create([
            'workspace_id' => $invoice->workspace_id,
            'invoice_id'   => $invoice->id,
            'to_phone'     => $validated['to_phone'],
            'message_body' => $validated['message'],
            'status'       => 'queued',
        ]);

        $result = $adapter->sendText($validated['to_phone'], $validated['message']);

        if ($result['ok']) {
            $log->update([
                'status'         => 'sent',
                'provider_msg_id' => $result['msg_id'],
                'sent_at'        => now(),
            ]);
            return redirect()->route('invoices.show', $invoice)->with('success', '✓ WhatsApp terkirim!');
        }

        $log->update([
            'status'      => 'failed',
            'fail_reason' => substr($result['error'] ?? '', 0, 500),
        ]);
        return back()->with('error', 'WhatsApp gagal dikirim: ' . $result['error']);
    }

    private function buildDefaultMessage(Invoice $invoice): string
    {
        $publicUrl = route('public.invoice.show', $invoice->public_token);
        return "Halo {$invoice->client?->name},\n\n"
            . "Berikut invoice dari *{$invoice->workspace?->name}*:\n\n"
            . "📄 No: {$invoice->invoice_no}\n"
            . "💰 Total: {$invoice->currency} " . number_format($invoice->total, 0) . "\n"
            . "📅 Jatuh tempo: " . $invoice->due_date->format('d M Y') . "\n\n"
            . "Detail & bayar: {$publicUrl}\n\n"
            . "Terima kasih! 🙏";
    }
}

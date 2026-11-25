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
        $templates = $this->templates($invoice);
        $defaultMessage = $templates['tagihan_baru'];
        return view('invoices.whatsapp-preview', compact('invoice', 'defaultMessage', 'templates'));
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

    /**
     * 4 template siap pakai. Semua memakai data invoice asli + link bayar publik.
     * @return array<string,string>
     */
    public function templates(Invoice $invoice): array
    {
        $publicUrl = route('public.invoice.show', $invoice->public_token);
        $total = $invoice->currency . ' ' . number_format($invoice->total, 0);

        return [
            'tagihan_baru' => $this->buildDefaultMessage($invoice),
            'pengingat_h3' => "Halo {$invoice->client?->name},\n\n"
                . "Pengingat: invoice *{$invoice->invoice_no}* ({$total}) jatuh tempo "
                . $invoice->due_date->format('d M Y') . ".\n\n"
                . "Bayar di sini: {$publicUrl}\n\n"
                . "Terima kasih! 🙏",
            'overdue' => "Halo {$invoice->client?->name},\n\n"
                . "Invoice *{$invoice->invoice_no}* ({$total}) sudah melewati jatuh tempo "
                . $invoice->due_date->format('d M Y') . ".\n\n"
                . "Mohon pembayaran via: {$publicUrl}\n"
                . "Balas pesan ini jika sudah bayar / ada kendala. 🙏",
            'kwitansi' => "Halo {$invoice->client?->name},\n\n"
                . "Terima kasih! Pembayaran invoice *{$invoice->invoice_no}* ({$total}) sudah kami terima. ✅\n\n"
                . "Kwitansi: {$publicUrl}\n\n"
                . "Sampai jumpa di project berikutnya! 🙏",
        ];
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

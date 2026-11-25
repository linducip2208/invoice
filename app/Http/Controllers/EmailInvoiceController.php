<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\EmailLog;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailInvoiceController extends Controller
{
    public function send(Request $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $validated = $request->validate([
            'subject'  => 'required|string|max:255',
            'message'  => 'nullable|string|max:5000',
            'to_email' => 'required|email|max:255',
        ]);

        $pdfPath = $invoiceService->generatePdf($invoice);

        Mail::to($validated['to_email'])->queue(new InvoiceMail(
            $invoice,
            $validated['subject'],
            $validated['message'] ?? '',
            $pdfPath,
        ));

        EmailLog::create([
            'invoice_id' => $invoice->id,
            'to_email'   => $validated['to_email'],
            'subject'    => $validated['subject'],
            'body'       => $validated['message'] ?? '',
            'status'     => 'queued',
            'sent_at'    => now(),
        ]);

        $invoice->update([
            'last_sent_at' => now(),
            'status'       => $invoice->status === 'draft' ? 'sent' : $invoice->status,
        ]);

        return redirect()->back()->with('success', 'Invoice sent successfully to ' . $validated['to_email']);
    }

    /**
     * Antrekan kirim terjadwal — diproses command per jam.
     */
    public function schedule(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->workspace_id === workspace()?->id, 403);

        $validated = $request->validate([
            'subject'  => 'required|string|max:255',
            'message'  => 'nullable|string|max:5000',
            'to_email' => 'required|email|max:255',
            'send_at'  => 'required|date|after:now',
        ]);

        if (in_array($invoice->status, ['paid', 'cancelled'], true)) {
            return back()->with('error', __('Invoice lunas/batal tidak bisa dijadwalkan.'));
        }

        \App\Models\ScheduledInvoiceSend::create([
            'workspace_id' => $invoice->workspace_id,
            'invoice_id'   => $invoice->id,
            'to_email'     => $validated['to_email'],
            'subject'      => $validated['subject'],
            'message'      => $validated['message'] ?? null,
            'send_at'      => $validated['send_at'],
            'status'       => 'pending',
        ]);

        return back()->with('success', __('Pengiriman dijadwalkan.'));
    }

    public function cancelSchedule(\App\Models\ScheduledInvoiceSend $scheduled): RedirectResponse
    {
        abort_unless($scheduled->workspace_id === workspace()?->id, 403);
        if ($scheduled->status === 'pending') {
            $scheduled->update(['status' => 'cancelled']);
        }

        return back()->with('success', __('Jadwal dibatalkan.'));
    }

    /**
     * Kirim massal (maks 10/request): antre email + PDF per invoice,
     * draft ikut berubah jadi sent. Tanpa email klien = dilewati.
     */
    public function bulkSend(Request $request, InvoiceService $invoiceService)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:10',
            'ids.*' => 'integer',
        ]);

        $sent = 0;
        $skipped = 0;
        $invoices = Invoice::with('client')->whereIn('id', $validated['ids'])->get();
        foreach ($invoices as $invoice) {
            if (! in_array($invoice->status, ['draft', 'sent', 'partial', 'overdue'], true)) {
                $skipped++;
                continue;
            }
            $to = $invoice->client?->email;
            if (! $to) {
                $skipped++;
                continue;
            }
            $pdfPath = $invoiceService->generatePdf($invoice);
            Mail::to($to)->queue(new InvoiceMail(
                $invoice,
                __('Invoice :no from :ws', ['no' => $invoice->invoice_no, 'ws' => $invoice->workspace?->name ?? config('app.name')]),
                '',
                $pdfPath,
            ));
            EmailLog::create([
                'invoice_id' => $invoice->id,
                'to_email' => $to,
                'subject' => $invoice->invoice_no,
                'body' => '',
                'status' => 'queued',
                'sent_at' => now(),
            ]);
            $invoice->update([
                'last_sent_at' => now(),
                'status' => $invoice->status === 'draft' ? 'sent' : $invoice->status,
            ]);
            $sent++;
        }

        return back()->with('success', __(':s invoice(s) queued, :k skipped (no email/closed).', ['s' => $sent, 'k' => $skipped]));
    }
}

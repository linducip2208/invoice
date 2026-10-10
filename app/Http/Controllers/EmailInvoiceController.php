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
}

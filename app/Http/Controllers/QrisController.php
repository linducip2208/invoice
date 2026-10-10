<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Models\QrisCode;
use App\Services\Qris\QrisGenerator;
use Illuminate\Http\Request;

class QrisController extends Controller
{
    public function generate(Request $request, Invoice $invoice, QrisGenerator $generator)
    {
        $request->validate(['provider_id' => 'nullable|exists:payment_gateways,id']);

        $provider = $request->filled('provider_id')
            ? PaymentGateway::find($request->provider_id)
            : null;

        $qris = $generator->generate($invoice, $provider);

        return back()->with('success', 'QRIS dibuat. Berlaku sampai ' . $qris->expires_at->format('H:i'));
    }

    public function show(Invoice $invoice, QrisCode $qris)
    {
        abort_if($qris->invoice_id !== $invoice->id, 404);
        return view('invoices.qris', compact('invoice', 'qris'));
    }

    public function cancel(QrisCode $qris)
    {
        $qris->update(['status' => 'cancelled']);
        return back()->with('success', 'QRIS dibatalkan.');
    }
}

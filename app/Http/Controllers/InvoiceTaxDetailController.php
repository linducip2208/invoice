<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceTaxDetail;
use Illuminate\Http\Request;

class InvoiceTaxDetailController extends Controller
{
    public function edit(Invoice $invoice)
    {
        $detail = $invoice->taxDetail ?? new InvoiceTaxDetail([
            'invoice_id'   => $invoice->id,
            'workspace_id' => $invoice->workspace_id,
            'ppn_rate'     => 11,
            'ppn_amount'   => 0,
            'pph23_rate'   => $invoice->client?->default_pph23_rate ?? 0,
            'pph23_amount' => 0,
            'efaktur_status' => 'not_applicable',
        ]);

        return view('invoices.tax-details', compact('invoice', 'detail'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'ppn_rate'          => 'nullable|numeric|min:0|max:100',
            'pph23_rate'        => 'nullable|numeric|min:0|max:30',
            'pph23_object_code' => 'nullable|string|max:20',
            'efaktur_status'    => 'required|in:not_applicable,draft,approved,rejected',
            'efaktur_no'        => 'nullable|string|max:50',
        ]);

        $subtotal = (float) $invoice->subtotal;
        $ppnRate = (float) ($validated['ppn_rate'] ?? 0);
        $pph23Rate = (float) ($validated['pph23_rate'] ?? 0);

        $ppnAmount = round($subtotal * $ppnRate / 100, 2);
        $pph23Amount = round($subtotal * $pph23Rate / 100, 2);

        InvoiceTaxDetail::updateOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'workspace_id'      => $invoice->workspace_id,
                'ppn_rate'          => $ppnRate,
                'ppn_amount'        => $ppnAmount,
                'pph23_rate'        => $pph23Rate,
                'pph23_amount'      => $pph23Amount,
                'pph23_object_code' => $validated['pph23_object_code'] ?? null,
                'efaktur_status'    => $validated['efaktur_status'],
                'efaktur_no'        => $validated['efaktur_no'] ?? null,
            ]
        );

        // Mirror into invoice columns for quick access
        $invoice->update([
            'pph23_rate'   => $pph23Rate,
            'pph23_amount' => $pph23Amount,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Detail pajak disimpan.');
    }
}

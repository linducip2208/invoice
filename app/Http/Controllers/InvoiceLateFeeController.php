<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceLateFee;
use App\Services\LateFee\LateFeeApplier;
use Illuminate\Http\Request;

class InvoiceLateFeeController extends Controller
{
    public function __construct(private LateFeeApplier $applier) {}

    public function apply(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'type'   => 'nullable|in:percent,fixed',
            'value'  => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:200',
        ]);

        // Optional override values for one-shot manual apply
        if ($request->filled('value')) {
            $invoice->late_fee_type = $request->input('type', 'fixed');
            $invoice->late_fee_value = $request->input('value');
            $invoice->late_fee_policy = 'one_time';
        }

        $fee = $this->applier->apply($invoice);

        if (! $fee) {
            return back()->with('error', 'Late fee tidak diapply (cek kebijakan invoice).');
        }

        return back()->with('success', "Late fee {$invoice->currency} " . number_format($fee->amount, 0) . ' diterapkan.');
    }

    public function destroy(InvoiceLateFee $fee)
    {
        $invoiceNo = $fee->invoice?->invoice_no;
        $this->applier->reverse($fee);

        return back()->with('success', "Late fee untuk {$invoiceNo} dihapus.");
    }
}

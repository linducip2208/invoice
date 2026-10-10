<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileQuickController extends Controller
{
    public function quickInvoice()
    {
        $clients = Client::orderBy('name')->get(['id', 'name']);
        return view('mobile.quick-invoice', compact('clients'));
    }

    public function storeQuickInvoice(Request $request)
    {
        $validated = $request->validate([
            'client_id'    => 'required|exists:clients,id',
            'description'  => 'required|string|max:500',
            'amount'       => 'required|numeric|min:0.01',
            'due_in_days'  => 'required|integer|min:1|max:365',
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $workspace = workspace();
            $invoice = Invoice::create([
                'workspace_id' => $workspace->id,
                'client_id'    => $validated['client_id'],
                'invoice_no'   => ($workspace->invoice_prefix ?? 'INV-') . str_pad((string) ($workspace->next_invoice_no ?? 1), 3, '0', STR_PAD_LEFT),
                'status'       => 'draft',
                'template'     => $workspace->default_template ?? 'classic',
                'issue_date'   => now()->toDateString(),
                'due_date'     => now()->addDays($validated['due_in_days'])->toDateString(),
                'subtotal'     => $validated['amount'],
                'tax_rate'     => 0,
                'tax_amount'   => 0,
                'discount_type' => 'none',
                'discount_value' => 0,
                'discount_amount' => 0,
                'total'        => $validated['amount'],
                'amount_paid'  => 0,
                'balance_due'  => $validated['amount'],
                'currency'     => $workspace->currency ?? 'IDR',
            ]);
            $invoice->items()->create([
                'description' => $validated['description'],
                'quantity'    => 1,
                'unit_price'  => $validated['amount'],
                'tax_rate'    => 0,
                'tax_amount'  => 0,
                'subtotal'    => $validated['amount'],
                'total'       => $validated['amount'],
                'sort_order'  => 0,
            ]);
            $workspace->increment('next_invoice_no');
            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', '✓ Invoice cepat dibuat!');
    }

    public function quickPayment()
    {
        $invoices = Invoice::with('client')
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->orderBy('issue_date', 'desc')
            ->limit(50)
            ->get();
        return view('mobile.quick-payment', compact('invoices'));
    }

    public function storeQuickPayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount'     => 'required|numeric|min:0.01',
            'method'     => 'required|string|max:50',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        Payment::create([
            'invoice_id'     => $invoice->id,
            'amount'         => $validated['amount'],
            'currency'       => $invoice->currency,
            'payment_date'   => now()->toDateString(),
            'payment_method' => $validated['method'],
            'reference'      => 'Quick mobile capture',
        ]);

        $newPaid = (float) $invoice->amount_paid + (float) $validated['amount'];
        $newBalance = max(0, (float) $invoice->total - $newPaid);
        $invoice->update([
            'amount_paid' => $newPaid,
            'balance_due' => $newBalance,
            'status'      => $newBalance <= 0 ? 'paid' : ($newPaid > 0 ? 'partial' : $invoice->status),
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', '✓ Payment dicatat!');
    }
}

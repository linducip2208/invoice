<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditNote::with('invoice.client', 'creator')->latest('issued_at');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('invoice_id')) $query->where('invoice_id', $request->invoice_id);

        $creditNotes = $query->paginate(20)->withQueryString();

        $stats = [
            'total_issued' => CreditNote::where('status', 'issued')->sum('amount'),
            'total_applied' => CreditNote::where('status', 'applied')->sum('amount'),
            'count_pending' => CreditNote::where('status', 'draft')->count(),
        ];

        return view('credit-notes.index', compact('creditNotes', 'stats'));
    }

    public function create(Request $request)
    {
        $invoiceId = $request->input('invoice_id');
        $invoice = $invoiceId ? Invoice::with('client')->find($invoiceId) : null;
        $invoices = Invoice::with('client')
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->orderBy('issue_date', 'desc')
            ->limit(100)
            ->get();

        return view('credit-notes.create', compact('invoice', 'invoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'reason'     => 'required|string|max:300',
            'amount'     => 'required|numeric|min:0.01',
            'currency'   => 'required|string|size:3',
            'issued_at'  => 'required|date',
            'notes'      => 'nullable|string|max:1000',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        if ($validated['amount'] > $invoice->balance_due) {
            return back()->withErrors(['amount' => 'Jumlah tidak boleh melebihi sisa tagihan invoice.'])->withInput();
        }

        $cn = CreditNote::create([
            'workspace_id' => $invoice->workspace_id,
            'invoice_id'   => $invoice->id,
            'credit_no'    => CreditNote::nextNumber($invoice->workspace_id),
            'reason'       => $validated['reason'],
            'amount'       => $validated['amount'],
            'currency'     => $validated['currency'],
            'issued_at'    => $validated['issued_at'],
            'status'       => 'draft',
            'notes'        => $validated['notes'] ?? null,
            'created_by'   => auth()->id(),
        ]);

        return redirect()->route('credit-notes.show', $cn)->with('success', 'Credit note dibuat sebagai draft.');
    }

    public function show(CreditNote $creditNote)
    {
        $creditNote->load('invoice.client', 'creator');
        return view('credit-notes.show', ['cn' => $creditNote]);
    }

    public function issue(CreditNote $creditNote)
    {
        if ($creditNote->status !== 'draft') {
            return back()->with('error', 'Hanya draft yang bisa diissue.');
        }

        $creditNote->update(['status' => 'issued']);
        return back()->with('success', "Credit note {$creditNote->credit_no} di-issue.");
    }

    public function apply(CreditNote $creditNote)
    {
        if ($creditNote->status !== 'issued') {
            return back()->with('error', 'Credit note harus issued dulu sebelum diapply.');
        }

        DB::transaction(function () use ($creditNote) {
            $invoice = $creditNote->invoice;
            $newBalance = max(0, (float) $invoice->balance_due - (float) $creditNote->amount);
            $newAmountPaid = (float) ($invoice->amount_paid ?? 0); // Credit note is not a payment
            $invoice->balance_due = $newBalance;

            // Auto-update status if balance reaches 0
            if ($newBalance <= 0 && $invoice->status !== 'paid') {
                $invoice->status = 'paid';
            }

            $invoice->save();
            $creditNote->update(['status' => 'applied']);
        });

        return back()->with('success', "Credit note {$creditNote->credit_no} diterapkan ke invoice.");
    }

    public function cancel(CreditNote $creditNote)
    {
        if ($creditNote->status === 'cancelled') {
            return back()->with('error', 'Sudah cancelled.');
        }

        DB::transaction(function () use ($creditNote) {
            // If was applied, reverse the balance reduction
            if ($creditNote->status === 'applied') {
                $invoice = $creditNote->invoice;
                $invoice->balance_due = (float) $invoice->balance_due + (float) $creditNote->amount;
                if ($invoice->status === 'paid' && $invoice->balance_due > 0) {
                    $invoice->status = (float) $invoice->amount_paid > 0 ? 'partial' : 'sent';
                }
                $invoice->save();
            }
            $creditNote->update(['status' => 'cancelled']);
        });

        return back()->with('success', "Credit note {$creditNote->credit_no} dibatalkan.");
    }

    public function destroy(CreditNote $creditNote)
    {
        if ($creditNote->status !== 'draft') {
            return back()->with('error', 'Hanya draft yang bisa dihapus. Untuk yang lain, gunakan Cancel.');
        }

        $no = $creditNote->credit_no;
        $creditNote->delete();
        return redirect()->route('credit-notes.index')->with('success', "Draft {$no} dihapus.");
    }
}

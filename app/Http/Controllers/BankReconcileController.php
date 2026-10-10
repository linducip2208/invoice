<?php

namespace App\Http\Controllers;

use App\Models\BankReconcileBatch;
use App\Models\BankReconcileLine;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Bank\Matcher;
use App\Services\Bank\StatementParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankReconcileController extends Controller
{
    public function index()
    {
        $batches = BankReconcileBatch::with('importer')->latest()->paginate(15);
        return view('bank-reconcile.index', compact('batches'));
    }

    public function create()
    {
        return view('bank-reconcile.create');
    }

    public function store(Request $request, StatementParser $parser, Matcher $matcher)
    {
        $validated = $request->validate([
            'bank_name'  => 'required|string|max:50',
            'account_no' => 'required|string|max:50',
            'file'       => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $request->file('file')->store('bank-reconcile', 'public');
        $absolute = Storage::disk('public')->path($path);

        $rows = $parser->parseCsv($absolute);

        if (empty($rows)) {
            Storage::disk('public')->delete($path);
            return back()->with('error', 'Tidak ada baris transaksi terbaca. Cek format CSV.');
        }

        $batch = DB::transaction(function () use ($validated, $path, $rows, $matcher) {
            $batch = BankReconcileBatch::create([
                'bank_name'   => $validated['bank_name'],
                'account_no'  => $validated['account_no'],
                'file_path'   => $path,
                'file_format' => 'csv',
                'imported_by' => auth()->id(),
                'total_lines' => count($rows),
                'status'      => 'review',
            ]);

            foreach ($rows as $row) {
                $line = $batch->lines()->create([
                    'workspace_id' => $batch->workspace_id,
                    'trx_date'     => $row['trx_date'],
                    'amount'       => $row['amount'],
                    'description'  => substr($row['description'], 0, 500),
                ]);
                $matcher->autoMatch($line);
            }

            $batch->update([
                'matched_count'   => $batch->lines()->whereNotNull('matched_invoice_id')->count(),
                'unmatched_count' => $batch->lines()->whereNull('matched_invoice_id')->count(),
            ]);

            return $batch;
        });

        return redirect()->route('bank-reconcile.show', $batch)->with('success', "✓ {$batch->total_lines} baris terimport, {$batch->matched_count} auto-matched.");
    }

    public function show(BankReconcileBatch $batch)
    {
        $batch->load('lines.matchedInvoice.client');
        $candidates = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->with('client')
            ->latest('issue_date')
            ->limit(50)
            ->get();
        return view('bank-reconcile.show', compact('batch', 'candidates'));
    }

    public function match(Request $request, BankReconcileBatch $batch)
    {
        $request->validate([
            'matches' => 'required|array',
            'matches.*' => 'nullable|integer|exists:invoices,id',
        ]);

        foreach ($request->matches as $lineId => $invoiceId) {
            BankReconcileLine::where('id', $lineId)
                ->where('batch_id', $batch->id)
                ->update([
                    'matched_invoice_id' => $invoiceId ?: null,
                    'match_confidence'   => $invoiceId ? 'manual' : 'none',
                    'match_method'       => $invoiceId ? 'manual' : 'none',
                ]);
        }

        $batch->update([
            'matched_count'   => $batch->lines()->whereNotNull('matched_invoice_id')->count(),
            'unmatched_count' => $batch->lines()->whereNull('matched_invoice_id')->count(),
        ]);

        return back()->with('success', 'Match diupdate.');
    }

    public function finalize(BankReconcileBatch $batch)
    {
        $created = 0;
        DB::transaction(function () use ($batch, &$created) {
            $matched = $batch->lines()->whereNotNull('matched_invoice_id')->whereNull('matched_payment_id')->get();
            foreach ($matched as $line) {
                $payment = Payment::create([
                    'workspace_id'   => $line->workspace_id,
                    'invoice_id'     => $line->matched_invoice_id,
                    'amount'         => $line->amount,
                    'currency'       => 'IDR',
                    'payment_date'   => $line->trx_date,
                    'payment_method' => 'bank_transfer',
                    'reference'      => substr($line->description, 0, 200),
                    'notes'          => "Auto-matched from {$batch->bank_name}",
                    'bank_reconcile_line_id' => $line->id,
                ]);
                $line->update(['matched_payment_id' => $payment->id]);

                // Update invoice balance
                $inv = Invoice::find($line->matched_invoice_id);
                if ($inv) {
                    $newPaid = (float) $inv->amount_paid + (float) $line->amount;
                    $newBalance = max(0, (float) $inv->total - $newPaid);
                    $inv->update([
                        'amount_paid' => $newPaid,
                        'balance_due' => $newBalance,
                        'status'      => $newBalance <= 0 ? 'paid' : ($newPaid > 0 ? 'partial' : $inv->status),
                    ]);
                }
                $created++;
            }
            $batch->update(['status' => 'completed']);
        });

        return back()->with('success', "✓ {$created} payment dibuat dari batch ini.");
    }
}

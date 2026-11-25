<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\TimeEntry;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeEntry::with('client')->latest('entry_date');
        if ($request->filled('client_id')) $query->where('client_id', $request->client_id);
        if ($request->filled('billable'))  $query->where('billable', $request->billable === 'yes');

        $entries = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('name')->get();
        $totalHours = (clone $query)->sum('hours');
        $totalAmount = TimeEntry::query()->selectRaw('SUM(hours * hourly_rate) as t')->value('t') ?? 0;

        return view('time-entries.index', compact('entries', 'clients', 'totalHours', 'totalAmount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'   => 'nullable|exists:clients,id',
            'description' => 'required|string|max:500',
            'hours'       => 'required|numeric|min:0.01|max:24',
            'hourly_rate' => 'required|numeric|min:0',
            'entry_date'  => 'required|date',
            'billable'    => 'boolean',
        ]);
        $validated['user_id'] = auth()->id();
        $validated['billable'] = ! empty($validated['billable']);

        TimeEntry::create($validated);
        return back()->with('success', 'Time entry created.');
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();
        return back()->with('success', 'Entry deleted.');
    }

    public function timerStart()
    {
        if (session()->has('timer_started_at')) {
            return back()->with('error', __('Timer already running.'));
        }
        session(['timer_started_at' => now()->toDateTimeString(), 'timer_note' => request('note')]);
        return back()->with('success', __('Timer started.'));
    }

    public function timerStop()
    {
        $started = session('timer_started_at');
        if (! $started) {
            return back()->with('error', __('No timer running.'));
        }
        $hours = round(now()->diffInMinutes(\Carbon\Carbon::parse($started)) / 60, 2);
        $hours = max($hours, 0.25);
        $note = session('timer_note') ?? request('note') ?? '';
        session()->forget(['timer_started_at', 'timer_note']);

        return redirect()->route('time-entries.index', [
            'prefill_hours' => $hours,
            'prefill_desc' => $note,
        ])->with('success', __('Timer stopped: :h h. Review & save below.', ['h' => $hours]));
    }

    /**
     * Buat draft invoice dari time entries terpilih.
     * Semua entry harus: billable, belum ditagih, dan milik SATU klien.
     */
    public function invoiceFromEntries(Request $request, InvoiceService $service)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $workspace = workspace();
        abort_unless($workspace, 403);

        $entries = TimeEntry::whereIn('id', $validated['ids'])->get();
        if ($entries->isEmpty()) {
            return back()->with('error', __('No entries selected.'));
        }
        if ($entries->contains(fn ($e) => ! $e->billable || $e->invoiced)) {
            return back()->with('error', __('Only unbilled, billable entries can be invoiced.'));
        }
        $clientId = $entries->first()->client_id;
        if (! $clientId || $entries->contains(fn ($e) => $e->client_id !== $clientId)) {
            return back()->with('error', __('Select entries from a single client with a client set.'));
        }

        $items = $entries->map(fn ($e) => [
            'description' => $e->entry_date?->format('d M Y') . ' — ' . $e->description . " ({$e->hours}h)",
            'quantity' => (float) $e->hours,
            'unit_price' => (float) $e->hourly_rate,
            'tax_rate' => 0,
        ])->all();

        $totals = InvoiceService::calculateTotals($items, 0, 'none', 0);

        $invoice = DB::transaction(function () use ($entries, $items, $totals, $clientId, $workspace, $service) {
            $inv = Invoice::create([
                'workspace_id' => $workspace->id,
                'client_id' => $clientId,
                'invoice_no' => $service->generateInvoiceNo($workspace),
                'status' => 'draft',
                'template' => $workspace->default_template ?? 'classic',
                'currency' => $workspace->currency ?? 'IDR',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'tax_rate' => 0,
                'tax_amount' => $totals['tax_amount'],
                'discount_type' => 'none',
                'discount_value' => 0,
                'discount_amount' => $totals['discount_amount'],
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total'],
                'amount_paid' => 0,
                'balance_due' => $totals['total'],
                'notes' => __('Generated from :n time entries.', ['n' => $entries->count()]),
            ]);

            foreach ($items as $i => $item) {
                $lineSub = round($item['quantity'] * $item['unit_price'], 2);
                $inv->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => 0,
                    'tax_amount' => 0,
                    'subtotal' => $lineSub,
                    'total' => $lineSub,
                    'sort_order' => $i,
                ]);
            }

            TimeEntry::whereIn('id', $entries->pluck('id'))
                ->update(['invoiced' => true, 'invoice_id' => $inv->id]);

            return $inv;
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', __('Draft invoice :no created from :n time entries.', ['no' => $invoice->invoice_no, 'n' => $entries->count()]));
    }
}

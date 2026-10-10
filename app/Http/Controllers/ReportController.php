<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->input('period', '30');
        $endDate   = Carbon::now()->endOfDay();
        $startDate = match ($period) {
            '7'   => Carbon::now()->subDays(7)->startOfDay(),
            '90'  => Carbon::now()->subDays(90)->startOfDay(),
            '365' => Carbon::now()->subYear()->startOfDay(),
            'ytd' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        $today = Carbon::now()->toDateString();

        $invStats = Invoice::query()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN issue_date BETWEEN ? AND ? THEN total END),0) AS invoiced,
                COALESCE(SUM(CASE WHEN status IN ('sent','partial','overdue') THEN balance_due END),0) AS outstanding,
                COALESCE(SUM(CASE WHEN status = 'overdue' OR (status IN ('sent','partial') AND due_date < ?) THEN balance_due END),0) AS overdue,
                COALESCE(SUM(CASE WHEN issue_date BETWEEN ? AND ? THEN 1 END),0) AS invoice_count
            ", [$startDate, $endDate, $today, $startDate, $endDate])
            ->first();

        $payStats = Payment::query()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('COALESCE(SUM(amount),0) AS collected, COUNT(*) AS payment_count')
            ->first();

        $totals = [
            'invoiced'     => (float) $invStats->invoiced,
            'collected'    => (float) $payStats->collected,
            'outstanding'  => (float) $invStats->outstanding,
            'overdue'      => (float) $invStats->overdue,
            'invoiceCount' => (int) $invStats->invoice_count,
            'paymentCount' => (int) $payStats->payment_count,
        ];

        $statusBreakdown = Invoice::selectRaw('status, COUNT(*) as count, SUM(total) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $topClients = Client::withSum(['invoices as revenue' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('issue_date', [$startDate, $endDate]);
            }], 'total')
            ->withCount(['invoices as invoice_count' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('issue_date', [$startDate, $endDate]);
            }])
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        // Group monthly in PHP for cross-DB compatibility
        $monthsStart = Carbon::now()->subMonths(11)->startOfMonth();
        $invoicesByMonth = Invoice::where('issue_date', '>=', $monthsStart)
            ->orderBy('issue_date')
            ->get(['issue_date', 'total']);

        $monthly = collect();
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->startOfMonth();
            $key = $m->format('Y-m');
            $bucket = $invoicesByMonth->filter(fn ($inv) => $inv->issue_date->format('Y-m') === $key);
            $monthly->push((object) [
                'month' => $key,
                'total' => $bucket->sum('total'),
                'count' => $bucket->count(),
            ]);
        }

        $methodBreakdown = Payment::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        return view('reports.index', compact(
            'period', 'startDate', 'endDate',
            'totals', 'statusBreakdown', 'topClients',
            'monthly', 'methodBreakdown'
        ));
    }

    /**
     * Aging report — bucket invoice yang belum lunas berdasarkan umur (hari sejak jatuh tempo).
     */
    public function aging(Request $request): View
    {
        $asOf = $request->filled('as_of') ? Carbon::parse($request->as_of) : Carbon::now();
        $currency = $request->input('currency');

        $unpaid = Invoice::query()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->when($currency, fn ($q) => $q->where('currency', $currency))
            ->with('client:id,name,company_name,email')
            ->orderBy('due_date')
            ->get();

        $buckets = [
            'current'   => ['label' => 'Belum jatuh tempo', 'min' => null, 'max' => 0,   'invoices' => collect(), 'total' => 0],
            '1_30'      => ['label' => '1-30 hari',         'min' => 1,    'max' => 30,  'invoices' => collect(), 'total' => 0],
            '31_60'     => ['label' => '31-60 hari',        'min' => 31,   'max' => 60,  'invoices' => collect(), 'total' => 0],
            '61_90'     => ['label' => '61-90 hari',        'min' => 61,   'max' => 90,  'invoices' => collect(), 'total' => 0],
            'over_90'   => ['label' => '90+ hari',          'min' => 91,   'max' => null,'invoices' => collect(), 'total' => 0],
        ];

        foreach ($unpaid as $inv) {
            $daysOverdue = $asOf->diffInDays($inv->due_date, false) * -1; // positive if overdue
            $key = match (true) {
                $daysOverdue <= 0 => 'current',
                $daysOverdue <= 30 => '1_30',
                $daysOverdue <= 60 => '31_60',
                $daysOverdue <= 90 => '61_90',
                default => 'over_90',
            };
            $inv->days_overdue = max($daysOverdue, 0);
            $buckets[$key]['invoices']->push($inv);
            $buckets[$key]['total'] += (float) $inv->balance_due;
        }

        // Per-client breakdown
        $byClient = $unpaid->groupBy('client_id')->map(function ($group) use ($asOf) {
            $client = $group->first()->client;
            $perBucket = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, 'over_90' => 0];
            $totalUnpaid = 0;
            foreach ($group as $inv) {
                $daysOverdue = $asOf->diffInDays($inv->due_date, false) * -1;
                $key = match (true) {
                    $daysOverdue <= 0 => 'current',
                    $daysOverdue <= 30 => '1_30',
                    $daysOverdue <= 60 => '31_60',
                    $daysOverdue <= 90 => '61_90',
                    default => 'over_90',
                };
                $perBucket[$key] += (float) $inv->balance_due;
                $totalUnpaid += (float) $inv->balance_due;
            }
            return (object) [
                'client'        => $client,
                'buckets'       => $perBucket,
                'total'         => $totalUnpaid,
                'invoice_count' => $group->count(),
            ];
        })->sortByDesc('total')->values();

        $grandTotal = collect($buckets)->sum('total');
        $currencies = Invoice::query()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->distinct()
            ->pluck('currency');

        if ($request->wantsJson() || $request->has('format') && $request->format === 'csv') {
            return $this->agingCsv($buckets, $byClient, $asOf);
        }

        return view('reports.aging', compact('buckets', 'byClient', 'grandTotal', 'asOf', 'currencies', 'currency'));
    }

    private function agingCsv($buckets, $byClient, $asOf): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'aging-' . $asOf->format('Y-m-d') . '.csv';
        return response()->streamDownload(function () use ($byClient, $buckets) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Klien', 'Email', 'Belum Jatuh Tempo', '1-30', '31-60', '61-90', '90+', 'Total']);
            foreach ($byClient as $row) {
                fputcsv($h, [
                    $row->client->name,
                    $row->client->email,
                    $row->buckets['current'],
                    $row->buckets['1_30'],
                    $row->buckets['31_60'],
                    $row->buckets['61_90'],
                    $row->buckets['over_90'],
                    $row->total,
                ]);
            }
            fputcsv($h, ['TOTAL', '', $buckets['current']['total'], $buckets['1_30']['total'], $buckets['31_60']['total'], $buckets['61_90']['total'], $buckets['over_90']['total'], collect($buckets)->sum('total')]);
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Cashflow forecast — projected inflow & outflow next N days.
     */
    public function cashflow(Request $request): View
    {
        $days = (int) $request->input('days', 90);
        $days = in_array($days, [30, 60, 90, 180]) ? $days : 90;
        $scenario = $request->input('scenario', 'realistic');

        $today = Carbon::now()->startOfDay();
        $endDate = $today->copy()->addDays($days);

        // Inflow: open invoices (sent/partial) with due_date in window
        $openInvoices = Invoice::query()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->where('balance_due', '>', 0)
            ->with('client:id,name,payment_reliability,avg_payment_days')
            ->get();

        // Group by week
        $weeks = collect();
        for ($i = 0; $i < ceil($days / 7); $i++) {
            $weekStart = $today->copy()->addDays($i * 7);
            $weekEnd = $weekStart->copy()->addDays(6);
            $weeks->push((object) [
                'label'      => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M'),
                'start'      => $weekStart,
                'end'        => $weekEnd,
                'inflow'     => 0,
                'invoices'   => collect(),
            ]);
        }

        foreach ($openInvoices as $inv) {
            // Adjust expected payment based on scenario & client reliability
            $adjustedDate = $inv->due_date->copy();
            if ($scenario === 'realistic' && $inv->client?->avg_payment_days !== null) {
                $adjustedDate = $inv->issue_date->copy()->addDays($inv->client->avg_payment_days);
            } elseif ($scenario === 'pessimistic') {
                $adjustedDate->addDays(14);
            }

            if ($adjustedDate->lt($today)) $adjustedDate = $today->copy()->addDay();
            if ($adjustedDate->gt($endDate)) continue;

            foreach ($weeks as $w) {
                if ($adjustedDate->between($w->start, $w->end)) {
                    $w->inflow += (float) $inv->balance_due;
                    $w->invoices->push($inv);
                    break;
                }
            }
        }

        // Recurring invoices that will generate
        $recurring = \App\Models\RecurringInvoice::where('status', 'active')
            ->whereNotNull('next_run_date')
            ->where('next_run_date', '<=', $endDate)
            ->get();

        foreach ($recurring as $rec) {
            $runDate = Carbon::parse($rec->next_run_date);
            foreach ($weeks as $w) {
                if ($runDate->between($w->start, $w->end)) {
                    $w->inflow += (float) ($rec->total ?? 0);
                    break;
                }
            }
        }

        $totalProjected = $weeks->sum('inflow');

        return view('reports.cashflow', compact('weeks', 'totalProjected', 'days', 'scenario'));
    }

    /**
     * P&L per Klien — revenue minus expense minus time cost.
     */
    public function pl(Request $request): View
    {
        $period = $request->input('period', '90');
        $startDate = match ($period) {
            '30'  => Carbon::now()->subDays(30),
            '60'  => Carbon::now()->subDays(60),
            '180' => Carbon::now()->subDays(180),
            '365' => Carbon::now()->subYear(),
            'ytd' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subDays(90),
        };

        $clients = Client::with(['invoices' => function ($q) use ($startDate) {
                $q->where('issue_date', '>=', $startDate);
            }])
            ->get();

        $rows = $clients->map(function ($client) use ($startDate) {
            $revenue = (float) $client->invoices->sum('amount_paid');
            $expense = (float) \App\Models\Expense::where('client_id', $client->id)
                ->where('expense_date', '>=', $startDate)
                ->sum('amount');
            $timeCost = (float) \App\Models\TimeEntry::where('client_id', $client->id)
                ->where('entry_date', '>=', $startDate)
                ->selectRaw('SUM(hours * hourly_rate) as t')
                ->value('t') ?? 0;
            $netCost = $expense + $timeCost;
            $profit = $revenue - $netCost;
            $marginPct = $revenue > 0 ? round($profit / $revenue * 100, 1) : null;

            return (object) [
                'client'        => $client,
                'revenue'       => $revenue,
                'expense'       => $expense,
                'time_cost'     => $timeCost,
                'profit'        => $profit,
                'margin_pct'    => $marginPct,
                'invoice_count' => $client->invoices->count(),
            ];
        })->filter(fn ($r) => $r->revenue > 0 || $r->expense > 0 || $r->time_cost > 0)
          ->sortByDesc('profit')
          ->values();

        $totals = (object) [
            'revenue'   => $rows->sum('revenue'),
            'expense'   => $rows->sum('expense'),
            'time_cost' => $rows->sum('time_cost'),
            'profit'    => $rows->sum('profit'),
        ];
        $totals->margin_pct = $totals->revenue > 0
            ? round($totals->profit / $totals->revenue * 100, 1)
            : null;

        return view('reports.pl', compact('rows', 'totals', 'period', 'startDate'));
    }

    /**
     * Tax Summary Report — PPN keluaran & PPh23 dipotong klien.
     */
    public function tax(Request $request): View
    {
        $type = $request->input('type', 'ppn');
        $period = $request->input('period', now()->format('Y-m'));
        $start = Carbon::parse($period . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $taxDetails = \App\Models\InvoiceTaxDetail::query()
            ->whereHas('invoice', function ($q) use ($start, $end) {
                $q->whereBetween('issue_date', [$start, $end])
                  ->whereIn('status', ['sent','partial','paid','overdue']);
            })
            ->with(['invoice.client'])
            ->get();

        $totalPpn = (float) $taxDetails->sum('ppn_amount');
        $totalPph23 = (float) $taxDetails->sum('pph23_amount');
        $efakturStats = $taxDetails->groupBy('efaktur_status')->map->count();

        return view('reports.tax', compact(
            'taxDetails', 'totalPpn', 'totalPph23', 'efakturStats', 'type', 'period', 'start', 'end'
        ));
    }
}

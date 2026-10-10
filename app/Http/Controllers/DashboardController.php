<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Quote;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $workspace = workspace();

        $stats = Invoice::query()
            ->selectRaw("
                SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) AS total_revenue,
                SUM(CASE WHEN status IN ('sent','partial','overdue') THEN balance_due ELSE 0 END) AS outstanding_amount,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS paid_count,
                SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) AS overdue_count
            ")
            ->first();

        $totalRevenue = (float) ($stats->total_revenue ?? 0);
        $outstandingAmount = (float) ($stats->outstanding_amount ?? 0);
        $paidCount = (int) ($stats->paid_count ?? 0);
        $overdueCount = (int) ($stats->overdue_count ?? 0);
        $clientCount = Client::count();

        $recentInvoices = Invoice::with('client:id,name')->latest()->take(5)->get();

        // 8-week collection trend: one aggregate query, bucketed in PHP
        // (portable across sqlite/mysql, no N+1).
        $trend = array_fill(0, 8, 0.0);
        $trendLabels = [];
        $weekStart = now()->subWeeks(7)->startOfWeek();
        for ($i = 0; $i < 8; $i++) {
            $trendLabels[] = $weekStart->copy()->addWeeks($i)->format('d M');
        }
        $payments = Payment::query()
            ->whereDate('payment_date', '>=', $weekStart->toDateString())
            ->get(['payment_date', 'amount']);
        foreach ($payments as $payment) {
            $idx = (int) $weekStart->diffInWeeks($payment->payment_date, false);
            if ($idx >= 0 && $idx < 8) {
                $trend[$idx] += (float) $payment->amount;
            }
        }

        $overdueInvoices = Invoice::with('client:id,name')
            ->where('status', 'overdue')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $pendingQuotes = Quote::with('client:id,name')
            ->whereIn('status', ['sent', 'draft'])
            ->orderBy('expiry_date')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalRevenue',
            'outstandingAmount',
            'paidCount',
            'overdueCount',
            'clientCount',
            'recentInvoices',
            'trend',
            'trendLabels',
            'overdueInvoices',
            'pendingQuotes'
        ));
    }
}

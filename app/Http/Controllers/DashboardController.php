<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
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

        return view('dashboard.index', compact(
            'totalRevenue',
            'outstandingAmount',
            'paidCount',
            'overdueCount',
            'clientCount',
            'recentInvoices'
        ));
    }
}

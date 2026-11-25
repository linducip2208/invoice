<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\View\View;

class AdminAnalyticsController extends Controller
{
    public function index(): View
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = now()->subMonthsNoOverflow($i)->startOfMonth();
            $end = now()->subMonthsNoOverflow($i)->endOfMonth();
            $months[] = [
                'label' => $start->format('M Y'),
                'users' => User::whereBetween('created_at', [$start, $end])->count(),
                'workspaces' => Workspace::whereBetween('created_at', [$start, $end])->count(),
                'mrr_idr' => (float) SubscriptionInvoice::where('status', 'paid')->where('currency', 'IDR')->whereBetween('paid_at', [$start, $end])->sum('amount'),
                'mrr_usd' => (float) SubscriptionInvoice::where('status', 'paid')->where('currency', 'USD')->whereBetween('paid_at', [$start, $end])->sum('amount'),
            ];
        }
        $planDistribution = \App\Models\Plan::withCount('subscriptions')->orderBy('sort_order')->get();
        $revenueByCurrency = SubscriptionInvoice::where('status', 'paid')->selectRaw('currency, SUM(amount) as total, COUNT(*) as cnt')->groupBy('currency')->get();
        $statusBreakdown = Subscription::selectRaw('status, COUNT(*) as cnt')->groupBy('status')->get();

        return view('admin.analytics', compact('months', 'planDistribution', 'revenueByCurrency', 'statusBreakdown'));
    }
}

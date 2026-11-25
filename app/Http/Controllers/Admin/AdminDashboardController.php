<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $totalWorkspaces = Workspace::count();
        $activeSubs = Subscription::where('status', 'active')->whereHas('plan', fn ($q) => $q->where('slug', '!=', 'free'))->count();
        $pendingSubs = Subscription::where('status', 'pending')->count();
        $expiredSubs = Subscription::whereIn('status', ['expired', 'cancelled'])->count();
        $freeWorkspaces = Subscription::whereHas('plan', fn ($q) => $q->where('slug', 'free'))->count();

        $startMonth = now()->startOfMonth();
        $startLastMonth = now()->subMonthNoOverflow()->startOfMonth();
        $endLastMonth = now()->subMonthNoOverflow()->endOfMonth();

        $mrrIdr = SubscriptionInvoice::where('status', 'paid')->where('currency', 'IDR')->whereDate('paid_at', '>=', $startMonth)->sum('amount');
        $mrrUsd = SubscriptionInvoice::where('status', 'paid')->where('currency', 'USD')->whereDate('paid_at', '>=', $startMonth)->sum('amount');
        $lastMrrIdr = SubscriptionInvoice::where('status', 'paid')->where('currency', 'IDR')->whereBetween('paid_at', [$startLastMonth, $endLastMonth])->sum('amount');
        $lastMrrUsd = SubscriptionInvoice::where('status', 'paid')->where('currency', 'USD')->whereBetween('paid_at', [$startLastMonth, $endLastMonth])->sum('amount');

        $growthIdr = $lastMrrIdr > 0 ? round((($mrrIdr - $lastMrrIdr) / $lastMrrIdr) * 100, 1) : ($mrrIdr > 0 ? 100.0 : 0.0);
        $growthUsd = $lastMrrUsd > 0 ? round((($mrrUsd - $lastMrrUsd) / $lastMrrUsd) * 100, 1) : ($mrrUsd > 0 ? 100.0 : 0.0);

        // 12-week growth series (real queries).
        $userGrowth = [];
        $workspaceGrowth = [];
        $mrrSeries = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subWeeks($i);
            $label = $d->format('d M');
            $userGrowth[] = ['label' => $label, 'value' => User::where('created_at', '<=', $d->endOfWeek())->count()];
            $workspaceGrowth[] = ['label' => $label, 'value' => Workspace::where('created_at', '<=', $d->endOfWeek())->count()];
            $mrrSeries[] = ['label' => $label, 'idr' => (float) SubscriptionInvoice::where('status', 'paid')->where('currency', 'IDR')->whereDate('paid_at', '<=', $d->endOfWeek())->whereDate('paid_at', '>=', $d->startOfWeek())->sum('amount'), 'usd' => (float) SubscriptionInvoice::where('status', 'paid')->where('currency', 'USD')->whereDate('paid_at', '<=', $d->endOfWeek())->whereDate('paid_at', '>=', $d->startOfWeek())->sum('amount')];
        }

        $planDistribution = \App\Models\Plan::withCount('subscriptions')->orderBy('sort_order')->get();
        $revenueByCurrency = SubscriptionInvoice::where('status', 'paid')
            ->selectRaw('currency, SUM(amount) as total, COUNT(*) as cnt')
            ->groupBy('currency')->get();

        $totalPaidSubs = Subscription::whereHas('plan', fn ($q) => $q->where('slug', '!=', 'free'))->count();
        $conversion = $totalWorkspaces > 0 ? round(($totalPaidSubs / $totalWorkspaces) * 100, 1) : 0.0;

        // Churn: cancelled+expired in last 30d divided by active at start — only if data exists.
        $churnBase = Subscription::where('created_at', '<', now()->subDays(30))->count();
        $churned = Subscription::whereIn('status', ['cancelled', 'expired'])->where('updated_at', '>=', now()->subDays(30))->count();
        $churnRate = ($churnBase > 0 && $churned > 0) ? round(($churned / $churnBase) * 100, 1) : null;

        $recentSubs = Subscription::with(['workspace', 'plan'])->latest()->take(10)->get();
        $upcomingRenewals = Subscription::with(['workspace', 'plan'])->where('status', 'active')
            ->whereNotNull('current_period_ends_at')
            ->whereBetween('current_period_ends_at', [now(), now()->addDays(14)])
            ->orderBy('current_period_ends_at')->take(8)->get();

        $pendingPayments = Subscription::with(['workspace', 'plan'])->where('status', 'pending')->whereNotNull('manual_proof_path')->latest()->take(8)->get();
        $approvedPayments = SubscriptionInvoice::with(['workspace', 'subscription.plan'])
            ->where('status', 'paid')->where('reference_no', 'like', 'MAN-%')->latest('paid_at')->take(5)->get();
        $recentInvoices = SubscriptionInvoice::with(['workspace', 'subscription.plan'])->latest()->take(8)->get();

        $newUsers = User::latest()->take(5)->get();
        $newWorkspaces = Workspace::latest()->take(5)->get();
        $recentAudit = \App\Models\AuditLog::with(['user', 'workspace'])->latest()->take(8)->get();

        $fxRate = \App\Services\CurrencyRateService::rate('USD', 'IDR');
        $fxDate = \App\Services\CurrencyRateService::rateDate('USD', 'IDR');
        $mrrUsdIdr = $fxRate ? round((float) $mrrUsd * $fxRate, 0) : null;

        return view('admin.dashboard', compact(
            'totalUsers', 'totalWorkspaces', 'activeSubs', 'pendingSubs', 'expiredSubs', 'freeWorkspaces',
            'mrrIdr', 'mrrUsd', 'lastMrrIdr', 'lastMrrUsd', 'growthIdr', 'growthUsd',
            'fxRate', 'fxDate', 'mrrUsdIdr',
            'userGrowth', 'workspaceGrowth', 'mrrSeries',
            'planDistribution', 'revenueByCurrency', 'conversion', 'churnRate', 'churned',
            'recentSubs', 'upcomingRenewals', 'pendingPayments', 'approvedPayments', 'recentInvoices',
            'newUsers', 'newWorkspaces', 'recentAudit'
        ));
    }
}

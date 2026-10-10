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
        $totalUsers       = User::count();
        $totalWorkspaces  = Workspace::count();
        $activeSubs       = Subscription::where('status', 'active')->whereHas('plan', fn($q) => $q->where('slug', '!=', 'free'))->count();
        $freeWorkspaces   = Subscription::whereHas('plan', fn($q) => $q->where('slug', 'free'))->count();
        $mrrIdr           = SubscriptionInvoice::where('status', 'paid')->where('currency', 'IDR')->whereDate('paid_at', '>=', now()->startOfMonth())->sum('amount');
        $mrrUsd           = SubscriptionInvoice::where('status', 'paid')->where('currency', 'USD')->whereDate('paid_at', '>=', now()->startOfMonth())->sum('amount');
        $recentSubs       = Subscription::with(['workspace', 'plan'])->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalWorkspaces', 'activeSubs', 'freeWorkspaces',
            'mrrIdr', 'mrrUsd', 'recentSubs'
        ));
    }
}

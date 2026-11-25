<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Illuminate\View\View;

class AdminRevenueController extends Controller
{
    public function index(): View
    {
        $byMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = now()->subMonthsNoOverflow($i)->startOfMonth();
            $end = now()->subMonthsNoOverflow($i)->endOfMonth();
            $byMonth[] = [
                'label' => $start->format('M Y'),
                'idr' => (float) SubscriptionInvoice::where('status', 'paid')->where('currency', 'IDR')->whereBetween('paid_at', [$start, $end])->sum('amount'),
                'usd' => (float) SubscriptionInvoice::where('status', 'paid')->where('currency', 'USD')->whereBetween('paid_at', [$start, $end])->sum('amount'),
                'count' => SubscriptionInvoice::where('status', 'paid')->whereBetween('paid_at', [$start, $end])->count(),
            ];
        }
        $pending = Subscription::with(['workspace', 'plan'])->where('status', 'pending')->latest()->paginate(15, ['*'], 'pending_page');
        $invoices = SubscriptionInvoice::with(['workspace', 'subscription.plan'])->latest()->paginate(20, ['*'], 'invoice_page');

        return view('admin.revenue', compact('byMonth', 'pending', 'invoices'));
    }
}

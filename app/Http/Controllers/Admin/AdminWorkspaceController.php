<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminWorkspaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Workspace::query()->with(['subscription.plan'])->withCount(['invoices', 'clients', 'users']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        $workspaces = $query->latest()->paginate(20)->withQueryString();

        return view('admin.workspaces.index', compact('workspaces'));
    }

    public function show(Workspace $workspace): View
    {
        $workspace->load(['users', 'subscription.plan', 'subscription.invoices']);
        $plans = \App\Models\Plan::orderBy('sort_order')->get();

        return view('admin.workspaces.show', compact('workspace', 'plans'));
    }

    public function assignPlan(\Illuminate\Http\Request $request, Workspace $workspace): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'plan_id'          => 'required|exists:plans,id',
            'billing_period'   => 'required|in:monthly,yearly,free',
            'currency'         => 'required|in:IDR,USD',
            'period_days'      => 'nullable|integer|min:1|max:3650',
            'mark_paid'        => 'nullable|boolean',
        ]);

        $sub = $workspace->activeSubscription();
        $plan = \App\Models\Plan::findOrFail($validated['plan_id']);

        $days = (int) ($validated['period_days'] ?? ($validated['billing_period'] === 'yearly' ? 365 : 30));
        $end = $plan->isFree() ? null : now()->addDays($days);

        $sub->update([
            'plan_id'                  => $plan->id,
            'billing_period'           => $validated['billing_period'],
            'currency'                 => $validated['currency'],
            'payment_method'           => 'manual',
            'status'                   => 'active',
            'current_period_starts_at' => $plan->isFree() ? null : now(),
            'current_period_ends_at'   => $end,
            'last_payment_at'          => empty($validated['mark_paid']) ? $sub->last_payment_at : now(),
            'cancel_at_period_end'     => false,
            'cancelled_at'             => null,
        ]);

        if (! $plan->isFree() && ! empty($validated['mark_paid'])) {
            \App\Models\SubscriptionInvoice::create([
                'subscription_id' => $sub->id,
                'workspace_id'    => $workspace->id,
                'reference_no'    => 'ADM-' . $sub->id . '-' . now()->format('YmdHis'),
                'amount'          => $plan->priceFor($validated['currency'], $validated['billing_period'] === 'free' ? 'monthly' : $validated['billing_period']),
                'currency'        => $validated['currency'],
                'status'          => 'paid',
                'paid_at'         => now(),
                'period_starts_at' => now(),
                'period_ends_at'  => $end,
            ]);
        }

        return back()->with('success', "Plan {$plan->name} di-assign ke {$workspace->name}, status active.");
    }
}

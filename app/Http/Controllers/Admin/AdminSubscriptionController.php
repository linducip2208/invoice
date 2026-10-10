<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subscription::query()->with(['workspace', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->whereHas('plan', fn($q) => $q->where('slug', $request->plan));
        }

        $subscriptions = $query->latest()->paginate(20)->withQueryString();
        $plans         = Plan::orderBy('sort_order')->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'plans'));
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['workspace', 'plan', 'invoices']);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id'                => 'required|exists:plans,id',
            'status'                 => 'required|in:trial,active,past_due,pending,cancelled,expired',
            'billing_period'         => 'required|in:monthly,yearly,free',
            'currency'               => 'required|in:IDR,USD',
            'current_period_ends_at' => 'nullable|date',
        ]);

        $subscription->update($validated);

        return back()->with('success', 'Subscription disimpan.');
    }

    /** Manual assignment hub — pick any workspace + plan, activate. */
    public function manualAssign()
    {
        $workspaces = \App\Models\Workspace::with('subscription.plan')->orderBy('name')->get();
        $users      = \App\Models\User::where('is_superadmin', false)->orderBy('name')->get();
        $plans      = \App\Models\Plan::orderBy('sort_order')->get();
        $pending = Subscription::with(['workspace', 'plan'])
            ->where('status', 'pending')
            ->orderByDesc('updated_at')
            ->get();
        return view('admin.subscriptions.manual', compact('workspaces', 'users', 'plans', 'pending'));
    }

    /** Create a fresh user + workspace + manual subscription in one shot. */
    public function manualCreate(\Illuminate\Http\Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode'              => 'required|in:existing_user,new_user',
            'existing_user_id'  => 'nullable|required_if:mode,existing_user|exists:users,id',
            'new_user_name'     => 'nullable|required_if:mode,new_user|string|max:255',
            'new_user_email'    => 'nullable|required_if:mode,new_user|email|unique:users,email',
            'new_user_password' => 'nullable|required_if:mode,new_user|string|min:6',
            'workspace_name'    => 'required|string|max:255',
            'plan_id'           => 'required|exists:plans,id',
            'billing_period'    => 'required|in:monthly,yearly,free',
            'currency'          => 'required|in:IDR,USD',
            'period_days'       => 'nullable|integer|min:1|max:3650',
            'mark_paid'         => 'nullable|boolean',
            'note'              => 'nullable|string|max:1000',
        ]);

        $plan = \App\Models\Plan::findOrFail($validated['plan_id']);

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $plan, &$workspace) {
            // 1) Get or create user
            if ($validated['mode'] === 'new_user') {
                $user = \App\Models\User::create([
                    'name'              => $validated['new_user_name'],
                    'email'             => $validated['new_user_email'],
                    'password'          => \Illuminate\Support\Facades\Hash::make($validated['new_user_password']),
                    'email_verified_at' => now(),
                ]);
            } else {
                $user = \App\Models\User::findOrFail($validated['existing_user_id']);
            }

            // 2) Create workspace
            $workspace = \App\Models\Workspace::create([
                'name'             => $validated['workspace_name'],
                'currency'         => $validated['currency'],
                'invoice_prefix'   => 'INV-',
                'next_invoice_no'  => 1,
                'default_template' => 'classic',
            ]);
            $workspace->users()->attach($user->id, ['role' => 'admin']);

            // 3) Create subscription
            $days = (int) ($validated['period_days'] ?? ($validated['billing_period'] === 'yearly' ? 365 : 30));
            $end = $plan->isFree() ? null : now()->addDays($days);

            $sub = $workspace->activeSubscription();
            $sub->update([
                'plan_id'                  => $plan->id,
                'billing_period'           => $validated['billing_period'],
                'currency'                 => $validated['currency'],
                'payment_method'           => 'manual',
                'status'                   => 'active',
                'current_period_starts_at' => $plan->isFree() ? null : now(),
                'current_period_ends_at'   => $end,
                'last_payment_at'          => empty($validated['mark_paid']) ? null : now(),
                'manual_payment_note'      => $validated['note'] ?? null,
            ]);

            if (! $plan->isFree() && ! empty($validated['mark_paid'])) {
                \App\Models\SubscriptionInvoice::create([
                    'subscription_id'  => $sub->id,
                    'workspace_id'     => $workspace->id,
                    'reference_no'     => 'ADM-' . $sub->id . '-' . now()->format('YmdHis'),
                    'amount'           => $plan->priceFor($validated['currency'], $validated['billing_period'] === 'free' ? 'monthly' : $validated['billing_period']),
                    'currency'         => $validated['currency'],
                    'status'           => 'paid',
                    'paid_at'          => now(),
                    'period_starts_at' => now(),
                    'period_ends_at'   => $end,
                ]);
            }
        });

        // Bust caches for the freshly-modified workspace
        $workspace->unsetRelation('subscription');
        app()->forgetInstance(\App\Models\Workspace::class . ':' . $workspace->id);

        return redirect()->route('admin.workspaces.show', $workspace)
            ->with('success', "✅ Workspace '{$workspace->name}' dibuat dengan plan {$plan->name}. Plan saat ini sekarang: {$plan->name}.");
    }

    public function manualStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'workspace_id'   => 'required|exists:workspaces,id',
            'plan_id'        => 'required|exists:plans,id',
            'billing_period' => 'required|in:monthly,yearly,free',
            'currency'       => 'required|in:IDR,USD',
            'period_days'    => 'nullable|integer|min:1|max:3650',
            'mark_paid'      => 'nullable|boolean',
            'note'           => 'nullable|string|max:1000',
        ]);

        $workspace = \App\Models\Workspace::findOrFail($validated['workspace_id']);
        $plan = \App\Models\Plan::findOrFail($validated['plan_id']);
        $sub = $workspace->activeSubscription();

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
            'manual_payment_note'      => $validated['note'] ?? null,
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

        // Bust caches so subsequent requests reflect the new plan immediately.
        $workspace->unsetRelation('subscription');
        app()->forgetInstance(\App\Models\Workspace::class . ':' . $workspace->id);

        return back()->with('success', "✅ {$plan->name} di-assign ke {$workspace->name}, valid {$days} hari. (Refresh halaman target untuk lihat update)");
    }
}

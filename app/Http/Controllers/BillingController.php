<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\Billing\RecurringBillingAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function show(): View
    {
        $workspace    = workspace();
        $subscription = $workspace->activeSubscription();
        $plans        = Plan::where('is_public', true)->orderBy('sort_order')->get();
        $billingHistory = $subscription->invoices()->latest('created_at')->take(20)->get();

        $usage = [
            'invoices'  => Invoice::where('workspace_id', $workspace->id)->withoutGlobalScopes()->count(),
            'clients'   => $workspace->clients()->count(),
            'recurring' => $workspace->recurringInvoices()->count(),
        ];

        return view('billing.show', compact('workspace', 'subscription', 'plans', 'usage', 'billingHistory'));
    }

    public function subscribe(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'period'         => 'required|in:monthly,yearly',
            'currency'       => 'required|in:IDR,USD',
            'payment_method' => 'nullable|in:gateway,manual',
        ]);
        $validated['payment_method'] = $validated['payment_method'] ?? 'gateway';

        $workspace = workspace();
        $subscription = $workspace->activeSubscription();

        // Free plan = downgrade — no payment needed
        if ($plan->isFree()) {
            $subscription->update([
                'plan_id'                  => $plan->id,
                'billing_period'           => 'free',
                'currency'                 => $validated['currency'],
                'status'                   => 'active',
                'cancel_at_period_end'     => false,
                'cancelled_at'             => null,
                'current_period_starts_at' => null,
                'current_period_ends_at'   => null,
            ]);

            return redirect()->route('billing.show')->with('success', __('Plan diturunkan ke Free.'));
        }

        $price = $plan->priceFor($validated['currency'], $validated['period']);

        if ($price <= 0) {
            return redirect()->route('billing.show')->with('error', __('Harga plan ini belum di-set.'));
        }

        $subscription->update([
            'plan_id'        => $plan->id,
            'billing_period' => $validated['period'],
            'currency'       => $validated['currency'],
            'payment_method' => $validated['payment_method'],
        ]);

        // Manual payment route — show instructions page, await admin verification
        if ($validated['payment_method'] === 'manual') {
            $subscription->update(['status' => 'pending']);
            return redirect()->route('billing.manual', $subscription)->with('success',
                __('Plan dipilih. Lakukan transfer manual lalu upload bukti.')
            );
        }

        // Try to charge via configured recurring gateway.
        $gateway = \App\Models\PaymentGateway::where('is_active', true)
            ->where('supports_recurring', true)
            ->orderBy('sort_order')
            ->first();

        if ($gateway) {
            try {
                $result = app(RecurringBillingAdapter::class)->createSubscription($gateway, $plan, $subscription, [
                    'email'       => auth()->user()->email,
                    'name'        => auth()->user()->name,
                    'success_url' => route('billing.show') . '?status=success',
                    'cancel_url'  => route('billing.show') . '?status=cancelled',
                ]);

                $subscription->update([
                    'payment_gateway_id'       => $gateway->id,
                    'external_subscription_id' => $result['external_subscription_id'],
                    'status'                   => 'active',
                ]);

                if (! empty($result['checkout_url'])) {
                    return redirect()->away($result['checkout_url']);
                }
            } catch (\Throwable $e) {
                return redirect()->route('billing.show')->with('error',
                    __('Gagal connect ke payment gateway: :err', ['err' => $e->getMessage()])
                );
            }
        }

        // No recurring gateway configured — activate manually (admin overrides via webhook later)
        $periodEnd = $validated['period'] === 'yearly' ? now()->addYear() : now()->addMonth();
        $subscription->update([
            'status'                   => 'active',
            'current_period_starts_at' => now(),
            'current_period_ends_at'   => $periodEnd,
            'last_payment_at'          => now(),
            'cancel_at_period_end'     => false,
            'cancelled_at'             => null,
        ]);

        return redirect()->route('billing.show')->with('success',
            __('Plan diaktifkan: :plan (:period). Setup recurring gateway di /admin untuk auto-billing.', [
                'plan'   => $plan->name,
                'period' => $validated['period'] === 'yearly' ? 'tahunan' : 'bulanan',
            ])
        );
    }

    public function cancel(): RedirectResponse
    {
        $sub = workspace()->activeSubscription();

        if ($sub->isFree()) {
            return back()->with('error', __('Plan Free tidak perlu di-cancel.'));
        }

        $sub->update([
            'cancel_at_period_end' => true,
            'cancelled_at'         => now(),
        ]);

        return back()->with('success', __('Subscription akan berakhir di akhir periode billing.'));
    }

    public function resume(): RedirectResponse
    {
        $sub = workspace()->activeSubscription();

        $sub->update([
            'cancel_at_period_end' => false,
            'cancelled_at'         => null,
        ]);

        return back()->with('success', __('Subscription dilanjutkan.'));
    }

    public function manualInstructions(Subscription $subscription)
    {
        abort_unless($subscription->workspace_id === workspace()->id, 403);
        $methods = \App\Models\ManualPaymentMethod::where('is_active', true)->orderBy('sort_order')->get();
        return view('billing.manual', compact('subscription', 'methods'));
    }

    public function uploadManualProof(Request $request, Subscription $subscription): RedirectResponse
    {
        abort_unless($subscription->workspace_id === workspace()->id, 403);

        $validated = $request->validate([
            'proof' => 'required|image|max:4096',
            'note'  => 'nullable|string|max:1000',
        ]);

        if ($subscription->manual_proof_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($subscription->manual_proof_path);
        }

        $subscription->update([
            'manual_proof_path'   => $request->file('proof')->store('payment-proofs', 'public'),
            'manual_payment_note' => $validated['note'] ?? null,
        ]);

        // Notify all superadmins via in-app notification
        \App\Models\User::where('is_superadmin', true)->each(function ($admin) use ($subscription) {
            \App\Models\AppNotification::create([
                'workspace_id' => $subscription->workspace_id,
                'user_id'      => $admin->id,
                'type'         => 'manual_payment_proof',
                'title'        => '💸 Bukti transfer baru',
                'message'      => 'Workspace "' . $subscription->workspace?->name . '" upload bukti untuk plan ' . $subscription->plan?->name,
                'action_url'   => route('admin.manual-payments.index'),
                'icon'         => '💸',
            ]);
        });

        return redirect()->route('billing.show')->with('success',
            __('Bukti transfer di-upload. Tim kami akan verifikasi dalam 1×24 jam.')
        );
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPaymentMethod;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminManualPaymentController extends Controller
{
    public function index(): View
    {
        $methods = ManualPaymentMethod::orderBy('sort_order')->get();
        $pending = Subscription::with(['workspace', 'plan'])
            ->where('status', 'pending')
            ->whereNotNull('manual_proof_path')
            ->latest()
            ->get();
        return view('admin.manual-payments.index', compact('methods', 'pending'));
    }

    public function storeMethod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label'            => 'required|string|max:100',
            'type'             => 'required|string|max:30',
            'account_name'     => 'nullable|string|max:255',
            'account_number'   => 'nullable|string|max:255',
            'bank_or_provider' => 'nullable|string|max:255',
            'instructions'     => 'nullable|string|max:1000',
            'is_active'        => 'boolean',
            'sort_order'       => 'nullable|integer',
        ]);
        $validated['is_active'] = ! empty($validated['is_active']);
        ManualPaymentMethod::create($validated);
        return back()->with('success', 'Metode pembayaran ditambahkan.');
    }

    public function destroyMethod(ManualPaymentMethod $method): RedirectResponse
    {
        $method->delete();
        return back()->with('success', 'Metode dihapus.');
    }

    public function approve(Subscription $subscription): RedirectResponse
    {
        $period = $subscription->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();
        $subscription->update([
            'status'                   => 'active',
            'last_payment_at'          => now(),
            'current_period_starts_at' => now(),
            'current_period_ends_at'   => $period,
        ]);

        \App\Models\SubscriptionInvoice::create([
            'subscription_id' => $subscription->id,
            'workspace_id'    => $subscription->workspace_id,
            'reference_no'    => 'MAN-' . $subscription->id . '-' . now()->format('YmdHis'),
            'amount'          => $subscription->plan->priceFor($subscription->currency, $subscription->billing_period),
            'currency'        => $subscription->currency,
            'status'          => 'paid',
            'paid_at'         => now(),
            'period_starts_at' => now(),
            'period_ends_at'  => $period,
        ]);

        return back()->with('success', 'Subscription di-approve. Workspace sudah aktif.');
    }

    public function reject(Subscription $subscription): RedirectResponse
    {
        $subscription->update([
            'status'              => 'cancelled',
            'manual_payment_note' => trim(($subscription->manual_payment_note ?? '') . "\n[REJECTED " . now()->format('d M Y H:i') . "]"),
        ]);
        return back()->with('success', 'Pembayaran di-reject.');
    }
}

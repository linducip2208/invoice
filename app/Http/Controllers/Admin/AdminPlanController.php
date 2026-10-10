<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::orderBy('sort_order')->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['features'] = $this->normalizeFeatures($data['features'] ?? []);

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan dibuat.');
    }

    public function show(Plan $plan): View
    {
        return view('admin.plans.show', compact('plan'));
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['features'] = $this->normalizeFeatures($data['features'] ?? []);

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan disimpan.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->slug === 'free') {
            return back()->with('error', 'Plan Free tidak boleh dihapus.');
        }

        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Plan masih punya subscription aktif. Migrate dulu.');
        }

        $plan->delete();

        return back()->with('success', 'Plan dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'slug'              => 'required|string|max:50',
            'name'              => 'required|string|max:100',
            'tagline'           => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'price_idr_monthly' => 'required|numeric|min:0',
            'price_idr_yearly'  => 'required|numeric|min:0',
            'price_usd_monthly' => 'required|numeric|min:0',
            'price_usd_yearly'  => 'required|numeric|min:0',
            'trial_days'        => 'nullable|integer|min:0',
            'is_public'         => 'boolean',
            'is_default'        => 'boolean',
            'sort_order'        => 'nullable|integer',
            'features'          => 'nullable|array',
        ]);
    }

    protected function normalizeFeatures(array $features): array
    {
        $bools = ['invoice_lock', 'recurring_enabled', 'custom_branding', 'remove_watermark',
                  'drag_drop_builder', 'api_access', 'audit_log', 'time_tracking',
                  'expense_tracking', 'bulk_actions', 'dunning', 'custom_email_template',
                  'custom_domain', 'tenant_webhooks', '2fa', 'csv_import'];

        foreach ($bools as $b) {
            $features[$b] = ! empty($features[$b]);
        }

        foreach (['max_invoices_lifetime', 'max_invoices_per_month', 'max_clients', 'max_users'] as $k) {
            if (isset($features[$k]) && $features[$k] !== '' && $features[$k] !== null) {
                $features[$k] = (int) $features[$k];
            } else {
                $features[$k] = null;
            }
        }

        return $features;
    }
}

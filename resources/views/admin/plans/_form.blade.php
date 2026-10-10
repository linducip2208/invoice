@php $plan = $plan ?? null; $f = $plan?->features ?? []; @endphp
<form method="POST" action="{{ $plan ? route('admin.plans.update', $plan) : route('admin.plans.store') }}" class="space-y-6">
    @csrf
    @if($plan) @method('PUT') @endif

    <div class="card-unicorn space-y-4">
        <h3 class="font-display font-bold text-lg text-purple-900">Basic</h3>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-bold text-purple-700">Slug *</label><input type="text" name="slug" value="{{ old('slug', $plan?->slug) }}" required class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full"></div>
            <div><label class="text-sm font-bold text-purple-700">Name *</label><input type="text" name="name" value="{{ old('name', $plan?->name) }}" required class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full"></div>
        </div>
        <div><label class="text-sm font-bold text-purple-700">Tagline</label><input type="text" name="tagline" value="{{ old('tagline', $plan?->tagline) }}" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full"></div>
        <div><label class="text-sm font-bold text-purple-700">Description</label><textarea name="description" rows="2" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">{{ old('description', $plan?->description) }}</textarea></div>
    </div>

    <div class="card-unicorn space-y-4">
        <h3 class="font-display font-bold text-lg text-purple-900">Pricing</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div><label class="text-xs text-purple-600 font-bold">IDR / month</label><input type="number" step="0.01" name="price_idr_monthly" value="{{ old('price_idr_monthly', $plan?->price_idr_monthly ?? 0) }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-xs text-purple-600 font-bold">IDR / year</label><input type="number" step="0.01" name="price_idr_yearly" value="{{ old('price_idr_yearly', $plan?->price_idr_yearly ?? 0) }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-xs text-purple-600 font-bold">USD / month</label><input type="number" step="0.01" name="price_usd_monthly" value="{{ old('price_usd_monthly', $plan?->price_usd_monthly ?? 0) }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-xs text-purple-600 font-bold">USD / year</label><input type="number" step="0.01" name="price_usd_yearly" value="{{ old('price_usd_yearly', $plan?->price_usd_yearly ?? 0) }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
        </div>
    </div>

    <div class="card-unicorn space-y-4">
        <h3 class="font-display font-bold text-lg text-purple-900">Limits</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div><label class="text-xs text-purple-600 font-bold">Max invoices (lifetime)</label><input type="number" name="features[max_invoices_lifetime]" value="{{ old('features.max_invoices_lifetime', $f['max_invoices_lifetime'] ?? '') }}" placeholder="Empty = unlimited" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-xs text-purple-600 font-bold">Max clients</label><input type="number" name="features[max_clients]" value="{{ old('features.max_clients', $f['max_clients'] ?? '') }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-xs text-purple-600 font-bold">Max users</label><input type="number" name="features[max_users]" value="{{ old('features.max_users', $f['max_users'] ?? '') }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-xs text-purple-600 font-bold">Trial days</label><input type="number" name="trial_days" value="{{ old('trial_days', $plan?->trial_days ?? 0) }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
        </div>
    </div>

    <div class="card-unicorn">
        <h3 class="font-display font-bold text-lg text-purple-900 mb-4">Feature Flags</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
            @php
                $flags = [
                    'invoice_lock' => 'Lock invoice (no edit/delete)',
                    'recurring_enabled' => 'Recurring invoices',
                    'custom_branding' => 'Custom branding',
                    'remove_watermark' => 'Remove watermark',
                    'drag_drop_builder' => 'Drag-drop builder',
                    'api_access' => 'API access',
                    'audit_log' => 'Audit log',
                    'time_tracking' => 'Time tracking',
                    'expense_tracking' => 'Expense tracking',
                    'bulk_actions' => 'Bulk actions',
                    'dunning' => 'Dunning automation',
                    'custom_email_template' => 'Custom email templates',
                    'custom_domain' => 'Custom domain',
                    'tenant_webhooks' => 'Tenant webhooks',
                    '2fa' => '2FA',
                    'csv_import' => 'CSV import',
                ];
            @endphp
            @foreach($flags as $key => $label)
                <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-purple-50">
                    <input type="checkbox" name="features[{{ $key }}]" value="1" {{ old('features.'.$key, $f[$key] ?? false) ? 'checked' : '' }} class="rounded border-purple-300">
                    <span class="text-purple-700">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="card-unicorn">
        <div class="grid grid-cols-3 gap-4">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_public" value="1" {{ old('is_public', $plan?->is_public) ? 'checked' : '' }}> <span class="text-sm font-semibold">Public on /pricing</span></label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_default" value="1" {{ old('is_default', $plan?->is_default) ? 'checked' : '' }}> <span class="text-sm font-semibold">Default plan for new signups</span></label>
            <div><label class="text-xs text-purple-600 font-bold">Sort order</label><input type="number" name="sort_order" value="{{ old('sort_order', $plan?->sort_order ?? 0) }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.plans.index') }}" class="btn-unicorn-secondary">Cancel</a>
        <button type="submit" class="btn-unicorn">{{ $plan ? 'Save Plan' : 'Create Plan' }}</button>
    </div>
</form>

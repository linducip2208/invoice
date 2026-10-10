@php $plan = $plan ?? null; $f = $plan?->features ?? []; @endphp
<form method="POST" action="{{ $plan ? route('admin.plans.update', $plan) : route('admin.plans.store') }}">
    @csrf
    @if($plan) @method('PUT') @endif

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Basic') }}</h3>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6">
                    <x-tabler.field :label="__('Slug')" name="slug" required>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $plan?->slug) }}" required
                            class="form-control @error('slug') is-invalid @enderror">
                    </x-tabler.field>
                </div>
                <div class="col-md-6">
                    <x-tabler.field :label="__('Name')" name="name" required>
                        <input type="text" id="name" name="name" value="{{ old('name', $plan?->name) }}" required
                            class="form-control @error('name') is-invalid @enderror">
                    </x-tabler.field>
                </div>
            </div>
            <x-tabler.field :label="__('Tagline')" name="tagline">
                <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $plan?->tagline) }}"
                    class="form-control @error('tagline') is-invalid @enderror">
            </x-tabler.field>
            <x-tabler.field :label="__('Description')" name="description">
                <textarea id="description" name="description" rows="2"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $plan?->description) }}</textarea>
            </x-tabler.field>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Pricing') }}</h3>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('IDR / month')" name="price_idr_monthly">
                        <input type="number" step="0.01" id="price_idr_monthly" name="price_idr_monthly" value="{{ old('price_idr_monthly', $plan?->price_idr_monthly ?? 0) }}"
                            class="form-control @error('price_idr_monthly') is-invalid @enderror">
                    </x-tabler.field>
                </div>
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('IDR / year')" name="price_idr_yearly">
                        <input type="number" step="0.01" id="price_idr_yearly" name="price_idr_yearly" value="{{ old('price_idr_yearly', $plan?->price_idr_yearly ?? 0) }}"
                            class="form-control @error('price_idr_yearly') is-invalid @enderror">
                    </x-tabler.field>
                </div>
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('USD / month')" name="price_usd_monthly">
                        <input type="number" step="0.01" id="price_usd_monthly" name="price_usd_monthly" value="{{ old('price_usd_monthly', $plan?->price_usd_monthly ?? 0) }}"
                            class="form-control @error('price_usd_monthly') is-invalid @enderror">
                    </x-tabler.field>
                </div>
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('USD / year')" name="price_usd_yearly">
                        <input type="number" step="0.01" id="price_usd_yearly" name="price_usd_yearly" value="{{ old('price_usd_yearly', $plan?->price_usd_yearly ?? 0) }}"
                            class="form-control @error('price_usd_yearly') is-invalid @enderror">
                    </x-tabler.field>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Limits') }}</h3>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('Max invoices (lifetime)')" name="features.max_invoices_lifetime" :help="__('Empty = unlimited')">
                        <input type="number" id="features.max_invoices_lifetime" name="features[max_invoices_lifetime]" value="{{ old('features.max_invoices_lifetime', $f['max_invoices_lifetime'] ?? '') }}"
                            placeholder="{{ __('Empty = unlimited') }}" class="form-control">
                    </x-tabler.field>
                </div>
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('Max clients')" name="features.max_clients">
                        <input type="number" id="features.max_clients" name="features[max_clients]" value="{{ old('features.max_clients', $f['max_clients'] ?? '') }}"
                            class="form-control">
                    </x-tabler.field>
                </div>
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('Max users')" name="features.max_users">
                        <input type="number" id="features.max_users" name="features[max_users]" value="{{ old('features.max_users', $f['max_users'] ?? '') }}"
                            class="form-control">
                    </x-tabler.field>
                </div>
                <div class="col-sm-6 col-md-3">
                    <x-tabler.field :label="__('Trial days')" name="trial_days">
                        <input type="number" id="trial_days" name="trial_days" value="{{ old('trial_days', $plan?->trial_days ?? 0) }}"
                            class="form-control @error('trial_days') is-invalid @enderror">
                    </x-tabler.field>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Feature Flags') }}</h3>
        </div>
        <div class="card-body">
            <div class="row g-2">
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
                    <div class="col-sm-6 col-md-4">
                        <label class="form-check">
                            <input type="checkbox" name="features[{{ $key }}]" value="1" {{ old('features.'.$key, $f[$key] ?? false) ? 'checked' : '' }} class="form-check-input">
                            <span class="form-check-label">{{ __($label) }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Visibility') }}</h3>
        </div>
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-check form-switch">
                        <input type="checkbox" name="is_public" value="1" {{ old('is_public', $plan?->is_public) ? 'checked' : '' }} class="form-check-input">
                        <span class="form-check-label">{{ __('Public on /pricing') }}</span>
                    </label>
                    <label class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $plan?->is_default) ? 'checked' : '' }} class="form-check-input">
                        <span class="form-check-label">{{ __('Default plan for new signups') }}</span>
                    </label>
                </div>
                <div class="col-md-4">
                    <x-tabler.field :label="__('Sort order')" name="sort_order">
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $plan?->sort_order ?? 0) }}"
                            class="form-control @error('sort_order') is-invalid @enderror">
                    </x-tabler.field>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('admin.plans.index') }}" class="btn">{{ __('Cancel') }}</a>
        <button type="submit" class="btn btn-primary">{{ $plan ? __('Save Plan') : __('Create Plan') }}</button>
    </div>
</form>

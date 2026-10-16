<x-tabler.layout :title="__('Workspace Settings')" :pretitle="__('Workspace')">
    <x-slot name="actions">
        <a href="{{ route('dashboard') }}" class="btn">
            <x-ti name="layout-dashboard" /> {{ __('Dashboard') }}
        </a>
    </x-slot>

    <form method="POST" action="{{ route('workspace.update') }}" enctype="multipart/form-data">
        @csrf @method('PATCH')

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Company Information') }}</h3>
                <div class="card-subtitle">{{ __('These details appear on every invoice.') }}</div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Company Name *')" name="name">
                            <input type="text" id="name" name="name" value="{{ old('name', $workspace->name) }}" required
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Tax ID / NPWP')" name="tax_id">
                            <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id', $workspace->tax_id) }}"
                                placeholder="12.345.678.9-123.000" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Phone')" name="phone">
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $workspace->phone) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Company Email')" name="email">
                            <input type="email" id="email" name="email" value="{{ old('email', $workspace->email) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-12">
                        <x-tabler.field :label="__('Address')" name="address">
                            <textarea id="address" name="address" rows="2" class="form-control">{{ old('address', $workspace->address) }}</textarea>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('City')" name="city">
                            <input type="text" id="city" name="city" value="{{ old('city', $workspace->city) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('State / Province')" name="state">
                            <input type="text" id="state" name="state" value="{{ old('state', $workspace->state) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Country')" name="country">
                            <input type="text" id="country" name="country" value="{{ old('country', $workspace->country) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Postal Code')" name="postal_code">
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $workspace->postal_code) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-12">
                        <x-tabler.field :label="__('Company Logo')" name="logo">
                            @if($workspace->logo_path)
                                <div class="mb-2 d-flex align-items-center gap-2">
                                    <img src="{{ asset('storage/' . $workspace->logo_path) }}" alt="Logo" class="avatar avatar-lg" style="width:auto;">
                                    <span class="form-hint">{{ __('Current logo — upload a new one to replace') }}</span>
                                </div>
                            @endif
                            <input type="file" id="logo" name="logo" accept="image/*" class="form-control">
                        </x-tabler.field>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Invoice Settings') }}</h3>
                <div class="card-subtitle">{{ __('Numbering, currency, template, and branding.') }}</div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Invoice Prefix *')" name="invoice_prefix" :help="__('Next:') . ' ' . $workspace->invoice_prefix . now()->format('Ymd') . '-' . str_pad($workspace->next_invoice_no, 4, '0', STR_PAD_LEFT)">
                            <input type="text" id="invoice_prefix" name="invoice_prefix" value="{{ old('invoice_prefix', $workspace->invoice_prefix) }}" required class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Currency *')" name="currency">
                            <select id="currency" name="currency" class="form-select">
                                @foreach ($currencies as $code => $label)
                                    <option value="{{ $code }}" @selected(old('currency', $workspace->currency) === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-12">
                        <x-tabler.field :label="__('Default Template')" name="default_template">
                            <div class="form-selectgroup">
                                @foreach ($templates as $value => $label)
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="default_template" value="{{ $value }}" {{ old('default_template', $workspace->default_template) === $value ? 'checked' : '' }} class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Primary Color *')" name="primary_color">
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" id="primary_color_picker" aria-label="{{ __('Primary color picker') }}" value="{{ old('primary_color', $workspace->primary_color) }}" class="form-control form-control-color">
                                <input type="text" id="primary_color" name="primary_color" value="{{ old('primary_color', $workspace->primary_color) }}" required pattern="^#[0-9A-Fa-f]{6}$" class="form-control @error('primary_color') is-invalid @enderror">
                            </div>
                            @error('primary_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Accent Color *')" name="accent_color">
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" id="accent_color_picker" aria-label="{{ __('Accent color picker') }}" value="{{ old('accent_color', $workspace->accent_color) }}" class="form-control form-control-color">
                                <input type="text" id="accent_color" name="accent_color" value="{{ old('accent_color', $workspace->accent_color) }}" required pattern="^#[0-9A-Fa-f]{6}$" class="form-control @error('accent_color') is-invalid @enderror">
                            </div>
                            @error('accent_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('After quote approval')" name="quote_auto_convert"
                            :help="__('Manual keeps conversion in workspace hands; auto creates a draft invoice on acceptance.')">
                            <select id="quote_auto_convert" name="quote_auto_convert" class="form-select">
                                <option value="manual" @selected(old('quote_auto_convert', $workspace->quote_auto_convert ?? 'manual') === 'manual')>{{ __('Manual conversion') }}</option>
                                <option value="draft_on_accept" @selected(old('quote_auto_convert', $workspace->quote_auto_convert ?? 'manual') === 'draft_on_accept')>{{ __('Auto-create draft invoice') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('High-risk threshold (optional)')" name="quote_high_risk_threshold"
                            :help="__('Quotes at/above this total require a signed-in designated approver — email codes are refused. Empty disables.')">
                            <input type="number" id="quote_high_risk_threshold" name="quote_high_risk_threshold" min="0" step="0.01"
                                value="{{ old('quote_high_risk_threshold', $workspace->quote_high_risk_threshold) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <x-ti name="check" /> {{ __('Save Settings') }}
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        ['primary','accent'].forEach(function(prefix){
            const picker = document.getElementById(prefix + '_color_picker');
            const text = document.getElementById(prefix + '_color');
            if (!picker || !text) return;
            picker.addEventListener('input', () => text.value = picker.value);
            text.addEventListener('input', () => { try { picker.value = text.value; } catch (e) {} });
        });
    </script>
    @endpush
</x-tabler.layout>

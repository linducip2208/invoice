@props(['client' => null])

<div class="row g-3">
    <div class="col-md-6">
        <x-tabler.field :label="__('Name *')" name="name" type="text" :value="old('name', $client?->name)"
            attrs="required autofocus" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Company Name')" name="company_name" type="text" :value="old('company_name', $client?->company_name)" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Email')" name="email" type="email" :value="old('email', $client?->email)"
            attrs="placeholder=client@magic.land" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Phone')" name="phone" type="text" :value="old('phone', $client?->phone)" />
    </div>

    <div class="col-12">
        <x-tabler.field :label="__('Address')" name="address">
            <textarea id="address" name="address" rows="2"
                class="form-control @error('address') is-invalid @enderror">{{ old('address', $client?->address) }}</textarea>
        </x-tabler.field>
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('City')" name="city" type="text" :value="old('city', $client?->city)" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('State')" name="state" type="text" :value="old('state', $client?->state)" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Country')" name="country" type="text" :value="old('country', $client?->country ?? 'Indonesia')" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Postal Code')" name="postal_code" type="text" :value="old('postal_code', $client?->postal_code)" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Tax ID')" name="tax_id" type="text" :value="old('tax_id', $client?->tax_id)" />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Status')" name="is_active">
            <select id="is_active" name="is_active" class="form-select">
                <option value="1" @selected(old('is_active', $client?->is_active ?? 1) == '1')>{{ __('Active') }}</option>
                <option value="0" @selected(old('is_active', $client?->is_active ?? 1) == '0')>{{ __('Inactive') }}</option>
            </select>
        </x-tabler.field>
    </div>

    <div class="col-12">
        <x-tabler.field :label="__('Notes')" name="notes">
            <textarea id="notes" name="notes" rows="3"
                class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $client?->notes) }}</textarea>
        </x-tabler.field>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label class="form-check">
                <input type="hidden" name="no_reminders" value="0" />
                <input type="checkbox" name="no_reminders" value="1"
                    @checked(old('no_reminders', $client?->no_reminders ?? false))
                    class="form-check-input" />
                <span class="form-check-label">{{ __('Do not send payment reminders to this client') }}</span>
            </label>
            @error('no_reminders')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

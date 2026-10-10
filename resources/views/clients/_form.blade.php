@props(['client' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <x-input-label for="name" :value="__('Name *')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $client?->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="company_name" :value="__('Company Name')" />
        <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $client?->company_name)" />
        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $client?->email)" placeholder="client@magic.land" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $client?->phone)" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <textarea id="address" name="address" rows="2" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">{{ old('address', $client?->address) }}</textarea>
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="city" :value="__('City')" />
        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $client?->city)" />
    </div>

    <div>
        <x-input-label for="state" :value="__('State')" />
        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $client?->state)" />
    </div>

    <div>
        <x-input-label for="country" :value="__('Country')" />
        <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $client?->country ?? 'Indonesia')" />
    </div>

    <div>
        <x-input-label for="postal_code" :value="__('Postal Code')" />
        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $client?->postal_code)" />
    </div>

    <div>
        <x-input-label for="tax_id" :value="__('Tax ID')" />
        <x-text-input id="tax_id" name="tax_id" type="text" class="mt-1 block w-full" :value="old('tax_id', $client?->tax_id)" />
    </div>

    <div>
        <x-input-label for="is_active" :value="__('Status')" />
        <select id="is_active" name="is_active" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">
            <option value="1" @selected(old('is_active', $client?->is_active ?? 1) == '1')>✦ {{ __('Active') }}</option>
            <option value="0" @selected(old('is_active', $client?->is_active ?? 1) == '0')>{{ __('Inactive') }}</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <x-input-label for="notes" :value="__('Notes')" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">{{ old('notes', $client?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>

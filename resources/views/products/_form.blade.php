@props(['product' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <x-input-label for="name" :value="__('Name *')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product?->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">{{ old('description', $product?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="unit_price" :value="__('Unit Price *')" />
        <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_price', $product?->unit_price)" required />
        <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="tax_rate" :value="__('Tax Rate (%)')" />
        <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('tax_rate', $product?->tax_rate ?? 0)" />
        <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category" :value="__('Category')" />
        <x-text-input id="category" name="category" type="text" class="mt-1 block w-full" :value="old('category', $product?->category)" placeholder="{{ __('e.g., Service, Hardware, Magic Dust') }}" />
        <x-input-error :messages="$errors->get('category')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="is_active" :value="__('Status')" />
        <select id="is_active" name="is_active" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">
            <option value="1" @selected(old('is_active', $product?->is_active ?? 1) == '1')>✦ {{ __('Active') }}</option>
            <option value="0" @selected(old('is_active', $product?->is_active ?? 1) == '0')>{{ __('Inactive') }}</option>
        </select>
    </div>
</div>

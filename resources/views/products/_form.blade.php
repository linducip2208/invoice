@props(['product' => null])

<div class="row g-3">
    <div class="col-12">
        <x-tabler.field :label="__('Name *')" name="name" type="text" :value="old('name', $product?->name)"
            attrs="required autofocus" />
    </div>

    <div class="col-12">
        <x-tabler.field :label="__('Description')" name="description">
            <textarea id="description" name="description" rows="3"
                class="form-control @error('description') is-invalid @enderror">{{ old('description', $product?->description) }}</textarea>
        </x-tabler.field>
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Unit Price *')" name="unit_price" type="number" :value="old('unit_price', $product?->unit_price)"
            attrs='required step="0.01" min="0"' />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Tax Rate (%)')" name="tax_rate" type="number" :value="old('tax_rate', $product?->tax_rate ?? 0)"
            attrs='step="0.01" min="0" max="100"' />
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Category')" name="category">
            <input type="text" id="category" name="category" value="{{ old('category', $product?->category) }}"
                placeholder="{{ __('e.g., Service, Hardware, Magic Dust') }}"
                class="form-control @error('category') is-invalid @enderror">
        </x-tabler.field>
    </div>

    <div class="col-md-6">
        <x-tabler.field :label="__('Status')" name="is_active">
            <select id="is_active" name="is_active" class="form-select">
                <option value="1" @selected(old('is_active', $product?->is_active ?? 1) == '1')>{{ __('Active') }}</option>
                <option value="0" @selected(old('is_active', $product?->is_active ?? 1) == '0')>{{ __('Inactive') }}</option>
            </select>
        </x-tabler.field>
    </div>
</div>

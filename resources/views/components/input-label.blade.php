@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-purple-700 mb-1.5 tracking-wide']) }}>
    {{ $value ?? $slot }}
</label>

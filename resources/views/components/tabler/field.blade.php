{{-- Labeled form control with validation state. Usage:
<x-tabler.field label="Name" name="name" :value="old('name')"> <input .../> </x-tabler.field>
or pass type for a default text input. --}}
@props(['label' => '', 'name' => '', 'help' => null, 'required' => false, 'type' => null, 'value' => null, 'attrs' => ''])
<div class="mb-3">
    @if($label)
        <label class="form-label{{ $required ? ' required' : '' }}" for="{{ $name }}">{{ $label }}</label>
    @endif
    @if($type)
        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}"
            class="form-control @error($name) is-invalid @enderror" {!! $attrs !!}>
    @else
        {{ $slot }}
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if($help)<small class="form-hint">{{ $help }}</small>@endif
</div>

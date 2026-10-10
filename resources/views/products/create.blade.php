<x-tabler.layout :title="__('Add Product')">
    <x-slot name="actions">
        <a href="{{ route('products.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Products') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add Product') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                @include('products._form')
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('products.index') }}" class="btn">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save Product') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

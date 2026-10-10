<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Add Product') }} <span class="text-2xl">✨</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Conjure a new product into existence') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Back to Products') }}</a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="card-unicorn">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                @include('products._form')
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-purple-100">
                    <a href="{{ route('products.index') }}" class="btn-unicorn-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>✦ {{ __('Save Product') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

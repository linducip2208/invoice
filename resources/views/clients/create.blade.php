<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Add Client') }} <span class="text-2xl">✨</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Welcome a new sparkle to your realm') }}</p>
            </div>
            <a href="{{ route('clients.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Back to Clients') }}</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="card-unicorn">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            <form method="POST" action="{{ route('clients.store') }}">
                @csrf
                @include('clients._form')
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-purple-100">
                    <a href="{{ route('clients.index') }}" class="btn-unicorn-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>✦ {{ __('Save Client') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

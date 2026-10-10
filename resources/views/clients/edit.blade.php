<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Edit Client') }} <span class="text-2xl">✏️</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ $client->name }}</p>
            </div>
            <a href="{{ route('clients.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Back to Clients') }}</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="card-unicorn">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            <form method="POST" action="{{ route('clients.update', $client) }}">
                @csrf @method('PUT')
                @include('clients._form', ['client' => $client])
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-purple-100">
                    <a href="{{ route('clients.index') }}" class="btn-unicorn-secondary">{{ __('Cancel') }}</a>
                    <x-primary-button>✦ {{ __('Update Client') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

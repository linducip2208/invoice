<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn">{{ $title ?? __('Coming Soon') }}</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card-unicorn-glow text-center py-16 sparkles-bg">
            <span class="text-7xl block animate-unicorn-float">🦄</span>
            <h3 class="font-display text-2xl font-bold text-purple-900 mt-5">{{ $title ?? __('Page') }}</h3>
            <p class="mt-3 text-sm text-purple-500 max-w-md mx-auto leading-relaxed">
                {{ __('This sparkly section is being conjured up by our magical engineers.') }}
            </p>
            <a href="{{ route('dashboard') }}" class="btn-unicorn mt-6">
                ← {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>
</x-app-layout>

<x-guest-layout>
    <div class="text-center py-10">
        <span class="text-7xl block animate-unicorn-float">🦄💥</span>
        <h1 class="mt-6 font-display text-4xl font-bold text-purple-900">500</h1>
        <p class="mt-2 text-lg font-semibold text-purple-700">{{ __('Something went wrong') }}</p>
        <p class="mt-2 text-sm text-purple-500 max-w-sm mx-auto">{{ __('Our unicorns tripped over a rainbow. The error has been logged — please try again in a moment.') }}</p>
        <div class="mt-6 flex items-center justify-center gap-3">
            <a href="/" class="btn-unicorn-secondary">🏠 {{ __('Home') }}</a>
            <a href="{{ route('dashboard') }}" class="btn-unicorn">✦ {{ __('Dashboard') }}</a>
        </div>
    </div>
</x-guest-layout>

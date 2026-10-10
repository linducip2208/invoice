<x-guest-layout>
    <div class="text-center py-10">
        <span class="text-7xl block animate-unicorn-float">⏳</span>
        <h1 class="mt-6 font-display text-4xl font-bold text-purple-900">419</h1>
        <p class="mt-2 text-lg font-semibold text-purple-700">{{ __('Page expired') }}</p>
        <p class="mt-2 text-sm text-purple-500 max-w-sm mx-auto">{{ __('Your session has expired for security. Please refresh and try again.') }}</p>
        <div class="mt-6 flex items-center justify-center gap-3">
            <a href="{{ url()->previous() }}" class="btn-unicorn-secondary">↩ {{ __('Go back') }}</a>
            <a href="{{ route('login') }}" class="btn-unicorn">✦ {{ __('Log in again') }}</a>
        </div>
    </div>
</x-guest-layout>

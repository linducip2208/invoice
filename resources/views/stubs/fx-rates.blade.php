<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">💱 {{ __('Multi-Currency Exchange Rates') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">{{ __('Auto-sync nilai tukar harian dari provider exchange rate (configurable).') }}</p>
        <form class="space-y-4">
            <div><label class="text-sm font-bold">{{ __('Exchange Rate Provider') }}</label>
                <input type="text" placeholder="https://api.exchangerate.host/latest" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
                <p class="text-xs text-purple-400 mt-1">{{ __('Endpoint apa saja yang return rate JSON. User input sendiri (no hardcoded provider).') }}</p>
            </div>
            <div><label class="text-sm font-bold">{{ __('API Key (opsional)') }}</label><input type="text" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-sm font-bold">{{ __('Base Currency') }}</label>
                <select class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"><option>IDR</option><option>USD</option></select>
            </div>
            <div><label class="text-sm font-bold">{{ __('Sync Frequency') }}</label>
                <select class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"><option>Daily (1 AM)</option><option>Hourly</option><option>Manual only</option></select>
            </div>
            <button type="button" disabled class="btn-unicorn opacity-50">💾 {{ __('Save (UI scaffold)') }}</button>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Generate Accounting Export') }} <span class="text-2xl">📊</span></h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('accounting-exports.store') }}" class="card-unicorn !p-8 space-y-5">
            @csrf
            <div>
                <x-input-label :value="__('Format Target *')" />
                <select name="format" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                    <option value="jurnal">Mekari Jurnal (Indonesia)</option>
                    <option value="mekari">Mekari Klikpajak</option>
                    <option value="quickbooks">QuickBooks (CSV)</option>
                    <option value="xero">Xero (CSV)</option>
                    <option value="csv_generic">CSV Generic (universal)</option>
                </select>
                <p class="text-xs text-purple-400 mt-1">Format CSV bisa di-import langsung ke aplikasi accounting target.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Periode Awal *')" />
                    <x-text-input name="period_start" type="date" :value="now()->startOfMonth()->format('Y-m-d')" required class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Periode Akhir *')" />
                    <x-text-input name="period_end" type="date" :value="now()->endOfMonth()->format('Y-m-d')" required class="mt-1 w-full" />
                </div>
            </div>
            <button type="submit" class="btn-unicorn w-full py-3">📊 {{ __('Generate Export') }}</button>
        </form>
    </div>
</x-app-layout>

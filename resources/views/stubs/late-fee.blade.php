<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">⏰ {{ __('Late Fee Settings') }}</h2></x-slot>
    <div class="max-w-2xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">{{ __('Otomatis tambah biaya keterlambatan ke invoice yang lewat jatuh tempo.') }}</p>
        <form class="space-y-4">
            <div><label class="text-sm font-bold">{{ __('Aktifkan Late Fee Otomatis') }}</label>
                <select class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"><option>Off</option><option>On</option></select>
            </div>
            <div><label class="text-sm font-bold">{{ __('Tipe Fee') }}</label>
                <select class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"><option value="percent">% per bulan</option><option value="fixed">Fixed amount</option></select>
            </div>
            <div><label class="text-sm font-bold">{{ __('Nilai Fee') }}</label>
                <input type="number" step="0.01" placeholder="2.00" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
            </div>
            <div><label class="text-sm font-bold">{{ __('Grace Period (hari)') }}</label>
                <input type="number" value="3" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
                <p class="text-xs text-purple-400 mt-1">{{ __('Jumlah hari setelah due date sebelum fee mulai berlaku.') }}</p>
            </div>
            <button type="button" disabled class="btn-unicorn opacity-50">💾 {{ __('Save (UI scaffold — backend job belum aktif)') }}</button>
        </form>
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            ⏳ {{ __('Job scheduled `invoices:apply-late-fees` belum di-wire. Saat live, akan jalan harian dan add line item ke invoice overdue.') }}
        </div>
    </div>
</x-app-layout>

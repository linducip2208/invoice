<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🏛️ {{ __('Indonesia Tax (e-Faktur, PPh, PPN)') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">{{ __('Konfigurasi pajak sesuai standar Indonesia.') }}</p>
        <form class="space-y-4">
            <div><label class="text-sm font-bold">{{ __('NPWP Workspace') }}</label><input type="text" placeholder="00.000.000.0-000.000" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-sm font-bold">{{ __('PPN Default') }}</label>
                <select class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
                    <option value="0">0% (non-PKP)</option>
                    <option value="11" selected>11% (PKP standar)</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-bold">{{ __('PPh Withholding (jika applicable)') }}</label>
                <div class="grid grid-cols-2 gap-2 mt-1 text-sm">
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox"> PPh 21 (jasa pribadi)</label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox"> PPh 23 (2% jasa)</label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox"> PPh 4(2) final</label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox"> PPh 26 (luar negeri)</label>
                </div>
            </div>
            <div><label class="text-sm font-bold">{{ __('Format Nomor e-Faktur') }}</label><input type="text" placeholder="010.000-25.00000001" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full font-mono"></div>
            <button type="button" disabled class="btn-unicorn opacity-50">💾 {{ __('Save (UI scaffold)') }}</button>
        </form>
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            ⏳ {{ __('Coretax / e-Faktur API integration untuk B2B PKP — coming next release.') }}
        </div>
    </div>
</x-app-layout>

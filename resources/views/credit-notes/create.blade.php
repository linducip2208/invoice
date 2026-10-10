<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Credit Note Baru') }} <span class="text-2xl">✨</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Refund / koreksi tagihan invoice') }}</p>
            </div>
            <a href="{{ route('credit-notes.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('credit-notes.store') }}" class="card-unicorn !p-8 space-y-5">
            @csrf

            <div>
                <x-input-label :value="__('Invoice yang Dikoreksi *')" />
                <select name="invoice_id" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                    <option value="">{{ __('Pilih invoice…') }}</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}"
                                data-balance="{{ $inv->balance_due }}"
                                data-currency="{{ $inv->currency }}"
                                {{ ($invoice && $invoice->id === $inv->id) || old('invoice_id') == $inv->id ? 'selected' : '' }}>
                            {{ $inv->invoice_no }} — {{ $inv->client?->name }} ({{ $inv->currency }} {{ number_format($inv->balance_due, 0) }} sisa)
                        </option>
                    @endforeach
                </select>
                @error('invoice_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Jumlah Kredit *')" />
                    <x-text-input name="amount" type="number" step="0.01" min="0.01" :value="old('amount')" required class="mt-1 w-full" />
                    @error('amount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label :value="__('Mata Uang *')" />
                    <select name="currency" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        @foreach(['IDR','USD','EUR','SGD','GBP'] as $c)
                            <option value="{{ $c }}" {{ old('currency', $invoice?->currency ?? 'IDR') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <x-input-label :value="__('Alasan Kredit *')" />
                <x-text-input name="reason" :value="old('reason')" required class="mt-1 w-full"
                              placeholder="Mis. Refund DP karena project dibatalkan" />
                @error('reason') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-input-label :value="__('Tanggal Issued *')" />
                <x-text-input name="issued_at" type="date" :value="old('issued_at', now()->format('Y-m-d'))" required class="mt-1" />
                @error('issued_at') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-input-label :value="__('Catatan Tambahan')" />
                <textarea name="notes" rows="3" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full" placeholder="{{ __('Detail internal...') }}">{{ old('notes') }}</textarea>
            </div>

            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-xs text-amber-800">
                <p class="font-bold mb-1">⚠️ Penting</p>
                <ul class="space-y-1">
                    <li>• Credit note dibuat sebagai <strong>Draft</strong>. Issue dulu, baru bisa diapply ke invoice.</li>
                    <li>• Jumlah tidak boleh melebihi sisa tagihan invoice.</li>
                    <li>• Saat di-apply, sisa tagihan invoice otomatis berkurang.</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('credit-notes.index') }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                <button type="submit" class="btn-unicorn px-8">✦ {{ __('Buat Credit Note') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>

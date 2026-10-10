<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Quick Payment') }} <span class="text-2xl">💸</span></h2>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <form method="POST" action="{{ route('mobile.quick-payment.store') }}" class="card-unicorn !p-6 space-y-5">
            @csrf
            <p class="text-sm text-purple-500">Klien transfer? Catat cepat dari sini.</p>

            <div>
                <x-input-label :value="__('Invoice yang Dibayar *')" />
                <select name="invoice_id" required class="input-unicorn rounded-xl px-4 py-3 mt-1 block w-full text-base">
                    <option value="">— Pilih invoice unpaid —</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" data-balance="{{ $inv->balance_due }}">
                            {{ $inv->invoice_no }} · {{ $inv->client?->name }} · {{ number_format($inv->balance_due, 0) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label :value="__('Jumlah Diterima *')" />
                <x-text-input name="amount" type="number" step="0.01" min="0.01" required class="mt-1 w-full text-lg font-bold" placeholder="500000" />
            </div>

            <div>
                <x-input-label :value="__('Metode')" />
                <select name="method" required class="input-unicorn rounded-xl px-4 py-3 mt-1 block w-full text-base">
                    <option value="bank_transfer">🏦 Transfer Bank</option>
                    <option value="qris">📱 QRIS</option>
                    <option value="cash">💵 Tunai</option>
                    <option value="card">💳 Kartu</option>
                    <option value="ewallet">💰 E-Wallet</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <button type="submit" class="btn-unicorn w-full py-4 text-lg">💸 {{ __('Catat Pembayaran') }}</button>
        </form>
    </div>
</x-app-layout>

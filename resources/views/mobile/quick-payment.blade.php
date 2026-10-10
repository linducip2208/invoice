<x-tabler.layout :title="__('Quick Payment')" :pretitle="__('Mobile')">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Quick Payment') }}</h3>
                    <div class="card-subtitle">{{ __('Klien transfer? Catat cepat dari sini.') }}</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('mobile.quick-payment.store') }}">
                        @csrf
                        <x-tabler.field :label="__('Invoice yang Dibayar *')" name="invoice_id" :required="true">
                            <select id="invoice_id" name="invoice_id" required class="form-select form-select-lg">
                                <option value="">— {{ __('Pilih invoice unpaid') }} —</option>
                                @foreach($invoices as $inv)
                                    <option value="{{ $inv->id }}" data-balance="{{ $inv->balance_due }}">
                                        {{ $inv->invoice_no }} · {{ $inv->client?->name }} · {{ number_format($inv->balance_due, 0) }}
                                    </option>
                                @endforeach
                            </select>
                        </x-tabler.field>

                        <x-tabler.field :label="__('Jumlah Diterima *')" name="amount" :required="true">
                            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required placeholder="500000" class="form-control form-control-lg fw-bold">
                        </x-tabler.field>

                        <x-tabler.field :label="__('Metode')" name="method">
                            <select id="method" name="method" required class="form-select form-select-lg">
                                <option value="bank_transfer">{{ __('Transfer Bank') }}</option>
                                <option value="qris">QRIS</option>
                                <option value="cash">{{ __('Tunai') }}</option>
                                <option value="card">{{ __('Kartu') }}</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="other">Other</option>
                            </select>
                        </x-tabler.field>

                        <button type="submit" class="btn btn-primary w-100 btn-lg"><x-ti name="cash" /> {{ __('Catat Pembayaran') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

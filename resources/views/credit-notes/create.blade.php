<x-tabler.layout :title="__('Credit Note Baru')" :pretitle="__('Refund / koreksi tagihan invoice')">
    <x-slot name="actions">
        <a href="{{ route('credit-notes.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali') }}
        </a>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('credit-notes.store') }}" class="card" data-prevent-double-submit>
                @csrf
                <div class="card-body">
                    <x-tabler.field :label="__('Invoice yang Dikoreksi *')" name="invoice_id">
                        <select id="invoice_id" name="invoice_id" required class="form-select @error('invoice_id') is-invalid @enderror">
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
                    </x-tabler.field>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-tabler.field :label="__('Jumlah Kredit *')" name="amount" type="number" :value="old('amount')" attrs="step=0.01 min=0.01 required" />
                        </div>
                        <div class="col-md-6">
                            <x-tabler.field :label="__('Mata Uang *')" name="currency">
                                <select id="currency" name="currency" required class="form-select">
                                    @foreach(['IDR','USD','EUR','SGD','GBP'] as $c)
                                        <option value="{{ $c }}" {{ old('currency', $invoice?->currency ?? 'IDR') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </x-tabler.field>
                        </div>
                    </div>

                    <x-tabler.field :label="__('Alasan Kredit *')" name="reason">
                        <input type="text" id="reason" name="reason" value="{{ old('reason') }}" required
                            class="form-control @error('reason') is-invalid @enderror"
                            placeholder="Mis. Refund DP karena project dibatalkan">
                    </x-tabler.field>

                    <x-tabler.field :label="__('Tanggal Issued *')" name="issued_at" type="date" :value="old('issued_at', now()->format('Y-m-d'))" attrs="required" />

                    <x-tabler.field :label="__('Catatan Tambahan')" name="notes">
                        <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="{{ __('Detail internal...') }}">{{ old('notes') }}</textarea>
                    </x-tabler.field>

                    <div class="rounded bg-yellow-lt border p-3 small text-yellow mb-3">
                        <p class="fw-bold mb-1">{{ __('Penting') }}</p>
                        <ul class="mb-0">
                            <li>Credit note dibuat sebagai <strong>Draft</strong>. Issue dulu, baru bisa diapply ke invoice.</li>
                            <li>Jumlah tidak boleh melebihi sisa tagihan invoice.</li>
                            <li>Saat di-apply, sisa tagihan invoice otomatis berkurang.</li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="{{ route('credit-notes.index') }}" class="btn">{{ __('Batal') }}</a>
                        <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Buat Credit Note') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

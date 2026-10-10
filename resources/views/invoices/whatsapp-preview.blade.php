<x-tabler.layout :title="__('Kirim via WhatsApp')" :pretitle="__('Invoice') . ' ' . $invoice->invoice_no">
    <x-slot name="actions">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali') }}
        </a>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('invoices.whatsapp.send', $invoice) }}" class="card" data-prevent-double-submit>
                @csrf
                <div class="card-body">
                    <x-tabler.field :label="__('Nomor WhatsApp')" name="to_phone" :help="__('Format Indonesia: 08xxx atau +62. Akan otomatis di-normalize.')">
                        <input type="text" id="to_phone" name="to_phone" value="{{ $invoice->client?->phone }}" required
                            class="form-control font-monospace @error('to_phone') is-invalid @enderror" placeholder="08123456789 atau +6281234...">
                    </x-tabler.field>

                    <x-tabler.field :label="__('Isi Pesan')" name="message" :help="__('WhatsApp formatting: *tebal*, _miring_, ~coret~')">
                        <textarea id="message" name="message" rows="10" required maxlength="4000"
                                  class="form-control font-monospace @error('message') is-invalid @enderror">{{ $defaultMessage }}</textarea>
                    </x-tabler.field>

                    <div class="rounded bg-green-lt border p-3 small text-green mb-3">
                        <p class="fw-bold mb-1">{{ __('Akan terkirim ke:') }}</p>
                        <p class="mb-0">{{ $invoice->client?->name }} ({{ $invoice->client?->company_name ?: '—' }})</p>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn">{{ __('Batal') }}</a>
                        <button type="submit" class="btn btn-primary"><x-ti name="brand-whatsapp" /> {{ __('Kirim WhatsApp') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

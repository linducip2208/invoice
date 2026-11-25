<x-tabler.layout :title="__('Kirim via WhatsApp')" :pretitle="__('Invoice') . ' ' . $invoice->invoice_no">
    <x-slot name="actions">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali') }}
        </a>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            @php $hour = (int) now()->setTimezone('Asia/Jakarta')->format('G'); @endphp
            @if($hour < 8 || $hour >= 17)
                <div class="alert alert-warning" role="alert">
                    {{ __('Di luar jam kerja (08:00–17:00 WIB). Pesan tetap bisa dikirim, tapi respons klien kemungkinan besok pagi.') }}
                </div>
            @endif
            <form method="POST" action="{{ route('invoices.whatsapp.send', $invoice) }}" class="card" data-prevent-double-submit>
                @csrf
                <div class="card-body">
                    <x-tabler.field :label="__('Nomor WhatsApp')" name="to_phone" :help="__('Format Indonesia: 08xxx atau +62. Akan otomatis di-normalize.')">
                        <input type="text" id="to_phone" name="to_phone" value="{{ $invoice->client?->phone }}" required
                            class="form-control font-monospace @error('to_phone') is-invalid @enderror" placeholder="08123456789 atau +6281234...">
                    </x-tabler.field>

                    <x-tabler.field :label="__('Template Pesan')" name="tpl">
                        <select id="tpl" class="form-select">
                            @foreach(['tagihan_baru' => __('Tagihan baru'), 'pengingat_h3' => __('Pengingat H-3'), 'overdue' => __('Overdue / menunggak'), 'kwitansi' => __('Kwitansi lunas')] as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
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
@push('scripts')
<script>
(function () {
    var tpl = document.getElementById('tpl');
    var msg = document.getElementById('message');
    var all = @json($templates);
    if (tpl && msg) tpl.addEventListener('change', function () {
        if (all[tpl.value]) msg.value = all[tpl.value];
    });
})();
</script>
@endpush

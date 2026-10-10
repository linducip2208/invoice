<x-tabler.layout :title="__('QRIS Pembayaran')" :pretitle="__('Invoice') . ' ' . $invoice->invoice_no">
    <x-slot name="actions">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali') }}
        </a>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body text-center p-4">
                    @if($qris->status === 'paid')
                        <div class="empty">
                            <div class="empty-icon"><x-ti name="check" class="icon icon-lg text-success" /></div>
                            <p class="empty-title">Sudah Dibayar</p>
                            @if($qris->paid_at)
                                <p class="empty-subtitle text-muted">{{ $qris->paid_at->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                    @elseif($qris->status === 'cancelled' || $qris->isExpired())
                        <div class="empty">
                            <div class="empty-icon"><x-ti name="clock" class="icon icon-lg text-danger" /></div>
                            <p class="empty-title">{{ $qris->isExpired() ? 'QRIS Kadaluarsa' : 'QRIS Dibatalkan' }}</p>
                            <div class="empty-action">
                                <form method="POST" action="{{ route('invoices.qris.generate', $invoice) }}" class="d-inline">@csrf
                                    <button type="submit" class="btn btn-primary"><x-ti name="refresh" /> {{ __('Generate Baru') }}</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <p class="subheader mb-2">{{ __('Scan untuk Bayar') }}</p>
                        <p class="h1 mb-1">{{ number_format($qris->amount, 0) }}</p>
                        <p class="text-muted mb-4">{{ $invoice->currency }}</p>

                        <div class="d-inline-block bg-white p-4 rounded border border-4">
                            <img src="{{ asset('storage/' . $qris->qr_image_path) }}" alt="QRIS" class="img-fluid mx-auto" style="width: 16rem; height: 16rem;">
                        </div>

                        <p class="small text-muted mt-4 mb-1">QRIS · Berlaku sampai <strong>{{ $qris->expires_at->format('H:i, d M Y') }}</strong></p>
                        <p class="small text-muted mt-1">{{ __('Sisa') }} <span class="fw-bold" x-data x-init="
                            setInterval(() => {
                                const exp = new Date('{{ $qris->expires_at->toIso8601String() }}');
                                const now = new Date();
                                const ms = exp - now;
                                if (ms <= 0) { $el.textContent = 'expired'; return; }
                                const m = Math.floor(ms / 60000), s = Math.floor((ms % 60000) / 1000);
                                $el.textContent = m + 'm ' + s + 's';
                            }, 1000);
                        ">—</span></p>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('invoices.qris.cancel', $qris) }}">@csrf
                                <button type="submit" class="btn btn-sm btn-link text-danger">{{ __('Batalkan QR') }}</button>
                            </form>
                        </div>

                        <div class="mt-4 text-start p-3 rounded bg-blue-lt border">
                            <p class="fw-bold text-blue mb-2">{{ __('Cara bayar:') }}</p>
                            <ol class="small text-blue mb-0">
                                <li>Buka aplikasi mobile banking / e-wallet (BCA, Mandiri, BNI, BRI, GoPay, OVO, Dana, ShopeePay, dll)</li>
                                <li>Pilih menu <strong>Scan / Bayar QRIS</strong></li>
                                <li>Scan QR di atas</li>
                                <li>Pastikan nominal {{ $invoice->currency }} {{ number_format($qris->amount, 0) }} sesuai, lalu konfirmasi</li>
                            </ol>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

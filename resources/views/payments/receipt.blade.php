<x-tabler.guest :title="__('Kwitansi') . ' ' . ($payment->reference_no ?: $payment->id)">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    @if(! empty($workspace->logo_path))
                        <img src="{{ asset('storage/' . $workspace->logo_path) }}" alt="Logo" style="height:2.5rem;">
                    @endif
                    <div class="h3 mb-0">{{ $workspace->name ?? config('app.name') }}</div>
                    <div class="text-muted small">{{ $workspace->email ?? '' }} {{ $workspace->phone ?? '' }}</div>
                </div>
                <div class="text-end">
                    <div class="subheader">{{ __('KWITANSI') }}</div>
                    <div class="fw-bold">{{ $payment->reference_no ?: 'PAY-' . $payment->id }}</div>
                </div>
            </div>
            <dl class="row g-2 mb-0">
                <dt class="col-5 text-muted small">{{ __('Telah terima dari') }}</dt>
                <dd class="col-7 fw-semibold">{{ $payment->invoice?->client?->name ?? '—' }}</dd>
                <dt class="col-5 text-muted small">{{ __('Jumlah') }}</dt>
                <dd class="col-7 h3 mb-0">{{ $payment->invoice?->currency }} {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                <dt class="col-5 text-muted small">{{ __('Untuk invoice') }}</dt>
                <dd class="col-7">{{ $payment->invoice?->invoice_no ?? '—' }}</dd>
                <dt class="col-5 text-muted small">{{ __('Tanggal') }}</dt>
                <dd class="col-7">{{ $payment->payment_date?->format('d M Y') }}</dd>
                <dt class="col-5 text-muted small">{{ __('Metode') }}</dt>
                <dd class="col-7">{{ ucwords(str_replace(['_','-'], ' ', $payment->payment_method)) }}</dd>
                @if($payment->notes)
                    <dt class="col-5 text-muted small">{{ __('Catatan') }}</dt>
                    <dd class="col-7">{{ $payment->notes }}</dd>
                @endif
            </dl>
            <div class="row mt-4">
                <div class="col-6"></div>
                <div class="col-6 text-center">
                    <div class="text-muted small">{{ $workspace->city ?? '' }}, {{ now()->format('d M Y') }}</div>
                    <div class="mt-5 pt-2 border-top fw-semibold">{{ $workspace->name ?? '' }}</div>
                </div>
            </div>
            <div class="d-print-none mt-3 d-flex gap-2 justify-content-end">
                <button class="btn btn-primary" onclick="window.print()"><x-ti name="printer" /> {{ __('Print / PDF') }}</button>
            </div>
        </div>
    </div>
</x-tabler.guest>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('QRIS Pembayaran') }} <span class="text-2xl">📱</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ Invoice <strong>{{ $invoice->invoice_no }}</strong></p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}" class="btn-unicorn-secondary text-sm">← {{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card-unicorn-glow !p-10 text-center">
            @if($qris->status === 'paid')
                <p class="text-6xl mb-4 animate-unicorn-float">✓</p>
                <h3 class="font-display text-2xl font-bold text-emerald-700">Sudah Dibayar</h3>
                @if($qris->paid_at)
                    <p class="text-sm text-emerald-600 mt-1">{{ $qris->paid_at->format('d M Y, H:i') }}</p>
                @endif
            @elseif($qris->status === 'cancelled' || $qris->isExpired())
                <p class="text-5xl mb-4">⏰</p>
                <h3 class="font-display text-xl font-bold text-rose-700">{{ $qris->isExpired() ? 'QRIS Kadaluarsa' : 'QRIS Dibatalkan' }}</h3>
                <form method="POST" action="{{ route('invoices.qris.generate', $invoice) }}" class="mt-4">@csrf
                    <button type="submit" class="btn-unicorn">↻ {{ __('Generate Baru') }}</button>
                </form>
            @else
                <p class="text-xs uppercase tracking-[0.2em] text-purple-400 mb-2">{{ __('Scan untuk Bayar') }}</p>
                <p class="font-display text-4xl font-bold text-rainbow mb-1">{{ number_format($qris->amount, 0) }}</p>
                <p class="text-sm text-purple-500 mb-6">{{ $invoice->currency }}</p>

                <div class="inline-block bg-white p-6 rounded-3xl border-4 border-purple-200 shadow-unicorn-glow">
                    <img src="{{ asset('storage/' . $qris->qr_image_path) }}" alt="QRIS" class="w-64 h-64 mx-auto">
                </div>

                <p class="text-xs text-purple-400 mt-4">QRIS · Berlaku sampai <strong>{{ $qris->expires_at->format('H:i, d M Y') }}</strong></p>
                <p class="text-xs text-purple-400 mt-1">⏰ Sisa <span class="font-bold" x-data x-init="
                    setInterval(() => {
                        const exp = new Date('{{ $qris->expires_at->toIso8601String() }}');
                        const now = new Date();
                        const ms = exp - now;
                        if (ms <= 0) { $el.textContent = 'expired'; return; }
                        const m = Math.floor(ms / 60000), s = Math.floor((ms % 60000) / 1000);
                        $el.textContent = m + 'm ' + s + 's';
                    }, 1000);
                ">—</span></p>

                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <form method="POST" action="{{ route('invoices.qris.cancel', $qris) }}">@csrf
                        <button type="submit" class="text-xs text-rose-500 font-semibold hover:underline">✗ Batalkan QR</button>
                    </form>
                </div>

                <div class="mt-8 text-left p-5 rounded-2xl bg-blue-50/60 border border-blue-200">
                    <p class="font-bold text-blue-900 text-sm mb-2">📱 Cara bayar:</p>
                    <ol class="text-sm text-blue-800 space-y-1">
                        <li>1. Buka aplikasi mobile banking / e-wallet (BCA, Mandiri, BNI, BRI, GoPay, OVO, Dana, ShopeePay, dll)</li>
                        <li>2. Pilih menu <strong>Scan / Bayar QRIS</strong></li>
                        <li>3. Scan QR di atas</li>
                        <li>4. Pastikan nominal {{ $invoice->currency }} {{ number_format($qris->amount, 0) }} sesuai, lalu konfirmasi</li>
                    </ol>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

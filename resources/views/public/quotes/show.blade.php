<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quote {{ $quote->quote_no }} — {{ $quote->workspace?->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=quicksand:400,500,600,700|comfortaa:600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js" defer></script>
</head>
<body class="font-sans" style="background: linear-gradient(135deg, #FFF8FE 0%, #FFE4F1 50%, #EDE9FE 100%); min-height: 100vh;">

<div class="max-w-4xl mx-auto px-4 py-8">

    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800 mb-4">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800 mb-4">⚠️ {{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="card-unicorn-glow !p-8 mb-6">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-purple-400 mb-1">{{ __('Penawaran dari') }}</p>
                <h1 class="font-display text-2xl font-bold text-purple-900">{{ $quote->workspace?->name }}</h1>
                @if($quote->workspace?->address)
                    <p class="text-sm text-purple-500 mt-1">{{ $quote->workspace->address }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-xs uppercase tracking-[0.2em] text-purple-400 mb-1">{{ __('No') }}</p>
                <p class="font-display text-2xl font-bold text-rainbow">{{ $quote->quote_no }}</p>
                @php
                    $statusBadge = match($quote->status) {
                        'accepted','converted' => ['emerald-100','emerald-700','✓ Diterima'],
                        'rejected'             => ['rose-100','rose-700','✗ Ditolak'],
                        'expired'              => ['gray-100','gray-600','— Kadaluarsa'],
                        'sent'                 => ['sky-100','sky-700','📤 Terkirim'],
                        default                => ['amber-100','amber-700','⏳ Draft'],
                    };
                @endphp
                <span class="inline-block mt-2 text-xs px-3 py-1 rounded-full bg-{{ $statusBadge[0] }} text-{{ $statusBadge[1] }} font-bold">{{ $statusBadge[2] }}</span>
            </div>
        </div>

        <hr class="border-purple-100 my-6">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-purple-400 mb-1">{{ __('Untuk') }}</p>
                <p class="font-display font-bold text-purple-900 text-lg">{{ $quote->client?->company_name ?: $quote->client?->name }}</p>
                @if($quote->client?->company_name)<p class="text-sm text-purple-600">{{ $quote->client->name }}</p>@endif
                @if($quote->client?->email)<p class="text-sm text-purple-500">{{ $quote->client->email }}</p>@endif
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-purple-400 mb-1">{{ __('Detail') }}</p>
                <p class="text-sm text-purple-700">📅 Issued: <strong>{{ $quote->issue_date->format('d M Y') }}</strong></p>
                @if($quote->expiry_date)
                    <p class="text-sm text-purple-700">⏰ Expires: <strong>{{ $quote->expiry_date->format('d M Y') }}</strong></p>
                @endif
                <p class="text-sm text-purple-700">💱 Mata uang: <strong>{{ $quote->currency }}</strong></p>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="card-unicorn !p-0 overflow-hidden mb-6">
        <div class="p-5 border-b border-purple-100">
            <h2 class="font-display font-bold text-lg text-purple-900">📋 {{ __('Detail Pekerjaan') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Deskripsi</th>
                        <th class="px-4 py-2 text-right text-[11px] font-bold text-purple-700 uppercase w-20">Qty</th>
                        <th class="px-4 py-2 text-right text-[11px] font-bold text-purple-700 uppercase w-32">Harga</th>
                        <th class="px-4 py-2 text-right text-[11px] font-bold text-purple-700 uppercase w-32">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                        <tr class="border-t border-purple-100">
                            <td class="px-4 py-3 text-purple-900">{{ $item['description'] }}</td>
                            <td class="px-4 py-3 text-right text-purple-700">{{ number_format($item['quantity'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-purple-700">{{ number_format($item['unit_price'], 0) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-purple-900">{{ number_format($item['quantity'] * $item['unit_price'], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-5 bg-gradient-to-br from-pink-50/40 to-purple-50/40 border-t border-purple-100">
            <div class="flex justify-end">
                <div class="w-full max-w-xs space-y-1.5 text-sm">
                    <div class="flex justify-between"><span class="text-purple-500">Subtotal</span><span class="text-purple-900 font-semibold">{{ number_format($quote->subtotal, 0) }}</span></div>
                    @if($quote->tax_amount > 0)
                        <div class="flex justify-between"><span class="text-purple-500">PPN ({{ number_format($quote->tax_rate, 0) }}%)</span><span class="text-purple-900">{{ number_format($quote->tax_amount, 0) }}</span></div>
                    @endif
                    <div class="flex justify-between border-t border-purple-200 pt-2 text-base">
                        <span class="font-display font-bold text-purple-900">Total</span>
                        <span class="font-display font-bold text-rainbow">{{ number_format($quote->total, 0) }} {{ $quote->currency }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($quote->notes || $quote->terms)
        <div class="card-unicorn !p-6 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($quote->notes)
                <div>
                    <p class="text-xs uppercase tracking-wider text-purple-400 mb-1">📝 {{ __('Catatan') }}</p>
                    <p class="text-sm text-purple-700 whitespace-pre-line">{{ $quote->notes }}</p>
                </div>
            @endif
            @if($quote->terms)
                <div>
                    <p class="text-xs uppercase tracking-wider text-purple-400 mb-1">📜 {{ __('Syarat & Ketentuan') }}</p>
                    <p class="text-sm text-purple-700 whitespace-pre-line">{{ $quote->terms }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Action: sign or already signed --}}
    @if($isSigned)
        <div class="card-unicorn-glow !p-8 mb-6 sparkles-bg">
            <p class="text-3xl mb-3 text-center">✓</p>
            <h3 class="font-display font-bold text-xl text-emerald-700 text-center">Quote sudah ditandatangani</h3>
            @foreach($signatures as $sig)
                <div class="mt-5 p-4 bg-white rounded-2xl border border-emerald-200">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="font-bold text-purple-900">{{ $sig->signer_name }}</p>
                            <p class="text-xs text-purple-500">{{ $sig->signer_email }} · {{ $sig->signed_at->format('d M Y, H:i') }}</p>
                        </div>
                        @if($sig->signature_image_path)
                            <img src="{{ asset('storage/' . $sig->signature_image_path) }}" alt="Signature" class="max-h-16 bg-white">
                        @elseif($sig->signature_typed)
                            <p class="font-bold italic text-2xl text-purple-700" style="font-family: cursive;">{{ $sig->signature_typed }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(in_array($quote->status, ['draft','sent']))
        <div class="card-unicorn !p-8 mb-6" x-data="signaturePad()">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-2">✍️ {{ __('Setujui Penawaran') }}</h3>
            <p class="text-sm text-purple-600 mb-5">Tanda tangan untuk menerima quote ini. Setelah ditandatangani, akan otomatis dikonversi ke invoice.</p>

            <form method="POST" action="{{ route('public.quote.sign', $quote->public_token) }}" @submit="onSubmit($event)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-purple-700 mb-1">Nama *</label>
                        <input type="text" name="signer_name" required maxlength="120"
                               value="{{ old('signer_name', $quote->client?->name) }}"
                               class="input-unicorn rounded-xl px-4 py-2 w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-purple-700 mb-1">Email *</label>
                        <input type="email" name="signer_email" required
                               value="{{ old('signer_email', $quote->client?->email) }}"
                               class="input-unicorn rounded-xl px-4 py-2 w-full">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="flex gap-2 mb-2">
                        <button type="button" @click="mode = 'draw'" :class="mode === 'draw' ? 'btn-unicorn' : 'btn-unicorn-secondary'" class="text-xs">✏️ Gambar</button>
                        <button type="button" @click="mode = 'type'" :class="mode === 'type' ? 'btn-unicorn' : 'btn-unicorn-secondary'" class="text-xs">⌨️ Ketik Nama</button>
                    </div>

                    <div x-show="mode === 'draw'">
                        <div class="rounded-2xl border-2 border-purple-200 bg-white overflow-hidden">
                            <canvas x-ref="canvas" class="block w-full" style="height: 200px; touch-action: none;"></canvas>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs text-purple-400">Tarik untuk gambar tanda tangan</p>
                            <button type="button" @click="clear()" class="text-xs text-rose-500 font-semibold hover:underline">🗑️ Bersihkan</button>
                        </div>
                        <input type="hidden" name="signature_data" :value="signatureData">
                    </div>

                    <div x-show="mode === 'type'" class="rounded-2xl border-2 border-purple-200 bg-white p-6">
                        <input type="text" name="signature_typed" maxlength="120" placeholder="Ketik nama lengkapmu"
                               class="w-full text-3xl text-center bg-transparent border-none focus:ring-0 italic text-purple-700"
                               style="font-family: 'Brush Script MT', cursive;">
                    </div>
                </div>

                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 mb-5">
                    ℹ️ Dengan menandatangani, kamu menyatakan setuju dengan terms quote ini. IP & timestamp akan tercatat untuk verifikasi.
                </div>

                <div class="flex items-center justify-end gap-3">
                    <form method="POST" action="{{ route('public.quote.reject', $quote->public_token) }}" onsubmit="return confirm('Yakin tolak quote ini?')" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-rose-500 font-semibold hover:underline">✗ Tolak Quote</button>
                    </form>
                    <button type="submit" class="btn-unicorn px-8">✦ Tanda Tangan & Setujui</button>
                </div>
            </form>
        </div>
    @else
        <div class="card-unicorn !p-6 text-center text-purple-500">
            Quote ini sudah {{ $quote->status }}.
        </div>
    @endif

    <p class="text-center text-xs text-purple-400 mt-8">
        Powered by <a href="/" class="font-bold text-rainbow">Invocely</a>
    </p>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function signaturePad() {
        return {
            mode: 'draw',
            pad: null,
            signatureData: '',
            init() {
                this.$nextTick(() => this.initPad());
            },
            initPad() {
                if (typeof SignaturePad === 'undefined') {
                    setTimeout(() => this.initPad(), 100);
                    return;
                }
                const canvas = this.$refs.canvas;
                const resize = () => {
                    const ratio = window.devicePixelRatio || 1;
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    if (this.pad) this.pad.clear();
                };
                window.addEventListener('resize', resize);
                resize();
                this.pad = new SignaturePad(canvas, {
                    penColor: '#7C3AED',
                    backgroundColor: 'rgba(0,0,0,0)',
                });
            },
            clear() {
                if (this.pad) this.pad.clear();
                this.signatureData = '';
            },
            onSubmit(e) {
                if (this.mode === 'draw') {
                    if (this.pad && !this.pad.isEmpty()) {
                        this.signatureData = this.pad.toDataURL('image/png');
                    } else {
                        e.preventDefault();
                        alert('Silakan gambar tanda tangan, atau pilih mode "Ketik Nama".');
                        return false;
                    }
                }
            }
        };
    }
</script>
</body>
</html>

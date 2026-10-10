<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">💸 {{ __('Pembayaran Manual') }}</h2>
        <p class="text-sm text-purple-500 mt-1">✦ {{ __('Transfer manual & upload bukti — admin verifikasi 1×24 jam') }}</p>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @php
            $plan = $subscription->plan;
            $amount = $plan->priceFor($subscription->currency, $subscription->billing_period);
            $amountFormatted = $subscription->currency === 'IDR'
                ? 'Rp ' . number_format($amount, 0, ',', '.')
                : '$ ' . number_format($amount, 2);
        @endphp

        <!-- Order summary -->
        <div class="glass-card p-6">
            <p class="text-xs text-purple-400 font-bold uppercase tracking-wider">{{ __('Order Summary') }}</p>
            <div class="mt-3 flex items-center justify-between">
                <div>
                    <p class="font-display text-2xl font-bold text-purple-900">{{ $plan->name }}</p>
                    <p class="text-sm text-purple-500">{{ ucfirst($subscription->billing_period) }} · {{ $subscription->currency }}</p>
                </div>
                <p class="font-display text-3xl font-bold heading-gradient">{{ $amountFormatted }}</p>
            </div>
        </div>

        <!-- Payment instructions -->
        <div class="glass-card p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">📍 {{ __('Transfer ke salah satu metode di bawah') }}</h3>
            @if($methods->isEmpty())
                <p class="text-sm text-purple-500">{{ __('Belum ada metode pembayaran manual yang dikonfigurasi superadmin. Hubungi support.') }}</p>
            @else
                <div class="space-y-3">
                    @foreach($methods as $m)
                        <div class="rounded-2xl p-4 border-2 border-purple-100 bg-white/50">
                            <div class="flex items-start gap-3">
                                <span class="text-3xl">
                                    @switch($m->type)
                                        @case('bank') 🏦 @break
                                        @case('ewallet') 📱 @break
                                        @case('crypto') ₿ @break
                                        @default 💳 @endswitch
                                </span>
                                <div class="flex-1">
                                    <p class="font-bold text-purple-900">{{ $m->label }}</p>
                                    @if($m->bank_or_provider)
                                        <p class="text-sm text-purple-500">{{ $m->bank_or_provider }}</p>
                                    @endif
                                    <div class="mt-2 grid grid-cols-2 gap-3 text-sm">
                                        @if($m->account_number)
                                        <div>
                                            <p class="text-xs text-purple-400">{{ __('No. Rekening / Akun') }}</p>
                                            <code class="font-mono font-bold text-purple-900 select-all">{{ $m->account_number }}</code>
                                        </div>
                                        @endif
                                        @if($m->account_name)
                                        <div>
                                            <p class="text-xs text-purple-400">{{ __('A.N.') }}</p>
                                            <p class="font-bold text-purple-900">{{ $m->account_name }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @if($m->instructions)
                                        <p class="mt-2 text-xs text-purple-600 whitespace-pre-line">{{ $m->instructions }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                ⚠️ {{ __('Transfer tepat sebesar') }} <strong>{{ $amountFormatted }}</strong> {{ __('dengan note "SUB-' . $subscription->id . '" supaya verifikasi cepat.') }}
            </div>
        </div>

        <!-- Upload proof -->
        <div class="glass-card p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">📤 {{ __('Upload Bukti Transfer') }}</h3>
            <form method="POST" action="{{ route('billing.manual.proof', $subscription) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-bold text-purple-700">{{ __('Screenshot / foto bukti transfer *') }}</label>
                    <input type="file" name="proof" accept="image/*" required class="block w-full text-sm mt-1">
                </div>
                <div>
                    <label class="text-sm font-bold text-purple-700">{{ __('Catatan (opsional)') }}</label>
                    <textarea name="note" rows="2" class="input-premium w-full mt-1" placeholder="{{ __('Misal: transfer dari BCA an. Budi') }}"></textarea>
                </div>
                <button class="btn-glow">📤 {{ __('Upload & Submit') }}</button>
            </form>

            @if($subscription->manual_proof_path)
                <div class="mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800">
                    ✓ {{ __('Bukti sudah di-upload') }} ({{ __('upload ulang akan menggantikan yang lama') }}).
                </div>
            @endif
        </div>

        <a href="{{ route('billing.show') }}" class="btn-ghost">← {{ __('Kembali ke Billing') }}</a>
    </div>
</x-app-layout>

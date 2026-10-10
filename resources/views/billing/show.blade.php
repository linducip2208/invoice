<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Billing & Plan') }} <span class="text-2xl">💳</span></h2>
        <p class="text-sm text-purple-500 mt-1">✦ {{ __('Kelola subscription kamu') }}</p>
    </x-slot>

    @php
        $plan = $subscription->plan;
        $features = $plan->features ?? [];
        $isFree = $plan->isFree();
        $cancelling = $subscription->cancel_at_period_end;
    @endphp

    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Current plan card -->
        <div class="card-unicorn">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-bold text-purple-400 uppercase tracking-[0.2em]">{{ __('Plan saat ini') }}</p>
                    <h3 class="font-display text-3xl font-bold text-purple-900 mt-1">{{ $plan->name }} <span class="text-rainbow text-lg">✦</span></h3>
                    <p class="text-sm text-purple-500 mt-1">{{ $plan->tagline }}</p>
                </div>
                <div class="text-right">
                    @if($isFree)
                        <span class="badge-unicorn">{{ __('Free') }}</span>
                    @else
                        <p class="text-2xl font-bold text-purple-900">
                            @if($subscription->currency === 'IDR')
                                Rp{{ number_format($plan->priceFor('IDR', $subscription->billing_period === 'yearly' ? 'yearly' : 'monthly'), 0, ',', '.') }}
                            @else
                                ${{ number_format($plan->priceFor('USD', $subscription->billing_period === 'yearly' ? 'yearly' : 'monthly'), 0) }}
                            @endif
                        </p>
                        <p class="text-xs text-purple-500">/{{ $subscription->billing_period === 'yearly' ? __('tahun') : __('bulan') }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-purple-400 text-xs uppercase">Status</p>
                    <p class="font-bold text-purple-900 capitalize">{{ $subscription->status }}</p>
                </div>
                <div>
                    <p class="text-purple-400 text-xs uppercase">Currency</p>
                    <p class="font-bold text-purple-900">{{ $subscription->currency }}</p>
                </div>
                <div>
                    <p class="text-purple-400 text-xs uppercase">{{ __('Period') }}</p>
                    <p class="font-bold text-purple-900 capitalize">{{ __($subscription->billing_period) }}</p>
                </div>
                <div>
                    <p class="text-purple-400 text-xs uppercase">{{ __('Next billing') }}</p>
                    <p class="font-bold text-purple-900">{{ $subscription->current_period_ends_at?->format('d M Y') ?? '—' }}</p>
                </div>
            </div>

            @if($subscription->status === 'pending')
                <div class="mt-5 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-sm text-amber-800">⏳ {{ __('Subscription menunggu verifikasi pembayaran manual.') }}</p>
                    <a href="{{ route('billing.manual', $subscription) }}" class="btn-glow text-xs">{{ __('Lihat Instruksi / Upload Bukti') }}</a>
                </div>
            @endif

            @if($cancelling)
                <div class="mt-5 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-sm text-amber-800">⚠️ {{ __('Subscription akan dibatalkan di akhir periode.') }}</p>
                    <form method="POST" action="{{ route('billing.resume') }}">
                        @csrf
                        <button class="btn-unicorn-secondary">{{ __('Lanjutkan Subscription') }}</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Usage -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @php
                $maxInv = $features['max_invoices_lifetime'] ?? null;
                $maxClients = $features['max_clients'] ?? null;
                $maxUsers = $features['max_users'] ?? null;
            @endphp
            <div class="card-unicorn">
                <p class="text-xs font-bold text-purple-400 uppercase tracking-wider">{{ __('Invoice') }}</p>
                <p class="font-display text-2xl font-bold text-purple-900 mt-1">
                    {{ $usage['invoices'] }}<span class="text-base text-purple-400">/{{ $maxInv ?? '∞' }}</span>
                </p>
            </div>
            <div class="card-unicorn">
                <p class="text-xs font-bold text-purple-400 uppercase tracking-wider">{{ __('Klien') }}</p>
                <p class="font-display text-2xl font-bold text-purple-900 mt-1">
                    {{ $usage['clients'] }}<span class="text-base text-purple-400">/{{ $maxClients ?? '∞' }}</span>
                </p>
            </div>
            <div class="card-unicorn">
                <p class="text-xs font-bold text-purple-400 uppercase tracking-wider">{{ __('Recurring') }}</p>
                <p class="font-display text-2xl font-bold text-purple-900 mt-1">{{ $usage['recurring'] }}</p>
            </div>
        </div>

        <!-- Plan picker -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ {{ __('Ubah Plan') }}</h3>

            <div x-data="{ period: '{{ $subscription->billing_period === 'yearly' ? 'yearly' : 'monthly' }}', currency: '{{ $subscription->currency }}' }">
                <div class="flex items-center gap-3 flex-wrap mb-5">
                    <div class="inline-flex items-center bg-purple-50 rounded-full p-1 border border-purple-100">
                        <button @click="period = 'monthly'" :class="period === 'monthly' ? 'btn-unicorn' : 'px-4 py-1.5 text-sm font-semibold text-purple-600'" class="transition rounded-full">{{ __('Bulanan') }}</button>
                        <button @click="period = 'yearly'" :class="period === 'yearly' ? 'btn-unicorn' : 'px-4 py-1.5 text-sm font-semibold text-purple-600'" class="transition rounded-full">{{ __('Tahunan -17%') }}</button>
                    </div>
                    <div class="inline-flex items-center bg-purple-50 rounded-full p-1 border border-purple-100">
                        <button @click="currency = 'IDR'" :class="currency === 'IDR' ? 'btn-unicorn' : 'px-3 py-1.5 text-sm font-semibold text-purple-600'" class="transition rounded-full">IDR</button>
                        <button @click="currency = 'USD'" :class="currency === 'USD' ? 'btn-unicorn' : 'px-3 py-1.5 text-sm font-semibold text-purple-600'" class="transition rounded-full">USD</button>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    @foreach($plans as $p)
                        <div class="rounded-2xl p-5 border-2 {{ $p->id === $plan->id ? 'border-pink-400 bg-pink-50/40' : 'border-purple-100 bg-white/50' }}">
                            <h4 class="font-display font-bold text-xl text-purple-900">{{ $p->name }}</h4>
                            <p class="text-xs text-purple-500 mt-1">{{ $p->tagline }}</p>
                            <p class="mt-3 text-2xl font-bold text-purple-900">
                                <template x-if="currency === 'IDR'">
                                    <span x-text="period === 'monthly' ? '{{ 'Rp' . number_format($p->price_idr_monthly, 0, ',', '.') }}' : '{{ 'Rp' . number_format($p->price_idr_yearly, 0, ',', '.') }}'"></span>
                                </template>
                                <template x-if="currency === 'USD'">
                                    <span x-text="period === 'monthly' ? '${{ number_format($p->price_usd_monthly, 0) }}' : '${{ number_format($p->price_usd_yearly, 0) }}'"></span>
                                </template>
                            </p>

                            @if($p->id === $plan->id)
                                <div class="mt-4 text-center text-sm text-purple-600 font-semibold py-2">{{ __('Plan saat ini') }}</div>
                            @else
                                <form method="POST" action="{{ route('billing.subscribe', $p) }}" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="period" :value="period">
                                    <input type="hidden" name="currency" :value="currency">
                                    <button class="btn-unicorn w-full justify-center">
                                        {{ $p->isFree() ? __('Downgrade ke Free') : ($p->price_idr_monthly > $plan->price_idr_monthly ? __('Upgrade') : __('Pilih Plan')) }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if(!$isFree && !$cancelling)
            <div class="text-center">
                <form method="POST" action="{{ route('billing.cancel') }}" onsubmit="return confirm('Yakin batalkan subscription?');">
                    @csrf
                    <button class="text-rose-600 hover:text-rose-700 text-sm font-semibold underline">
                        {{ __('Batalkan Subscription') }}
                    </button>
                </form>
            </div>
        @endif

        @if($billingHistory->isNotEmpty())
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ {{ __('Riwayat Billing') }}</h3>
            <table class="table-unicorn">
                <thead>
                    <tr>
                        <th>{{ __('Tanggal') }}</th>
                        <th>{{ __('Referensi') }}</th>
                        <th class="text-right">{{ __('Jumlah') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billingHistory as $bh)
                        <tr>
                            <td class="text-purple-700">{{ $bh->paid_at?->format('d M Y') ?? $bh->created_at->format('d M Y') }}</td>
                            <td class="font-semibold text-purple-900">{{ $bh->reference_no }}</td>
                            <td class="text-right font-bold text-purple-900">{{ $bh->currency }} {{ number_format($bh->amount, 0) }}</td>
                            <td><span class="badge-{{ $bh->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($bh->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>

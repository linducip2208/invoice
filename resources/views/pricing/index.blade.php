@extends('layouts.seo', [
    'metaTitle' => __('Pricing — Invocely'),
    'metaDescription' => __('Plan invoicing yang fleksibel: Free selamanya, Pro untuk freelancer, Business untuk agency. Mulai gratis, upgrade kapan saja.'),
])

@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => 'Invocely',
        'description' => __('Aplikasi invoicing SaaS multi-tenant untuk freelancer, UKM, dan agency.'),
        'offers' => $plans->map(fn($p) => [
            '@type' => 'Offer',
            'name' => $p->name . ' (' . $currency . ')',
            'price' => (string) $p->priceFor($currency, 'monthly'),
            'priceCurrency' => $currency,
            'description' => $p->tagline,
        ])->all(),
    ];
@endphp

@section('jsonld'){!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES) !!}@endsection

@section('content')
<div x-data="{ period: 'yearly', currency: '{{ $currency }}' }" class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20">
    <div class="text-center max-w-3xl mx-auto">
        <p class="text-xs font-bold text-pink-500 uppercase tracking-[0.2em] mb-3">✦ Pricing ✦</p>
        <h1 class="font-display text-4xl sm:text-5xl font-bold text-purple-900 mb-4">
            {{ __('Pilih plan yang') }} <span class="text-rainbow">{{ __('cocok buat bisnis kamu') }}</span>
        </h1>
        <p class="text-purple-500 text-lg">{{ __('Mulai gratis, upgrade kapan saja. Tanpa kontrak, tanpa kartu kredit untuk Free plan.') }}</p>

        <div class="mt-8 flex items-center justify-center gap-3 flex-wrap">
            <div class="inline-flex items-center bg-white/80 backdrop-blur rounded-full p-1 border-2 border-purple-100">
                <button @click="period = 'monthly'" :class="period === 'monthly' ? 'btn-unicorn' : 'px-5 py-2 text-sm font-semibold text-purple-600'" class="transition rounded-full">
                    {{ __('Bulanan') }}
                </button>
                <button @click="period = 'yearly'" :class="period === 'yearly' ? 'btn-unicorn' : 'px-5 py-2 text-sm font-semibold text-purple-600'" class="transition rounded-full">
                    {{ __('Tahunan') }} <span class="ml-1 text-[10px] font-bold text-emerald-500">{{ __('-17%') }}</span>
                </button>
            </div>
            <div class="inline-flex items-center bg-white/80 backdrop-blur rounded-full p-1 border-2 border-purple-100">
                <button @click="currency = 'IDR'" :class="currency === 'IDR' ? 'btn-unicorn' : 'px-4 py-2 text-sm font-semibold text-purple-600'" class="transition rounded-full">IDR</button>
                <button @click="currency = 'USD'" :class="currency === 'USD' ? 'btn-unicorn' : 'px-4 py-2 text-sm font-semibold text-purple-600'" class="transition rounded-full">USD</button>
            </div>
        </div>
    </div>

    <div class="mt-12 grid md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            @php $isPopular = $plan->slug === 'pro'; @endphp
            <div class="card-unicorn relative {{ $isPopular ? 'ring-4 ring-pink-300' : '' }}">
                @if($isPopular)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs font-bold px-4 py-1 rounded-full shadow">
                        ✦ POPULER ✦
                    </div>
                @endif
                <h3 class="font-display text-2xl font-bold text-purple-900">{{ $plan->name }}</h3>
                <p class="text-sm text-purple-500 mt-1">{{ $plan->tagline }}</p>

                <div class="mt-5 mb-6">
                    <template x-if="currency === 'IDR'">
                        <div>
                            <span class="text-4xl font-bold text-purple-900" x-text="period === 'monthly' ? '{{ 'Rp' . number_format($plan->price_idr_monthly, 0, ',', '.') }}' : '{{ 'Rp' . number_format($plan->price_idr_yearly, 0, ',', '.') }}'"></span>
                            <span class="text-purple-500 text-sm" x-text="period === 'monthly' ? '/bulan' : '/tahun'"></span>
                        </div>
                    </template>
                    <template x-if="currency === 'USD'">
                        <div>
                            <span class="text-4xl font-bold text-purple-900" x-text="period === 'monthly' ? '${{ number_format($plan->price_usd_monthly, 0) }}' : '${{ number_format($plan->price_usd_yearly, 0) }}'"></span>
                            <span class="text-purple-500 text-sm" x-text="period === 'monthly' ? '/month' : '/year'"></span>
                        </div>
                    </template>
                </div>

                <ul class="space-y-2.5 text-sm text-purple-700 mb-6">
                    @if($plan->slug === 'free')
                        <li>✦ <strong>1 invoice selamanya</strong> (tidak bisa dihapus)</li>
                        <li>✦ Maks 5 klien</li>
                        <li>✦ 1 user</li>
                        <li>✦ 5 template invoice</li>
                        <li class="text-purple-400">✦ Watermark "Powered by Invocely"</li>
                        <li class="text-purple-400 line-through">✦ Recurring invoice</li>
                        <li class="text-purple-400 line-through">✦ Drag-drop builder</li>
                    @elseif($plan->slug === 'pro')
                        <li>✦ <strong>Unlimited</strong> invoice</li>
                        <li>✦ Recurring invoice ✓</li>
                        <li>✦ 3 user · unlimited klien</li>
                        <li>✦ Custom branding (no watermark)</li>
                        <li>✦ Time tracking · Expense tracking</li>
                        <li>✦ Audit log · Bulk actions</li>
                        <li>✦ Auto reminder (dunning)</li>
                        <li>✦ 2FA · CSV import</li>
                    @else
                        <li>✦ Semua fitur Pro +</li>
                        <li>✦ <strong>Drag-drop invoice builder</strong></li>
                        <li>✦ API access (REST)</li>
                        <li>✦ Custom domain (white-label)</li>
                        <li>✦ Unlimited team users</li>
                        <li>✦ Tenant webhooks</li>
                        <li>✦ Priority support</li>
                    @endif
                </ul>

                @auth
                    @if($plan->isFree())
                        <form method="POST" action="{{ route('billing.subscribe', $plan) }}">
                            @csrf
                            <input type="hidden" name="period" :value="period">
                            <input type="hidden" name="currency" :value="currency">
                            <button type="submit" class="{{ $isPopular ? 'btn-unicorn' : 'btn-unicorn-secondary' }} w-full justify-center">
                                {{ __('Mulai Gratis') }} ✦
                            </button>
                        </form>
                    @else
                        <div class="space-y-2">
                            <form method="POST" action="{{ route('billing.subscribe', $plan) }}">
                                @csrf
                                <input type="hidden" name="period" :value="period">
                                <input type="hidden" name="currency" :value="currency">
                                <input type="hidden" name="payment_method" value="gateway">
                                <button type="submit" class="{{ $isPopular ? 'btn-unicorn' : 'btn-unicorn-secondary' }} w-full justify-center">
                                    💳 {{ __('Bayar via Gateway') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('billing.subscribe', $plan) }}">
                                @csrf
                                <input type="hidden" name="period" :value="period">
                                <input type="hidden" name="currency" :value="currency">
                                <input type="hidden" name="payment_method" value="manual">
                                <button type="submit" class="btn-ghost w-full justify-center text-xs">
                                    💸 {{ __('Bayar Manual (Transfer)') }}
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="{{ $isPopular ? 'btn-unicorn' : 'btn-unicorn-secondary' }} w-full justify-center">
                        {{ __('Mulai dengan ') . $plan->name }} ✦
                    </a>
                @endauth
            </div>
        @endforeach
    </div>

    <div class="mt-16 text-center text-sm text-purple-500">
        {{ __('Bandingkan plan:') }}
        <a href="/compare/free-vs-pro" class="text-pink-600 font-semibold hover:underline">Free vs Pro</a>
        ·
        <a href="/compare/pro-vs-business" class="text-pink-600 font-semibold hover:underline">Pro vs Business</a>
    </div>
</div>
@endsection

<x-tabler.public :title="__('Pricing')" :meta-description="__('Flexible invoicing plans: Free forever, Pro for freelancers, Business for agencies. Start free, upgrade anytime.')">
    @push('head')
        @php
            $jsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => 'Invocely',
                'description' => __('SaaS invoicing app for freelancers, small businesses, and agencies.'),
                'offers' => $plans->map(fn($p) => [
                    '@type' => 'Offer',
                    'name' => $p->name . ' (' . $currency . ')',
                    'price' => (string) $p->priceFor($currency, 'monthly'),
                    'priceCurrency' => $currency,
                    'description' => $p->tagline,
                ])->all(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    <div x-data="{ period: 'yearly', currency: '{{ $currency }}' }">
        <div class="page-header">
            <div class="row g-2 align-items-center">
                <div class="col text-center">
                    <div class="page-pretitle justify-content-center">{{ __('Pricing') }}</div>
                    <h1 class="page-title">{{ __('Choose the plan that fits your business') }}</h1>
                    <div class="text-muted mt-1">{{ __('Start free, upgrade anytime. No contracts, no credit card for the Free plan.') }}</div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-4">
            <div class="btn-group" role="group" aria-label="{{ __('Billing period') }}">
                <button type="button" @click="period = 'monthly'" :class="period === 'monthly' ? 'btn btn-primary' : 'btn'" class="btn">{{ __('Monthly') }}</button>
                <button type="button" @click="period = 'yearly'" :class="period === 'yearly' ? 'btn btn-primary' : 'btn'" class="btn">{{ __('Yearly') }} <span class="badge bg-green-lt ms-1">{{ __('-17%') }}</span></button>
            </div>
            <div class="btn-group" role="group" aria-label="{{ __('Currency') }}">
                <button type="button" @click="currency = 'IDR'" :class="currency === 'IDR' ? 'btn btn-primary' : 'btn'" class="btn">IDR</button>
                <button type="button" @click="currency = 'USD'" :class="currency === 'USD' ? 'btn btn-primary' : 'btn'" class="btn">USD</button>
            </div>
        </div>

        <div class="row row-cards">
            @foreach($plans as $plan)
                @php $isPopular = $plan->slug === 'pro'; @endphp
                <div class="col-md-4">
                    <div class="card h-100 {{ $isPopular ? 'card-active' : '' }}">
                        @if($isPopular)
                            <div class="ribbon ribbon-top bg-primary">{{ __('Popular') }}</div>
                        @endif
                        <div class="card-body">
                            <h3 class="card-title">{{ $plan->name }}</h3>
                            <p class="text-muted">{{ $plan->tagline }}</p>
                            <div class="mb-3">
                                <template x-if="currency === 'IDR'">
                                    <div>
                                        <span class="h1 mb-0" x-text="period === 'monthly' ? '{{ 'Rp' . number_format($plan->price_idr_monthly, 0, ',', '.') }}' : '{{ 'Rp' . number_format($plan->price_idr_yearly, 0, ',', '.') }}'"></span>
                                        <span class="text-muted" x-text="period === 'monthly' ? '/bulan' : '/tahun'"></span>
                                    </div>
                                </template>
                                <template x-if="currency === 'USD'">
                                    <div>
                                        <span class="h1 mb-0" x-text="period === 'monthly' ? '${{ number_format($plan->price_usd_monthly, 0) }}' : '${{ number_format($plan->price_usd_yearly, 0) }}'"></span>
                                        <span class="text-muted" x-text="period === 'monthly' ? '/month' : '/year'"></span>
                                    </div>
                                </template>
                            </div>
                            <ul class="list-unstyled space-y-1 mb-3">
                                @if($plan->slug === 'free')
                                    <li><x-ti name="check" /> <strong>{{ __('1 invoice forever') }}</strong></li>
                                    <li><x-ti name="check" /> {{ __('Max 5 clients') }}</li>
                                    <li><x-ti name="check" /> {{ __('1 user') }}</li>
                                    <li><x-ti name="check" /> {{ __('5 invoice templates') }}</li>
                                    <li class="text-muted"><x-ti name="info-circle" /> {{ __('"Powered by Invocely" watermark') }}</li>
                                @elseif($plan->slug === 'pro')
                                    <li><x-ti name="check" /> <strong>{{ __('Unlimited invoices') }}</strong></li>
                                    <li><x-ti name="check" /> {{ __('Recurring invoices') }}</li>
                                    <li><x-ti name="check" /> {{ __('3 users, unlimited clients') }}</li>
                                    <li><x-ti name="check" /> {{ __('Custom branding (no watermark)') }}</li>
                                    <li><x-ti name="check" /> {{ __('Time tracking, expense tracking') }}</li>
                                    <li><x-ti name="check" /> {{ __('Audit log, bulk actions') }}</li>
                                    <li><x-ti name="check" /> {{ __('Auto reminders') }}</li>
                                    <li><x-ti name="check" /> {{ __('2FA, CSV import') }}</li>
                                @else
                                    <li><x-ti name="check" /> {{ __('Everything in Pro, plus:') }}</li>
                                    <li><x-ti name="check" /> <strong>{{ __('Drag-drop invoice builder') }}</strong></li>
                                    <li><x-ti name="check" /> {{ __('API access (REST)') }}</li>
                                    <li><x-ti name="check" /> {{ __('Custom domain (white-label)') }}</li>
                                    <li><x-ti name="check" /> {{ __('Unlimited team users') }}</li>
                                    <li><x-ti name="check" /> {{ __('Tenant webhooks') }}</li>
                                    <li><x-ti name="check" /> {{ __('Priority support') }}</li>
                                @endif
                            </ul>
                            @auth
                                @if($plan->isFree())
                                    <form method="POST" action="{{ route('billing.subscribe', $plan) }}">
                                        @csrf
                                        <input type="hidden" name="period" :value="period">
                                        <input type="hidden" name="currency" :value="currency">
                                        <button type="submit" class="btn {{ $isPopular ? 'btn-primary' : 'btn-outline-primary' }} w-100"><x-ti name="plus" /> {{ __('Start Free') }}</button>
                                    </form>
                                @else
                                    <div class="d-grid gap-2">
                                        <form method="POST" action="{{ route('billing.subscribe', $plan) }}">
                                            @csrf
                                            <input type="hidden" name="period" :value="period">
                                            <input type="hidden" name="currency" :value="currency">
                                            <input type="hidden" name="payment_method" value="gateway">
                                            <button type="submit" class="btn {{ $isPopular ? 'btn-primary' : 'btn-outline-primary' }} w-100"><x-ti name="credit-card" /> {{ __('Pay via Gateway') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('billing.subscribe', $plan) }}">
                                            @csrf
                                            <input type="hidden" name="period" :value="period">
                                            <input type="hidden" name="currency" :value="currency">
                                            <input type="hidden" name="payment_method" value="manual">
                                            <button type="submit" class="btn btn-sm w-100"><x-ti name="cash" /> {{ __('Pay Manual (Transfer)') }}</button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="btn {{ $isPopular ? 'btn-primary' : 'btn-outline-primary' }} w-100"><x-ti name="plus" /> {{ __('Start with ') . $plan->name }}</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-center text-muted mt-4">
            {{ __('Compare plans:') }}
            <a href="/compare/free-vs-pro" class="fw-semibold">Free vs Pro</a>
            &middot;
            <a href="/compare/pro-vs-business" class="fw-semibold">Pro vs Business</a>
        </p>
    </div>
</x-tabler.public>

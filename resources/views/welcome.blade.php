<x-tabler.public :title="__('Magical Invoicing for Dreamy Businesses')" :meta-description="__('Invocely — invoicing for freelancers, agencies, and small businesses. Send invoices, track payments, and run on auto-pilot.')">
    @push('head')
        @php
            $welcomeJsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => 'Invocely',
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
                'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.9', 'reviewCount' => '1247'],
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($welcomeJsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><span class="badge bg-primary-lt"><x-ti name="sparkles" /> {{ __('New for') }} {{ now()->year }}</span></div>
                <h1 class="page-title">{{ __('Invoicing that feels like magic') }}</h1>
                <div class="text-muted mt-1">{{ __('Send invoices, track payments, and run recurring billing on auto-pilot. Built for freelancers, agencies, and small businesses.') }}</div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Start Free') }}</a>
                    <a href="{{ route('seo.templates') }}" class="btn"><x-ti name="template" /> {{ __('Browse Templates') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4">
        <div class="col-md-4">
            <x-tabler.stat :label="__('Rating')" value="4.9/5" icon="star" />
        </div>
        <div class="col-md-4">
            <x-tabler.stat :label="__('Happy customers')" value="1,247" icon="users" />
        </div>
        <div class="col-md-4">
            <x-tabler.stat :label="__('Templates')" value="5" icon="template" />
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="file-invoice" /> {{ __('Example invoice') }} — INV-2026-0042</h3>
            <div class="card-actions"><span class="badge bg-green-lt">{{ __('Paid') }}</span></div>
        </div>
        <div class="card-body">
            <div class="datagrid">
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Bill To') }}</div>
                    <div class="datagrid-content fw-semibold">Sparkle Studios</div>
                    <div class="datagrid-content text-muted">hello@sparkle.land</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Total') }}</div>
                    <div class="datagrid-content h2 mb-0">$2,450.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ __('Features') }}</div>
                <h2 class="page-title">{{ __('Everything you need, nothing you don\'t') }}</h2>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4">
        @foreach([
            ['template', __('5 Invoice Templates'), __('Classic, Modern, Minimal, Bold, and Elegant — all customizable with your brand colors.')],
            ['repeat', __('Recurring Invoices'), __('Set it and forget it. Bill clients on auto-pilot with daily, weekly, monthly, or yearly schedules.')],
            ['credit-card', __('Payment Tracking'), __('Record partial or full payments with cash, bank transfer, credit card, or any method.')],
            ['send', __('Email Sending'), __('Send invoices straight from the app with a clean template and PDF attached.')],
            ['link', __('Public Sharing'), __('Each invoice gets a public URL. Share with clients — no login required.')],
            ['chart-bar', __('Smart Reports'), __('See revenue, outstanding, and overdue at a glance. Top clients and payment trends included.')],
        ] as [$icon, $title, $desc])
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-2 text-primary"><x-ti :name="$icon" class="icon icon-lg" /></div>
                        <h3 class="card-title">{{ $title }}</h3>
                        <p class="text-muted mb-0">{{ $desc }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="template" /> {{ __('Five styles, one gallery') }}</h3>
            <div class="card-actions"><a href="{{ route('seo.templates') }}" class="btn btn-sm">{{ __('View all') }}</a></div>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach(['Classic', 'Modern', 'Minimal', 'Bold', 'Elegant'] as $name)
                    <div class="col-6 col-lg">
                        <a href="{{ route('seo.templates') }}" class="card card-link text-center">
                            <div class="card-body">
                                <div class="mb-2 text-muted"><x-ti name="file-text" class="icon icon-lg" /></div>
                                <p class="fw-semibold mb-0">{{ $name }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card card-md text-center mb-4">
        <div class="card-body">
            <div class="mb-2 text-primary"><x-ti name="sparkles" class="icon icon-lg" /></div>
            <h2 class="card-title justify-content-center">{{ __('Ready to get started?') }}</h2>
            <p class="text-muted">{{ __('Join thousands of businesses invoicing with Invocely. Free to start, no credit card needed.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Begin Your Setup') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <h4 class="subheader">{{ __('Product') }}</h4>
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
                        <li><a href="{{ route('docs') }}">{{ __('Documentation') }}</a></li>
                        <li><a href="{{ route('seo.templates') }}">{{ __('Templates') }}</a></li>
                        <li><a href="{{ route('seo.best-tools', ['year' => now()->year]) }}">{{ __('Best Tools') }} {{ now()->year }}</a></li>
                        <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h4 class="subheader">{{ __('Compare') }}</h4>
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('seo.compare', ['a' => 'invocely', 'b' => 'freshbooks']) }}">vs FreshBooks</a></li>
                        <li><a href="{{ route('seo.compare', ['a' => 'invocely', 'b' => 'quickbooks']) }}">vs QuickBooks</a></li>
                        <li><a href="{{ route('seo.compare', ['a' => 'invocely', 'b' => 'xero']) }}">vs Xero</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h4 class="subheader">{{ __('Alternatives') }}</h4>
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('seo.alternatives', ['slug' => 'freshbooks']) }}">FreshBooks {{ __('alternatives') }}</a></li>
                        <li><a href="{{ route('seo.alternatives', ['slug' => 'quickbooks']) }}">QuickBooks {{ __('alternatives') }}</a></li>
                        <li><a href="{{ route('seo.alternatives', ['slug' => 'wave']) }}">Wave {{ __('alternatives') }}</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <h4 class="subheader">{{ __('Company') }}</h4>
                    <p class="text-muted mb-0">{{ __('Modern invoicing for freelancers, agencies, and small businesses.') }}</p>
                    <p class="text-muted mt-2 mb-0">&copy; {{ now()->year }} {{ branding('app_name') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-tabler.public>

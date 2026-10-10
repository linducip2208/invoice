<x-tabler.public :title="__('Professional invoicing for freelancers and small businesses')" :meta-description="__('Invocely — create invoices, track payments, automate recurring billing, and understand your cash flow.')">
    @push('head')
        @php
            $welcomeJsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => 'Invocely',
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($welcomeJsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    {{-- 1. Hero --}}
    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col-lg">
                <div class="page-pretitle"><span class="badge bg-blue-lt">{{ __('Invoicing & collections') }}</span></div>
                <h1 class="page-title">{{ __('Get paid faster, with less chasing') }}</h1>
                <div class="text-muted mt-1">{{ __('Create professional invoices, share a secure link, track every payment, and automate recurring billing — all from one workspace.') }}</div>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Start free') }}</a>
                    <a href="{{ route('login') }}" class="btn">{{ __('Sign in') }}</a>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Product preview (clearly illustrative, built from real UI components) --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="file-invoice" /> {{ __('How an invoice looks') }}</h3>
            <div class="card-actions"><span class="badge bg-azure-lt">{{ __('Illustrative preview') }}</span></div>
        </div>
        <div class="card-body">
            <div class="datagrid">
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Status') }}</div>
                    <div class="datagrid-content"><span class="badge bg-warning-lt">{{ __('Partial') }}</span></div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Total') }}</div>
                    <div class="datagrid-content h2 mb-0">Rp 2.450.000</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Balance due') }}</div>
                    <div class="datagrid-content h2 mb-0">Rp 950.000</div>
                </div>
            </div>
            <p class="text-muted small mb-0">{{ __('Sample figures for illustration only — not real customer data.') }}</p>
        </div>
    </div>

    {{-- 3. Benefits (verified capabilities only) --}}
    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ __('Capabilities') }}</div>
                <h2 class="page-title">{{ __('Everything around the invoice') }}</h2>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4">
        @foreach([
            ['file-invoice', __('Invoice creation'), __('Line items, taxes, discounts, currencies, due dates, and draft-to-paid lifecycle.')],
            ['template', __('Invoice templates'), __('Multiple print-ready layouts with your brand colors and logo.')],
            ['credit-card', __('Payment tracking'), __('Record partial or full payments; balances update automatically.')],
            ['repeat', __('Recurring invoices'), __('Schedules generate invoices automatically, with pause and resume.')],
            ['chart-bar', __('Financial reports'), __('Aging, cash flow, profit & loss, and tax summaries from your own data.')],
            ['bell', __('Collection reminders'), __('Automatic schedules plus one-click reminders from the aging report.')],
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

    {{-- 4. Workflow --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">{{ __('How it works') }}</h3></div>
        <div class="card-body">
            <ul class="steps steps-vertical">
                <li class="step-item"><div class="h4 m-0">{{ __('Create an invoice') }}</div><div class="text-muted">{{ __('Pick a customer, add line items, set terms.') }}</div></li>
                <li class="step-item"><div class="h4 m-0">{{ __('Send it') }}</div><div class="text-muted">{{ __('Email with PDF, or share a secure link that can be rotated.') }}</div></li>
                <li class="step-item"><div class="h4 m-0">{{ __('Track payment') }}</div><div class="text-muted">{{ __('Record payments; reminders and aging stay current.') }}</div></li>
                <li class="step-item"><div class="h4 m-0">{{ __('Review reports') }}</div><div class="text-muted">{{ __('See collected, outstanding, and overdue at a glance.') }}</div></li>
            </ul>
        </div>
    </div>

    {{-- 5. Pricing pointer (real plans live on the pricing page) --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h3 class="card-title mb-1">{{ __('Simple plans that grow with you') }}</h3>
                    <p class="text-muted mb-0">{{ __('Free to start. Upgrade for recurring billing, automation, and team features.') }}</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pricing') }}" class="btn btn-primary">{{ __('See pricing') }}</a>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. Security & trust (verified claims only) --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="shield-lock" /> {{ __('Security you can verify') }}</h3></div>
        <div class="card-body">
            <ul class="mb-0">
                <li>{{ __('Workspace isolation: every record belongs to exactly one workspace.') }}</li>
                <li>{{ __('Secrets encrypted at rest; one-time codes hashed, never logged.') }}</li>
                <li>{{ __('Private pages never cached; financial data never served stale offline.') }}</li>
                <li>{{ __('Read-only audit trail on business records.') }}</li>
            </ul>
        </div>
    </div>

    {{-- 7. FAQ --}}
    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">{{ __('Frequently asked') }}</h3></div>
        <div class="card-body">
            <div class="accordion" id="landing-faq">
                @foreach([
                    [__('Is there a free plan?'), __('Yes. Core invoicing is free; paid plans unlock automation and team sizes.')],
                    [__('Can clients pay online?'), __('You can connect payment gateways, or record manual bank transfers. Each gateway needs its own provider account.')],
                    [__('Is my data separated from other businesses?'), __('Yes. Workspaces are strictly isolated at the database level.')],
                ] as $i => [$q, $a])
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $i }}" aria-expanded="false" aria-controls="faq-{{ $i }}">{{ $q }}</button>
                        </h3>
                        <div id="faq-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#landing-faq">
                            <div class="accordion-body text-muted">{{ $a }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 8. Conversion CTA --}}
    <div class="card card-md text-center mb-4">
        <div class="card-body">
            <h2 class="card-title justify-content-center">{{ __('Send your first invoice today') }}</h2>
            <p class="text-muted">{{ __('Free to start. No credit card required.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Create free account') }}</a>
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
                    <p class="text-muted mb-0">{{ __('Invoicing for freelancers, agencies, and small businesses.') }}</p>
                    <p class="text-muted mt-2 mb-0">&copy; {{ now()->year }} {{ branding('app_name') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-tabler.public>

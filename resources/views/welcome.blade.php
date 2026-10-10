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

    {{-- HERO: headline + visual produk skala besar --}}
    <div class="row g-3 align-items-center mb-5">
        <div class="col-lg-6">
            <div class="page-pretitle"><span class="badge bg-blue-lt">{{ __('Invoicing & collections') }}</span></div>
            <h1 class="display-5 fw-bold mt-2">{{ __('Get paid faster, without the chase') }}</h1>
            <p class="text-muted lead">{{ __('Professional invoices, secure client links, automatic balances, and recurring billing — in one calm workspace.') }}</p>
            <div class="btn-list mt-3">
                <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary btn-lg"><x-ti name="plus" /> {{ __('Start free') }}</a>
                <a href="{{ route('pricing') }}" class="btn btn-lg">{{ __('See pricing') }}</a>
            </div>
            <p class="text-muted small mt-3 mb-0">{{ __('Free plan included · No credit card to start · Cancel anytime') }}</p>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><x-ti name="file-invoice" /> INV-2026-0042</h3>
                    <div class="card-actions">
                        <span class="badge bg-warning-lt">{{ __('Partial') }}</span>
                        <span class="badge bg-azure-lt">{{ __('Illustrative preview') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="datagrid mb-3">
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Billed to') }}</div>
                            <div class="datagrid-content fw-semibold">Contoso Studio</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ __('Due date') }}</div>
                            <div class="datagrid-content">09 Nov 2026</div>
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
                    <div class="progress mb-2" style="height: 0.6rem;" role="img" aria-label="{{ __('61% collected') }}">
                        <div class="progress-bar bg-success" style="width: 61%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>{{ __('Collected 61%') }}</span><span>{{ __('2 reminders scheduled') }}</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">{{ __('Sample figures for illustration only.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TRUST STRIP: fakta jujur --}}
    <div class="card mb-5">
        <div class="card-body">
            <div class="row g-3 text-center">
                @foreach([
                    [__('Separate workspace per business'), __('Your records are isolated at the database level.')],
                    [__('Audit trail included'), __('Key business changes are logged and reviewable.')],
                    [__('Cancel anytime'), __('Monthly plans can be cancelled at period end.')],
                ] as [$t, $d])
                    <div class="col-md-4">
                        <div class="fw-semibold"><x-ti name="check" /> {{ $t }}</div>
                        <div class="text-muted small">{{ $d }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- EDITORIAL ALTERNATING: use cases --}}
    <div class="page-header mb-3">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ __('Who it fits') }}</div>
                <h2 class="page-title">{{ __('Built around how you bill') }}</h2>
            </div>
        </div>
    </div>

    @foreach([
        ['file-invoice', __('Freelancers'), __('Send an invoice the day work ships. Share a link, get notified on view, nudge automatically when it goes overdue.'), false],
        ['briefcase', __('Agencies & consultants'), __('Per-client workspaces, quotes that convert to invoices, partial payments, and reports your accountant will accept.'), true],
        ['repeat', __('Retainers & subscriptions'), __('Recurring schedules with pause and resume, proration by line items, and dunning reminders before cards fail.'), false],
    ] as [$icon, $title, $desc, $flip])
        <div class="row g-3 align-items-center mb-5">
            <div class="col-lg-6 {{ $flip ? 'order-lg-2' : '' }}">
                <div class="mb-2 text-primary"><x-ti :name="$icon" class="icon icon-lg" /></div>
                <h3 class="h2">{{ $title }}</h3>
                <p class="text-muted">{{ $desc }}</p>
                <a href="{{ route('docs') }}" class="btn btn-outline-primary">{{ __('Read the docs') }}</a>
            </div>
            <div class="col-lg-6 {{ $flip ? 'order-lg-1' : '' }}">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="avatar"><x-ti :name="$icon" /></span>
                            <div>
                                <div class="fw-semibold">{{ $title }}</div>
                                <div class="text-muted small">{{ __('Typical week') }}</div>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0"><x-ti name="check" /> {{ __('Monday: send 3 invoices') }}</li>
                            <li class="list-group-item px-0"><x-ti name="check" /> {{ __('Wednesday: 2 paid, 1 reminded') }}</li>
                            <li class="list-group-item px-0"><x-ti name="check" /> {{ __('Friday: review the aging report') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- LIFECYCLE --}}
    <div class="card mb-5">
        <div class="card-header"><h3 class="card-title">{{ __('From work to cash in four steps') }}</h3></div>
        <div class="card-body">
            <ul class="steps steps-vertical">
                <li class="step-item"><div class="h4 m-0">{{ __('Create') }}</div><div class="text-muted">{{ __('Customer, items, tax, due date.') }}</div></li>
                <li class="step-item"><div class="h4 m-0">{{ __('Send') }}</div><div class="text-muted">{{ __('Email with PDF, or a rotatable secure link.') }}</div></li>
                <li class="step-item"><div class="h4 m-0">{{ __('Collect') }}</div><div class="text-muted">{{ __('Partial and full payments, gateway or manual.') }}</div></li>
                <li class="step-item"><div class="h4 m-0">{{ __('Understand') }}</div><div class="text-muted">{{ __('Aging, cash flow, and P&L from your own data.') }}</div></li>
            </ul>
        </div>
    </div>

    {{-- PRICING nyata dari DB --}}
    <div class="page-header mb-3">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ __('Pricing') }}</div>
                <h2 class="page-title">{{ __('Start free, upgrade when it pays for itself') }}</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('pricing') }}" class="btn">{{ __('Compare all features') }}</a>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-5">
        @foreach($plans as $plan)
            <div class="col-md-4">
                <div class="card h-100 {{ $plan->slug === 'pro' ? 'card-active' : '' }}">
                    <div class="card-body text-center">
                        @if($plan->slug === 'pro')
                            <span class="badge bg-primary-lt mb-2">{{ __('Most popular') }}</span>
                        @endif
                        <h3 class="card-title justify-content-center">{{ $plan->name }}</h3>
                        <p class="text-muted small">{{ $plan->tagline }}</p>
                        <p class="display-6 mb-0">
                            @if((float) $plan->price_idr_monthly > 0)
                                Rp{{ number_format((float) $plan->price_idr_monthly, 0, ',', '.') }}<span class="text-muted h4">/{{ __('mo') }}</span>
                            @else
                                {{ __('Free') }}
                            @endif
                        </p>
                        <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn {{ $plan->slug === 'pro' ? 'btn-primary' : '' }} w-100 mt-3">{{ __('Choose :plan', ['plan' => $plan->name]) }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- FAQ --}}
    <div class="card mb-5">
        <div class="card-header"><h3 class="card-title">{{ __('Questions, answered') }}</h3></div>
        <div class="card-body">
            <div class="accordion" id="landing-faq">
                @foreach([
                    [__('Do I need a credit card to try it?'), __('No. The free plan needs only an email address.')],
                    [__('Can my clients pay online?'), __('Connect a gateway provider, or record manual transfers. Provider accounts are separate.')],
                    [__('What happens if I cancel?'), __('Monthly plans run to period end; your data stays exportable via CSV.')],
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

    <div class="card card-md text-center mb-4">
        <div class="card-body">
            <h2 class="card-title justify-content-center">{{ __('Stop chasing. Start collecting.') }}</h2>
            <p class="text-muted">{{ __('Create your first invoice in minutes.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary btn-lg"><x-ti name="plus" /> {{ __('Create free account') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
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

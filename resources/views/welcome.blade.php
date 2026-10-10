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

    {{-- HERO --}}
    <div class="landing-hero mb-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="mb-3"><span class="badge bg-white-lt text-white">{{ __('Invoicing & collections') }}</span></div>
                <h1 class="display-5 fw-bold">{{ __('Get paid faster, without the chase') }}</h1>
                <p class="lead text-muted">{{ __('Professional invoices, secure client links, automatic balances, and recurring billing — in one calm workspace.') }}</p>
                <div class="btn-list mt-3">
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-light btn-lg"><x-ti name="plus" /> {{ __('Start free') }}</a>
                    <a href="{{ route('pricing') }}" class="btn btn-outline-light btn-lg">{{ __('See pricing') }}</a>
                </div>
                <p class="small mt-3 mb-0 text-muted">{{ __('Free plan included · No credit card to start · Cancel anytime') }}</p>
            </div>
            <div class="col-lg-6">
                <div class="card landing-shot mb-0">
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
    </div>

    {{-- TRUST STRIP --}}
    <div class="card mb-5">
        <div class="card-body">
            <div class="row g-3 text-center">
                @foreach([
                    ['shield-lock', 'bg-green-lt text-green', __('Separate workspace per business'), __('Your records are isolated at the database level.')],
                    ['history', 'bg-blue-lt text-blue', __('Audit trail included'), __('Key business changes are logged and reviewable.')],
                    ['x', 'bg-azure-lt text-azure', __('Cancel anytime'), __('Monthly plans can be cancelled at period end.')],
                ] as [$icon, $tile, $t, $d])
                    <div class="col-md-4">
                        <div class="mb-2"><span class="icon-tile {{ $tile }}"><x-ti :name="$icon" class="icon icon-lg" /></span></div>
                        <div class="fw-semibold">{{ $t }}</div>
                        <div class="text-muted small">{{ $d }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CAPABILITIES --}}
    <div class="page-header mb-3">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ __('Capabilities') }}</div>
                <h2 class="page-title">{{ __('Everything around the invoice') }}</h2>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-5">
        @foreach([
            ['file-invoice', 'bg-blue-lt text-blue', __('Invoice creation'), __('Line items, taxes, discounts, currencies, due dates, and draft-to-paid lifecycle.')],
            ['template', 'bg-purple-lt text-purple', __('Invoice templates'), __('Multiple print-ready layouts with your brand colors and logo.')],
            ['credit-card', 'bg-green-lt text-green', __('Payment tracking'), __('Record partial or full payments; balances update automatically.')],
            ['repeat', 'bg-azure-lt text-azure', __('Recurring invoices'), __('Schedules generate invoices automatically, with pause and resume.')],
            ['chart-bar', 'bg-yellow-lt text-yellow', __('Financial reports'), __('Aging, cash flow, profit & loss, and tax summaries from your own data.')],
            ['bell', 'bg-red-lt text-red', __('Collection reminders'), __('Automatic schedules plus one-click reminders from the aging report.')],
        ] as [$icon, $tile, $title, $desc])
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3"><span class="icon-tile {{ $tile }}"><x-ti :name="$icon" class="icon icon-lg" /></span></div>
                        <h3 class="card-title">{{ $title }}</h3>
                        <p class="text-muted mb-0">{{ $desc }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- EDITORIAL ALTERNATING --}}
    <div class="page-header mb-3">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ __('Who it fits') }}</div>
                <h2 class="page-title">{{ __('Built around how you bill') }}</h2>
            </div>
        </div>
    </div>

    @foreach([
        ['file-invoice', 'bg-blue-lt text-blue', __('Freelancers'), __('Send an invoice the day work ships. Share a link, get notified on view, nudge automatically when it goes overdue.'), false],
        ['briefcase', 'bg-purple-lt text-purple', __('Agencies & consultants'), __('Per-client workspaces, quotes that convert to invoices, partial payments, and reports your accountant will accept.'), true],
        ['repeat', 'bg-green-lt text-green', __('Retainers & subscriptions'), __('Recurring schedules with pause and resume, proration by line items, and dunning reminders before cards fail.'), false],
    ] as [$icon, $tile, $title, $desc, $flip])
        <div class="landing-tint mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6 {{ $flip ? 'order-lg-2' : '' }}">
                    <div class="mb-2"><span class="icon-tile {{ $tile }}"><x-ti :name="$icon" class="icon icon-lg" /></span></div>
                    <h3 class="h2">{{ $title }}</h3>
                    <p class="text-muted">{{ $desc }}</p>
                    <a href="{{ route('docs') }}" class="btn btn-outline-primary">{{ __('Read the docs') }}</a>
                </div>
                <div class="col-lg-6 {{ $flip ? 'order-lg-1' : '' }}">
                    <div class="card mb-0">
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
        </div>
    @endforeach

    {{-- LIFECYCLE --}}
    <div class="card mb-5">
        <div class="card-body">
            <h3 class="card-title mb-1">{{ __('From work to cash in four steps') }}</h3>
            <p class="text-muted mb-4">{{ __('A straight line from effort to money in the bank.') }}</p>
            <div class="row row-cards row-deck">
                @foreach([
                    ['01', __('Create'), __('Pick a customer, add line items, set terms.')],
                    ['02', __('Send'), __('Email with PDF, or a rotatable secure link.')],
                    ['03', __('Collect'), __('Partial and full payments, gateway or manual.')],
                    ['04', __('Understand'), __('Aging, cash flow, and P&L from your own data.')],
                ] as [$no, $title, $desc])
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="display-6 fw-bold text-primary mb-2">{{ $no }}</div>
                                <div class="fw-semibold mb-1">{{ $title }}</div>
                                <div class="text-muted small">{{ $desc }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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

    {{-- CTA BAND --}}
    <div class="landing-cta-band mb-4 text-center">
        <h2 class="mb-2">{{ __('Stop chasing. Start collecting.') }}</h2>
        <p class="text-muted">{{ __('Create your first invoice in minutes.') }}</p>
        <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-light btn-lg"><x-ti name="plus" /> {{ __('Create free account') }}</a>
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

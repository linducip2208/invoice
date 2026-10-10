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

    {{-- HERO: editorial split, dominant document preview --}}
    <div class="landing-hero mb-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <p class="mb-2"><span class="badge bg-white-lt text-white">{{ __('Invoicing & collections · ID + EN') }}</span></p>
                <h1 class="display-4 fw-bold mb-3">{{ __('Get paid faster, without the chase') }}</h1>
                <p class="fs-3 text-muted mb-1">{{ __('The calm billing workspace for freelancers and small teams.') }}</p>
                <p class="text-muted">{{ __('Professional invoices, secure client links, automatic balances, and recurring billing — with an audit trail your accountant will accept.') }}</p>
                <div class="btn-list mt-3">
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-light btn-lg"><x-ti name="plus" /> {{ __('Start free') }}</a>
                    <a href="{{ route('pricing') }}" class="btn btn-outline-light btn-lg">{{ __('See pricing') }}</a>
                </div>
                <p class="small mt-3 mb-0 text-muted">{{ __('Free plan included · No credit card to start · Cancel anytime') }}</p>
                <div class="d-flex gap-4 mt-3 pt-3 border-top border-white opacity-75" style="--tblr-border-opacity: .2;">
                    <div><div class="h2 mb-0">61%</div><div class="small text-muted">{{ __('collected on sample invoice') }}</div></div>
                    <div><div class="h2 mb-0">4</div><div class="small text-muted">{{ __('steps: work → cash') }}</div></div>
                    <div><div class="h2 mb-0">8</div><div class="small text-muted">{{ __('ID gateways supported') }}</div></div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card landing-shot mb-0" role="img" aria-label="{{ __('Sample invoice preview') }}">
                    <div class="card-header">
                        <div>
                            <div class="subheader">{{ __('Sample invoice') }}</div>
                            <h3 class="card-title mb-0"><x-ti name="file-invoice" /> INV-2026-0042 · Contoso Studio</h3>
                        </div>
                        <div class="card-actions">
                            <span class="badge bg-warning-lt">{{ __('Partial') }}</span>
                            <span class="badge bg-azure-lt">{{ __('Illustrative preview') }}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead><tr><th>{{ __('Item') }}</th><th class="text-end">{{ __('Qty') }}</th><th class="text-end">{{ __('Amount') }}</th></tr></thead>
                            <tbody>
                                <tr><td>{{ __('Brand identity package') }}</td><td class="text-end text-muted">1</td><td class="text-end">Rp 1.500.000</td></tr>
                                <tr><td>{{ __('Landing page build') }}</td><td class="text-end text-muted">1</td><td class="text-end">Rp 950.000</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-7">
                                <div class="datagrid mb-2">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Due date') }}</div>
                                        <div class="datagrid-content fw-semibold">09 Nov 2026</div>
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
                            </div>
                            <div class="col-sm-5 text-sm-end">
                                <div class="text-muted small">{{ __('Total') }}</div>
                                <div class="h1 mb-2">Rp 2.450.000</div>
                                <span class="btn btn-primary btn-sm disabled" aria-disabled="true"><x-ti name="link" /> {{ __('Secure client link') }}</span>
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">{{ __('Sample figures for illustration only.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TRUST STRIP: plain divider, not a card --}}
    <div class="d-flex flex-wrap gap-4 align-items-center justify-content-between py-3 px-1 mb-4 border-bottom">
        @foreach([
            [__('Separate workspace per business'), __('Isolated at the database level.')],
            [__('Audit trail included'), __('Key changes are logged.')],
            [__('Cancel anytime'), __('Monthly plans end at period end.')],
        ] as [$t, $d])
            <div class="d-flex align-items-center gap-2">
                <x-ti name="check" class="text-success" />
                <span class="fw-semibold">{{ $t }}</span>
                <span class="text-muted small d-none d-md-inline">{{ $d }}</span>
            </div>
        @endforeach
    </div>

    {{-- CAPABILITIES: definition list, not 6 identical cards --}}
    <div class="row g-4 mb-5 align-items-start">
        <div class="col-lg-4">
            <div class="page-pretitle">{{ __('Capabilities') }}</div>
            <h2 class="h1 mb-2">{{ __('Everything around the invoice') }}</h2>
            <p class="text-muted mb-3">{{ __('One workspace from first draft to money in the bank. No plugins, no spreadsheet side-quests.') }}</p>
            <a href="{{ route('docs') }}" class="btn btn-outline-primary">{{ __('Read the docs') }}</a>
        </div>
        <div class="col-lg-8">
            <dl class="row g-3 mb-0">
                @foreach([
                    ['file-invoice', __('Invoice creation'), __('Line items, taxes, discounts, currencies, due dates, draft-to-paid lifecycle.')],
                    ['repeat', __('Recurring invoices'), __('Schedules generate automatically, with pause and resume.')],
                    ['credit-card', __('Payment tracking'), __('Partial or full payments; balances update automatically.')],
                    ['bell', __('Collection reminders'), __('Automatic schedules plus one-click nudges from aging.')],
                    ['chart-bar', __('Financial reports'), __('Aging, cash flow, P&L, and tax from your own data.')],
                    ['template', __('Print-ready templates'), __('Multiple layouts with your brand colors and logo.')],
                ] as [$icon, $title, $desc])
                    <div class="col-sm-6 d-flex gap-3">
                        <span class="avatar avatar-md"><x-ti :name="$icon" /></span>
                        <div><dt class="fw-semibold">{{ $title }}</dt><dd class="text-muted small mb-0">{{ $desc }}</dd></div>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>

    {{-- EDITORIAL: alternating tint + checklist/metric, right side is workflow not a card --}}
    <div class="page-header mb-3">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ __('Who it fits') }}</div>
                <h2 class="page-title">{{ __('Built around how you bill') }}</h2>
            </div>
        </div>
    </div>

    @foreach([
        ['01', __('Freelancers'), __('Send an invoice the day work ships. Share a link, get notified on view, nudge automatically when overdue.'), [__('Monday: send 3 invoices'), __('Wednesday: 2 paid, 1 reminded'), __('Friday: review the aging report')], 'Rp 4,2 jt', __('collected this week (sample)')],
        ['02', __('Agencies & consultants'), __('Per-client workspaces, quotes that convert to invoices, partial payments, reports your accountant accepts.'), [__('Quote → invoice in one click'), __('Partial payments tracked'), __('P&L per workspace')], '3', __('workspaces isolated (sample)')],
        ['03', __('Retainers & subscriptions'), __('Recurring schedules with pause and resume, proration by line items, dunning reminders before payments fail.'), [__('Pause / resume anytime'), __('Auto-generate on schedule'), __('Overdue auto-reminders')], '12×', __('invoices / year on autopilot')],
    ] as $i => [$no, $title, $desc, $steps, $metric, $metricLabel])
        <div class="{{ $i % 2 === 1 ? '' : 'landing-tint' }} mb-4" @if($i % 2 === 1) style="border: 1px solid var(--tblr-border-color); border-radius: 1.5rem; padding: clamp(1.5rem, 4vw, 2.5rem);" @endif>
            <div class="row g-4 align-items-center">
                <div class="col-lg-6 {{ $i % 2 === 1 ? 'order-lg-2' : '' }}">
                    <div class="subheader text-primary mb-1">{{ $no }}</div>
                    <h3 class="h1 mb-2">{{ $title }}</h3>
                    <p class="text-muted">{{ $desc }}</p>
                    <ul class="list-unstyled mb-3">
                        @foreach($steps as $s)
                            <li class="d-flex gap-2 mb-1"><x-ti name="check" class="text-success mt-1" /><span>{{ $s }}</span></li>
                        @endforeach
                    </ul>
                    <a href="{{ route('pricing') }}" class="link-primary fw-semibold">{{ __('See plans for :who →', ['who' => strtolower($title)]) }}</a>
                </div>
                <div class="col-lg-6 {{ $i % 2 === 1 ? 'order-lg-1' : '' }}">
                    <div class="p-4 rounded-3 text-center" style="background: linear-gradient(135deg, #141b33, #2c2a72); color: #fff;">
                        <div class="display-4 fw-bold">{{ $metric }}</div>
                        <div class="text-muted" style="color: rgba(255,255,255,.78) !important;">{{ $metricLabel }}</div>
                        <div class="mt-3 d-flex justify-content-center gap-2">
                            <span class="badge bg-white-lt text-white">{{ __('Illustrative') }}</span>
                            <span class="badge bg-white-lt text-white">{{ $title }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- LIFECYCLE: numbered steps on a rule, no nested cards --}}
    <div class="py-4 mb-5 border-top border-bottom">
        <h3 class="h2 mb-1">{{ __('From work to cash in four steps') }}</h3>
        <p class="text-muted mb-4">{{ __('A straight line from effort to money in the bank.') }}</p>
        <ol class="row g-3 list-unstyled mb-0">
            @foreach([
                [__('Create'), __('Pick a customer, add line items, set terms.')],
                [__('Send'), __('Email with PDF, or a rotatable secure link.')],
                [__('Collect'), __('Partial and full payments, gateway or manual.')],
                [__('Understand'), __('Aging, cash flow, and P&L from your own data.')],
            ] as $i => [$title, $desc])
                <li class="col-sm-6 col-lg-3 d-flex gap-3">
                    <span class="display-6 fw-bold text-primary">0{{ $i + 1 }}</span>
                    <div><div class="fw-semibold">{{ $title }}</div><div class="text-muted small">{{ $desc }}</div></div>
                </li>
            @endforeach
        </ol>
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

    {{-- FAQ: plain accordion without outer card --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <h3 class="h2 mb-2">{{ __('Questions, answered') }}</h3>
            <p class="text-muted">{{ __('Honest answers — no fine-print surprises.') }}</p>
        </div>
        <div class="col-lg-8">
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

    <div class="py-4 border-top">
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
</x-tabler.public>

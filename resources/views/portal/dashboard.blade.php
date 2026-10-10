<x-tabler.public :title="__('Customer Portal')">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
    @endpush

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="subheader">{{ __('Hi, :email', ['email' => $portalUser->email]) }}</div>
                    <h1 class="page-title">{{ $client->company_name ?: $client->name }}</h1>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <a href="{{ route('portal.dashboard') }}" class="btn btn-primary btn-sm">{{ __('Dashboard') }}</a>
                        <a href="{{ route('portal.invoices') }}" class="btn btn-sm">{{ __('Invoices') }}</a>
                        <a href="{{ route('portal.quotes') }}" class="btn btn-sm">{{ __('Quotes') }}</a>
                        <form method="POST" action="{{ route('portal.logout') }}" class="d-inline">@csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <x-tabler.stat :label="__('Outstanding')" :value="number_format($stats['total_outstanding'], 0)" icon="hourglass" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-tabler.stat :label="__('Total Paid')" :value="number_format($stats['total_paid'], 0)" icon="check" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-tabler.stat :label="__('Total Invoices')" :value="$stats['invoice_count']" icon="file-invoice" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-tabler.stat :label="__('Unpaid')" :value="$stats['unpaid_count']" icon="alert-circle" />
        </div>
    </div>
    <p class="text-muted mb-3">{{ __(':count unpaid invoices', ['count' => $stats['unpaid_count']]) }}</p>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Recent Invoices') }}</h3>
            <div class="card-actions"><a href="{{ route('portal.invoices') }}" class="btn btn-sm">{{ __('View all') }} &rarr;</a></div>
        </div>
        @if($invoices->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="file-invoice" :title="__('No invoices yet')" :body="__('New invoices from your provider will appear here.')" />
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($invoices->take(10) as $inv)
                    <a href="{{ route('public.invoice.show', $inv->public_token) }}" class="list-group-item list-group-item-action">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="fw-bold">{{ $inv->invoice_no }}</div>
                                <div class="text-muted">{{ $inv->issue_date->format('d M Y') }} &middot; <span class="badge bg-blue-lt">{{ ucfirst($inv->status) }}</span></div>
                            </div>
                            <div class="col-auto text-end">
                                <div class="fw-bold">{{ number_format($inv->total, 0) }}</div>
                                @if($inv->balance_due > 0)
                                    <div class="text-danger">{{ __('Balance :amount', ['amount' => number_format($inv->balance_due, 0)]) }}</div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-tabler.public>

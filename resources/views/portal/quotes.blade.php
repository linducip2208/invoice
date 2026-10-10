<x-tabler.public :title="__('My Quotes')">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
    @endpush

    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title"><x-ti name="file-text" /> {{ __('My Quotes') }}</h1>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ route('portal.dashboard') }}" class="btn btn-sm">{{ __('Dashboard') }}</a>
                    <a href="{{ route('portal.invoices') }}" class="btn btn-sm">{{ __('Invoices') }}</a>
                    <form method="POST" action="{{ route('portal.logout') }}" class="d-inline">@csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($quotes->isEmpty())
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="file-text" :title="__('No quotes yet')" :body="__('New quotes from your provider will appear here.')" />
            </div>
        </div>
    @else
        <div class="card">
            <div class="list-group list-group-flush">
                @foreach($quotes as $q)
                    <a href="{{ route('public.quote.show', $q->public_token) }}" class="list-group-item list-group-item-action">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="fw-bold">{{ $q->quote_no }}</div>
                                <div class="text-muted">{{ $q->issue_date->format('d M Y') }} &middot; <span class="badge bg-blue-lt">{{ ucfirst($q->status) }}</span></div>
                            </div>
                            <div class="col-auto fw-bold">{{ number_format($q->total, 0) }} {{ $q->currency }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
            @if($quotes->hasPages())
                <div class="card-footer d-flex justify-content-center">{{ $quotes->links() }}</div>
            @endif
        </div>
    @endif
</x-tabler.public>

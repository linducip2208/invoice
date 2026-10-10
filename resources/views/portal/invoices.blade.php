<x-tabler.public :title="__('My Invoices')">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
    @endpush

    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h1 class="page-title"><x-ti name="file-invoice" /> {{ __('My Invoices') }}</h1>
            </div>
            <div class="col-auto">
                <div class="btn-list">
                    <a href="{{ route('portal.dashboard') }}" class="btn btn-sm">{{ __('Dashboard') }}</a>
                    <a href="{{ route('portal.quotes') }}" class="btn btn-sm">{{ __('Quotes') }}</a>
                    <form method="POST" action="{{ route('portal.logout') }}" class="d-inline">@csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('No') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Due') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                        <th class="text-end">{{ __('Balance') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="fw-bold">{{ $inv->invoice_no }}</td>
                            <td class="text-muted">{{ $inv->issue_date->format('d M Y') }}</td>
                            <td class="text-muted">{{ $inv->due_date->format('d M Y') }}</td>
                            <td class="text-end fw-bold">{{ number_format($inv->total, 0) }} {{ $inv->currency }}</td>
                            <td class="text-end {{ $inv->balance_due > 0 ? 'text-danger' : 'text-muted' }}">{{ $inv->balance_due > 0 ? number_format($inv->balance_due, 0) : '—' }}</td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ ucfirst($inv->status) }}</span></td>
                            <td class="text-end"><a href="{{ route('public.invoice.show', $inv->public_token) }}" class="btn btn-sm">{{ __('View') }} &rarr;</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-tabler.empty icon="file-invoice" :title="__('No invoices')" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $invoices->links() }}</div>
        @endif
    </div>
</x-tabler.public>

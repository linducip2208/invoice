<x-tabler.layout :title="$quote->quote_no" :pretitle="__('Sales')" :breadcrumbs="[['label' => __('Quotations'), 'url' => route('quotes.index')], ['label' => $quote->quote_no]]">
    <x-slot name="actions">
        <a href="{{ route('quotes.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
        @if($quote->status === 'draft')
            <form method="POST" action="{{ route('quotes.send', $quote) }}" class="d-inline">@csrf<button class="btn btn-primary"><x-ti name="send" /> {{ __('Send to client') }}</button></form>
        @endif
        @if(in_array($quote->status, ['draft', 'sent']))
            <a href="{{ route('public.quote.show', $quote->public_token) }}" target="_blank" rel="noopener" class="btn"><x-ti name="eye" /> {{ __('Public link') }}</a>
            <form method="POST" action="{{ route('quotes.withdraw', $quote) }}" class="d-inline" onsubmit="return confirm({{ Js::from(__('Withdraw this quote? Outstanding links and codes become invalid.')) }});">@csrf<button class="btn btn-outline-warning">{{ __('Withdraw') }}</button></form>
        @endif
        @if(in_array($quote->status, ['rejected', 'withdrawn', 'expired']))
            <form method="POST" action="{{ route('quotes.reopen', $quote) }}" class="d-inline">@csrf<button class="btn btn-outline-info">{{ __('Reopen as new revision') }}</button></form>
        @endif
        @if(in_array($quote->status, ['accepted', 'converted']))
            <form method="POST" action="{{ route('quotes.convert', $quote) }}" class="d-inline">@csrf<button class="btn btn-primary"><x-ti name="file-invoice" /> {{ $quote->status === 'converted' ? __('View Invoice') : __('Convert to Invoice') }}</button></form>
        @endif
        <form method="POST" action="{{ route('quotes.rotate-link', $quote) }}" class="d-inline" onsubmit="return confirm({{ Js::from(__('Rotate the public link? The old link stops working.')) }});">@csrf<button class="btn btn-sm btn-ghost-secondary" title="{{ __('Rotate public link') }}"><x-ti name="refresh" /></button></form>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center g-3 mb-3">
                <div class="col">
                    <p class="subheader mb-1">{{ __('Client') }}</p>
                    <p class="h3 mb-0">{{ $quote->client?->name }}</p>
                    <p class="text-muted small mb-0">{{ __('Issue:') }} {{ $quote->issue_date?->format('d M Y') }} · {{ __('Expiry:') }} {{ $quote->expiry_date?->format('d M Y') ?? '—' }}</p>
                </div>
                <div class="col-auto">
                    <x-tabler.status-badge :status="$quote->status" />
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Description') }}</th>
                        <th class="text-end">{{ __('Qty') }}</th>
                        <th class="text-end">{{ __('Price') }}</th>
                        <th class="text-end">{{ __('Tax%') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items ?? [] as $i)
                        <tr>
                            <td>{{ $i['description'] }}</td>
                            <td class="text-end">{{ $i['quantity'] }}</td>
                            <td class="text-end">{{ number_format($i['unit_price'], 2) }}</td>
                            <td class="text-end text-muted">{{ $i['tax_rate'] ?? 0 }}%</td>
                            <td class="text-end fw-bold">{{ number_format(($i['quantity'] * $i['unit_price']), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">{{ __('Subtotal:') }}</span><span>{{ number_format($quote->subtotal, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">{{ __('Tax:') }}</span><span>{{ number_format($quote->tax_amount, 2) }}</span></div>
                    <div class="d-flex justify-content-between h3 mb-0"><span>{{ __('Total:') }}</span><span>{{ $quote->currency }} {{ number_format($quote->total, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    @if($quote->notes)
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="subheader mb-2">{{ __('Notes:') }}</h4>
                <p class="mb-0">{{ $quote->notes }}</p>
            </div>
        </div>
    @endif
    @if($quote->terms)
        <div class="card">
            <div class="card-body">
                <h4 class="subheader mb-2">{{ __('Terms:') }}</h4>
                <p class="mb-0">{{ $quote->terms }}</p>
            </div>
        </div>
    @endif

    @php $events = $quote->approvalEvents()->latest('decided_at')->get(); @endphp
    @if($events->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title"><x-ti name="history" /> {{ __('Approval history') }}</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>{{ __('When (UTC)') }}</th><th>{{ __('Outcome') }}</th><th>{{ __('Revision') }}</th><th>{{ __('Approver') }}</th><th>{{ __('Via') }}</th><th>{{ __('Document hash') }}</th></tr></thead>
                    <tbody>
                        @foreach($events as $ev)
                            <tr>
                                <td class="text-muted">{{ $ev->decided_at }}</td>
                                <td><x-tabler.status-badge :status="$ev->outcome === 'accepted' ? 'accepted' : 'rejected'" /></td>
                                <td>v{{ $ev->revision }}</td>
                                <td>{{ $ev->approver_name }} <span class="text-muted small">({{ $ev->approver_email }})</span></td>
                                <td class="text-muted">{{ $ev->verification_method }} · {{ $ev->authority_basis }}</td>
                                <td><code class="small">{{ substr($ev->document_sha256, 0, 16) }}…</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-tabler.layout>

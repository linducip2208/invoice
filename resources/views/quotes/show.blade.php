<x-tabler.layout :title="$quote->quote_no" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('quotes.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
        @if($quote->status !== 'converted')
            <form method="POST" action="{{ route('quotes.convert', $quote) }}" class="d-inline">@csrf<button class="btn btn-primary"><x-ti name="file-invoice" /> {{ __('Convert to Invoice') }}</button></form>
        @else
            <a href="{{ route('invoices.show', $quote->converted_invoice_id) }}" class="btn btn-primary">{{ __('View Invoice') }}</a>
        @endif
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
</x-tabler.layout>

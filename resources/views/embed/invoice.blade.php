<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('Invoice :no', ['no' => $invoice->invoice_no]) }}</title>
    @vite(['resources/css/tabler.css'])
    <style>
        body { margin: 0; padding: 1rem; }
        .embed-wrap { max-width: 38rem; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="embed-wrap">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">{{ $invoice->workspace?->name }}</h3>
                    <div class="text-muted">{{ $invoice->invoice_no }}</div>
                </div>
                <div class="card-actions text-end">
                    <div class="text-muted">{{ __('Due :date', ['date' => $invoice->due_date->format('d M Y')]) }}</div>
                    <div class="h3 mb-1">{{ number_format($invoice->total, 0) }} {{ $invoice->currency }}</div>
                    <span class="badge {{ in_array($invoice->status, ['paid']) ? 'bg-green-lt' : (in_array($invoice->status, ['overdue']) ? 'bg-red-lt' : 'bg-yellow-lt') }}">{{ strtoupper($invoice->status) }}</span>
                </div>
            </div>
            <div class="card-body">
                <p class="fw-semibold mb-2">{{ $invoice->client?->name }}</p>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr><th>{{ __('Description') }}</th><th class="text-end">{{ __('Qty') }}</th><th class="text-end">{{ __('Total') }}</th></tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->total, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($invoice->status !== 'paid')
                    <div class="text-center mt-3">
                        <a href="{{ route('public.invoice.show', $invoice->public_token) }}" target="_top" class="btn btn-primary"><x-ti name="credit-card" /> {{ __('Pay Now') }}</a>
                    </div>
                @endif
                <p class="text-center text-muted mt-3 mb-0">{{ __('Powered by') }} <a href="{{ url('/') }}" target="_top" class="fw-semibold">Invocely</a></p>
            </div>
        </div>
    </div>
</body>
</html>

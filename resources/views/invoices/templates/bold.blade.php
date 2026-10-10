<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Arial Black', 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #ffffff; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; background: #ffffff; }
        .dark-header { background: {{ $primaryColor }}; padding: 40px 48px 32px 48px; position: relative; overflow: hidden; }
        .dark-header::after { content: ''; position: absolute; top: -30px; right: -30px; width: 200px; height: 200px; border-radius: 50%; background: rgba(0,0,0,0.08); }
        .header-content { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: flex-start; }
        .header-left h1 { font-size: 26px; font-weight: 900; margin: 0; letter-spacing: -0.5px; }
        .header-left p { font-size: 10px; margin: 4px 0 0 0; opacity: 0.7; }
        @if($logoBase64)
        .header-logo { max-height: 44px; max-width: 140px; margin-bottom: 10px; filter: brightness(0) invert(1); }
        @endif
        .header-right { text-align: right; }
        .header-right .title { font-size: 40px; font-weight: 900; margin: 0; line-height: 1; }
        .header-right .number { font-size: 16px; font-weight: 700; margin: 6px 0 0 0; opacity: 0.9; }
        .content { padding: 32px 48px; color: #111827; }
        .info-grid { display: flex; gap: 40px; margin-bottom: 32px; }
        .info-col { flex: 1; }
        .info-col .label { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: {{ $primaryColor }}; margin: 0 0 8px 0; font-weight: 700; }
        .info-col .name { font-size: 16px; font-weight: 900; margin: 0 0 4px 0; color: #111827; }
        .info-col p { margin: 2px 0; font-size: 11px; color: #374151; }
        .meta-strip { background: #111827; border-radius: 8px; display: flex; padding: 0; margin-bottom: 28px; overflow: hidden; }
        .meta-item { flex: 1; padding: 14px 18px; border-right: 1px solid rgba(255,255,255,0.1); }
        .meta-item:last-child { border-right: none; }
        .meta-item .ml { font-size: 8px; text-transform: uppercase; letter-spacing: 2px; color: #9ca3af; margin: 0; }
        .meta-item .mv { font-size: 13px; font-weight: 800; margin: 3px 0 0 0; color: #ffffff; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        thead th { background: #111827; color: #ffffff; padding: 12px 14px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; text-align: left; font-weight: 700; }
        thead th:last-child, thead th:nth-last-child(2) { text-align: right; }
        tbody td { padding: 11px 14px; border-bottom: 1px solid #e5e7eb; font-size: 11px; font-weight: 500; vertical-align: top; }
        tbody td.text-right { text-align: right; }
        tbody tr { border-left: 3px solid transparent; }
        tbody tr:nth-child(odd) { background: #f9fafb; }
        .total-section { background: {{ $primaryColor }}; color: #ffffff; padding: 20px 28px; width: 340px; margin-left: auto; }
        .total-section .tr { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; font-weight: 500; opacity: 0.9; }
        .total-section .tr.grand { font-size: 18px; font-weight: 900; opacity: 1; border-top: 2px solid rgba(255,255,255,0.3); margin-top: 6px; padding-top: 10px; }
        .notes-row { display: flex; gap: 32px; margin-top: 32px; }
        .notes-col { flex: 1; }
        .notes-col h4 { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: {{ $primaryColor }}; margin: 0 0 6px 0; font-weight: 700; }
        .notes-col p { font-size: 10px; color: #4b5563; margin: 0; line-height: 1.6; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; background: #111827; color: #6b7280; text-align: center; padding: 10px 48px; font-size: 9px; font-weight: 600; }
        .pgn:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="dark-header">
        <div class="header-content">
            <div class="header-left">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="header-logo">
                @endif
                <h1>{{ $workspace->name }}</h1>
                <p>{{ $workspace->city }}{{ $workspace->state ? ', ' . $workspace->state : '' }} &middot; {{ $workspace->country }}</p>
            </div>
            <div class="header-right">
                <h2 class="title">{{ __("Invoice") }}</h2>
                <p class="number">{{ $invoice->invoice_no }}</p>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="info-grid">
            <div class="info-col">
                <p class="label">{{ __("Bill To") }}</p>
                <p class="name">{{ $client->company_name ?: $client->name }}</p>
                @if($client->company_name)<p>{{ $client->name }}</p>@endif
                @if($client->address)<p>{{ $client->address }}</p>@endif
                <p>{{ $client->city }}@if($client->state), {{ $client->state }}@endif @if($client->postal_code){{ $client->postal_code }}@endif</p>
                @if($client->email)<p>{{ $client->email }}</p>@endif
            </div>
            <div class="info-col">
                <p class="label">{{ __("From") }}</p>
                <p class="name">{{ $workspace->name }}</p>
                @if($workspace->address)<p>{{ $workspace->address }}</p>@endif
                <p>{{ $workspace->city }}@if($workspace->state), {{ $workspace->state }}@endif @if($workspace->postal_code){{ $workspace->postal_code }}@endif</p>
                @if($workspace->phone)<p>{{ $workspace->phone }}</p>@endif
            </div>
        </div>

        <div class="meta-strip">
            <div class="meta-item"><p class="ml">{{ __("Issue Date") }}</p><p class="mv">{{ $invoice->issue_date->format('M d, Y') }}</p></div>
            <div class="meta-item"><p class="ml">{{ __("Due Date") }}</p><p class="mv">{{ $invoice->due_date->format('M d, Y') }}</p></div>
            <div class="meta-item"><p class="ml">{{ __("Status") }}</p><p class="mv" style="text-transform:uppercase;">{{ $invoice->status }}</p></div>
            <div class="meta-item"><p class="ml">{{ __("Currency") }}</p><p class="mv">{{ $invoice->currency }}</p></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:40%;">{{ __("Description") }}</th>
                    <th style="width:10%;text-align:center;">{{ __("Qty") }}</th>
                    <th style="width:15%;text-align:right;">{{ __("Unit Price") }}</th>
                    <th style="width:10%;text-align:right;">{{ __("Tax") }}</th>
                    <th style="width:20%;text-align:right;">{{ __("Total") }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->description ?: $item->product?->name }}</td>
                        <td style="text-align:center;">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->tax_rate ?? 0, 2) }}%</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; color:#9ca3af; padding:32px;">{{ __("No items") }}</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="total-section">
            <div class="tr"><span>{{ __("Subtotal") }}</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
            @if($invoice->tax_amount > 0)
                <div class="tr"><span>Tax ({{ number_format($invoice->tax_rate, 2) }}%)</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>
            @endif
            @if($invoice->discount_amount > 0)
                <div class="tr"><span>{{ __("Discount") }} @if($invoice->discount_type === 'percent') ({{ number_format($invoice->discount_value, 2) }}%) @endif</span><span>-{{ number_format($invoice->discount_amount, 2) }}</span></div>
            @endif
            <div class="tr grand"><span>{{ __("Total") }}</span><span>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</span></div>
            @if($invoice->amount_paid > 0)
            <div class="tr"><span>{{ __("Paid") }}</span><span>-{{ number_format($invoice->amount_paid, 2) }}</span></div>
            <div class="tr grand"><span>{{ __("Balance Due") }}</span><span>{{ number_format($invoice->balance_due, 2) }}</span></div>
            @endif
        </div>

        @if($invoice->notes || $invoice->terms)
        <div class="notes-row">
            @if($invoice->notes)
            <div class="notes-col"><h4>{{ __("Notes") }}</h4><p>{{ $invoice->notes }}</p></div>
            @endif
            @if($invoice->terms)
            <div class="notes-col"><h4>terms &amp; Conditions</h4><p>{{ $invoice->terms }}</p></div>
            @endif
        </div>
        @endif
    </div>

    <div class="footer">{{ $workspace->name }} &mdash; Page <span class="pgn"></span></div>

    <x-free-tier-watermark :invoice="$invoice" />
</body>
</html>

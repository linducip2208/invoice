<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; }
        .hero { background: {{ $primaryColor }}; padding: 36px 48px 28px 48px; color: #ffffff; }
        .hero-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .hero-left { flex: 1; }
        .hero-left .company { font-size: 20px; font-weight: 700; margin: 0 0 6px 0; }
        .hero-left .address { font-size: 9px; opacity: 0.8; line-height: 1.4; margin: 0; max-width: 280px; }
        .hero-right { text-align: right; }
        .hero-right .title { font-size: 32px; font-weight: 800; margin: 0; letter-spacing: 2px; text-transform: uppercase; }
        .hero-right .number { font-size: 14px; font-weight: 500; margin: 4px 0 0 0; opacity: 0.9; }
        @if($logoBase64)
        .hero-logo { max-height: 40px; max-width: 140px; margin-bottom: 10px; }
        @endif
        .content { padding: 32px 48px; }
        .two-col { display: flex; gap: 40px; margin-bottom: 28px; }
        .col { flex: 1; }
        .col-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; margin: 0 0 8px 0; font-weight: 700; }
        .col-name { font-size: 15px; font-weight: 700; margin: 0 0 4px 0; }
        .col p { margin: 2px 0; font-size: 11px; color: #475569; }
        .meta-grid { display: flex; gap: 24px; }
        .meta-box { flex: 1; background: #f8fafc; border-radius: 6px; padding: 10px 14px; }
        .meta-box .m-label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin: 0; }
        .meta-box .m-value { font-size: 12px; font-weight: 700; margin: 2px 0 0 0; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin: 24px 0; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        thead th { background: {{ $primaryColor }}; color: #ffffff; padding: 12px 14px; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; text-align: left; }
        thead th:last-child, thead th:nth-last-child(2) { text-align: right; }
        tbody td { padding: 11px 14px; border-bottom: 1px solid #f1f5f9; font-size: 11px; vertical-align: top; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td.text-right { text-align: right; }
        .total-box { background: {{ $primaryColor }}; color: #ffffff; border-radius: 6px; padding: 16px 24px; width: 320px; margin-left: auto; }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 11px; opacity: 0.85; }
        .total-row.grand { font-size: 16px; font-weight: 800; opacity: 1; border-top: 1px solid rgba(255,255,255,0.3); margin-top: 6px; padding-top: 8px; }
        .notes-row { display: flex; gap: 32px; margin-top: 28px; }
        .note-col { flex: 1; }
        .note-col h4 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: {{ $primaryColor }}; margin: 0 0 6px 0; font-weight: 700; }
        .note-col p { font-size: 10px; color: #64748b; margin: 0; line-height: 1.6; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; background: #1e293b; color: #94a3b8; text-align: center; padding: 10px 48px; font-size: 9px; }
        .footer span:after { content: counter(page); }
        .status-badge { display: inline-block; background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 12px; font-size: 10px; text-transform: uppercase; font-weight: 600; }
    </style>
</head>
<body>
    <div class="hero">
        <div class="hero-top">
            <div class="hero-left">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="hero-logo">
                @endif
                <h1 class="company">{{ $workspace->name }}</h1>
                <p class="address">{{ $workspace->address }} &middot; {{ $workspace->city }}{{ $workspace->state ? ', ' . $workspace->state : '' }} {{ $workspace->postal_code }}</p>
            </div>
            <div class="hero-right">
                <h2 class="title">{{ __("Invoice") }}</h2>
                <p class="number">{{ $invoice->invoice_no }}</p>
                <span class="status-badge">{{ $invoice->status }}</span>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="two-col">
            <div class="col">
                <p class="col-label">{{ __("Bill To") }}</p>
                <p class="col-name">{{ $client->company_name ?: $client->name }}</p>
                @if($client->company_name)<p>{{ $client->name }}</p>@endif
                @if($client->address)<p>{{ $client->address }}</p>@endif
                <p>{{ $client->city }}@if($client->state), {{ $client->state }}@endif @if($client->postal_code){{ $client->postal_code }}@endif</p>
                @if($client->country)<p>{{ $client->country }}</p>@endif
                @if($client->email)<p>{{ $client->email }}</p>@endif
                @if($client->phone)<p>{{ $client->phone }}</p>@endif
            </div>
            <div class="col">
                <p class="col-label">{{ __("From") }}</p>
                <p class="col-name">{{ $workspace->name }}</p>
                @if($workspace->address)<p>{{ $workspace->address }}</p>@endif
                <p>{{ $workspace->city }}@if($workspace->state), {{ $workspace->state }}@endif @if($workspace->postal_code){{ $workspace->postal_code }}@endif</p>
                @if($workspace->country)<p>{{ $workspace->country }}</p>@endif
                @if($workspace->email)<p>{{ $workspace->email }}</p>@endif
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-box"><p class="m-label">{{ __("Issue Date") }}</p><p class="m-value">{{ $invoice->issue_date->format('M d, Y') }}</p></div>
            <div class="meta-box"><p class="m-label">{{ __("Due Date") }}</p><p class="m-value">{{ $invoice->due_date->format('M d, Y') }}</p></div>
            <div class="meta-box"><p class="m-label">{{ __("Currency") }}</p><p class="m-value">{{ $invoice->currency }}</p></div>
            <div class="meta-box"><p class="m-label">{{ __("Tax ID") }}</p><p class="m-value">{{ $workspace->tax_id ?: '—' }}</p></div>
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
                    <tr><td colspan="6" style="text-align:center; color:#94a3b8; padding:32px;">{{ __("No items") }}</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-row"><span>{{ __("Subtotal") }}</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
            @if($invoice->tax_amount > 0)
                <div class="total-row"><span>Tax ({{ number_format($invoice->tax_rate, 2) }}%)</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>
            @endif
            @if($invoice->discount_amount > 0)
                <div class="total-row"><span>{{ __("Discount") }} @if($invoice->discount_type === 'percent') ({{ number_format($invoice->discount_value, 2) }}%) @endif</span><span>-{{ number_format($invoice->discount_amount, 2) }}</span></div>
            @endif
            <div class="total-row grand"><span>{{ __("Total") }}</span><span>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</span></div>
            @if($invoice->amount_paid > 0)
            <div class="total-row"><span>{{ __("Paid") }}</span><span>-{{ number_format($invoice->amount_paid, 2) }}</span></div>
            <div class="total-row grand"><span>{{ __("Balance Due") }}</span><span>{{ number_format($invoice->balance_due, 2) }}</span></div>
            @endif
        </div>

        @if($invoice->notes || $invoice->terms)
        <div class="notes-row">
            @if($invoice->notes)
            <div class="note-col">
                <h4>{{ __("Notes") }}</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif
            @if($invoice->terms)
            <div class="note-col">
                <h4>terms &amp; Conditions</h4>
                <p>{{ $invoice->terms }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <div class="footer">
        {{ $workspace->name }} &mdash; Page <span></span>
    </div>

    <x-free-tier-watermark :invoice="$invoice" />
</body>
</html>

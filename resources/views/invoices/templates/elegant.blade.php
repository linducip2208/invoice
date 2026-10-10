<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 48px 52px 60px 52px; }
        body { font-family: 'Georgia', 'Times New Roman', Times, serif; color: #2d2a26; font-size: 12px; line-height: 1.7; margin: 0; padding: 0; }
        .header { border: 2px solid {{ $primaryColor }}; padding: 28px 32px; margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; }
        .header-left { flex: 1; }
        @if($logoBase64)
        .logo { max-height: 48px; max-width: 160px; margin-bottom: 8px; }
        @endif
        .company { font-size: 20px; font-weight: 700; margin: 0; font-family: 'Georgia', serif; color: {{ $primaryColor }}; }
        .subtitle { font-size: 10px; color: #8c8279; margin: 4px 0 0 0; font-style: italic; }
        .header-right { text-align: right; border-left: 2px solid {{ $accentColor }}; padding-left: 24px; }
        .invoice-label { font-size: 10px; text-transform: uppercase; letter-spacing: 3px; color: #8c8279; margin: 0; }
        .invoice-title { font-size: 28px; font-weight: 700; margin: 0; color: {{ $accentColor }}; font-family: 'Georgia', serif; }
        .invoice-no { font-size: 13px; font-weight: 600; margin: 4px 0 0 0; }
        .divider-line { border: none; border-top: 1px solid {{ $accentColor }}; margin: 28px 0; opacity: 0.4; }
        .address-row { display: flex; gap: 48px; margin-bottom: 28px; }
        .address-col { flex: 1; padding: 16px 20px; border: 1px solid #e3ddd6; background: #fdfcf9; }
        .address-col h4 { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: {{ $accentColor }}; margin: 0 0 8px 0; }
        .address-col .name { font-size: 14px; font-weight: 700; margin: 0 0 4px 0; }
        .address-col p { margin: 2px 0; font-size: 11px; color: #5c554d; }
        .meta-row { display: flex; gap: 24px; margin-bottom: 28px; }
        .meta-item { flex: 1; text-align: center; padding: 10px; border-bottom: 2px solid {{ $primaryColor }}; }
        .meta-item .ml { font-size: 8px; text-transform: uppercase; letter-spacing: 2px; color: #8c8279; margin: 0 0 4px 0; }
        .meta-item .mv { font-size: 12px; font-weight: 700; margin: 0; color: #2d2a26; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        thead { border-top: 2px solid {{ $primaryColor }}; border-bottom: 2px solid {{ $primaryColor }}; }
        thead th { padding: 12px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: 1.5px; text-align: left; color: {{ $accentColor }}; font-weight: 600; }
        thead th:last-child, thead th:nth-last-child(2) { text-align: right; }
        tbody td { padding: 11px 10px; border-bottom: 1px solid #e3ddd6; font-size: 11px; vertical-align: top; }
        tbody td.text-right { text-align: right; }
        .totals { width: 320px; margin-left: auto; border: 1px solid #e3ddd6; padding: 16px 20px; background: #fdfcf9; }
        .totals .row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 11px; }
        .totals .row.grand { border-top: 1px solid {{ $primaryColor }}; margin-top: 8px; padding-top: 10px; font-size: 15px; font-weight: 700; color: {{ $primaryColor }}; }
        .notes-section { margin-top: 32px; display: flex; gap: 40px; }
        .notes-col { flex: 1; padding: 14px 18px; border-left: 3px solid {{ $accentColor }}; background: #fdfcf9; }
        .notes-col h4 { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: {{ $accentColor }}; margin: 0 0 6px 0; }
        .notes-col p { font-size: 10px; color: #5c554d; margin: 0; line-height: 1.7; font-style: italic; }
        .footer { position: fixed; bottom: -36px; left: 0; right: 0; text-align: center; font-size: 9px; color: #b8b0a4; font-style: italic; border-top: 1px solid #e3ddd6; padding-top: 8px; }
        .footer span:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
            @endif
            <h1 class="company">{{ $workspace->name }}</h1>
            <p class="subtitle">{{ $workspace->city }}{{ $workspace->state ? ', ' . $workspace->state : '' }} &mdash; {{ $workspace->country }}</p>
        </div>
        <div class="header-right">
            <p class="invoice-label">Document</p>
            <h2 class="invoice-title">{{ __("Invoice") }}</h2>
            <p class="invoice-no">{{ $invoice->invoice_no }}</p>
        </div>
    </div>

    <div class="address-row">
        <div class="address-col">
            <h4>{{ __("From") }}</h4>
            <p class="name">{{ $workspace->name }}</p>
            @if($workspace->address)<p>{{ $workspace->address }}</p>@endif
            <p>{{ $workspace->city }}@if($workspace->state), {{ $workspace->state }}@endif @if($workspace->postal_code){{ $workspace->postal_code }}@endif</p>
            @if($workspace->email)<p>{{ $workspace->email }}</p>@endif
            @if($workspace->tax_id)<p>Tax ID: {{ $workspace->tax_id }}</p>@endif
        </div>
        <div class="address-col">
            <h4>{{ __("Bill To") }}</h4>
            <p class="name">{{ $client->company_name ?: $client->name }}</p>
            @if($client->company_name)<p>{{ $client->name }}</p>@endif
            @if($client->address)<p>{{ $client->address }}</p>@endif
            <p>{{ $client->city }}@if($client->state), {{ $client->state }}@endif @if($client->postal_code){{ $client->postal_code }}@endif</p>
            @if($client->email)<p>{{ $client->email }}</p>@endif
        </div>
    </div>

    <div class="meta-row">
        <div class="meta-item"><p class="ml">{{ __("Issue Date") }}</p><p class="mv">{{ $invoice->issue_date->format('M d, Y') }}</p></div>
        <div class="meta-item"><p class="ml">{{ __("Due Date") }}</p><p class="mv">{{ $invoice->due_date->format('M d, Y') }}</p></div>
        <div class="meta-item"><p class="ml">{{ __("Status") }}</p><p class="mv" style="text-transform:uppercase;">{{ $invoice->status }}</p></div>
        <div class="meta-item"><p class="ml">{{ __("Currency") }}</p><p class="mv">{{ $invoice->currency }}</p></div>
    </div>

    <div class="divider-line"></div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:45%;">Item &amp; Description</th>
                <th style="width:10%;text-align:center;">{{ __("Qty") }}</th>
                <th style="width:15%;text-align:right;">{{ __("Unit Price") }}</th>
                <th style="width:25%;text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description ?: $item->product?->name }}</td>
                    <td style="text-align:center;">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; color:#b8b0a4; padding:32px; font-style:italic;">No items on this invoice</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <div class="row"><span>{{ __("Subtotal") }}</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
        @if($invoice->tax_amount > 0)
            <div class="row"><span>Tax ({{ number_format($invoice->tax_rate, 2) }}%)</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>
        @endif
        @if($invoice->discount_amount > 0)
            <div class="row"><span>{{ __("Discount") }} @if($invoice->discount_type === 'percent') ({{ number_format($invoice->discount_value, 2) }}%) @endif</span><span>-{{ number_format($invoice->discount_amount, 2) }}</span></div>
        @endif
        <div class="row grand"><span>Total ({{ $invoice->currency }})</span><span>{{ number_format($invoice->total, 2) }}</span></div>
        @if($invoice->amount_paid > 0)
        <div class="row"><span>{{ __("Paid") }}</span><span>-{{ number_format($invoice->amount_paid, 2) }}</span></div>
        <div class="row grand"><span>{{ __("Balance Due") }}</span><span>{{ number_format($invoice->balance_due, 2) }}</span></div>
        @endif
    </div>

    @if($invoice->notes || $invoice->terms)
    <div class="notes-section">
        @if($invoice->notes)
        <div class="notes-col"><h4>{{ __("Notes") }}</h4><p>{{ $invoice->notes }}</p></div>
        @endif
        @if($invoice->terms)
        <div class="notes-col"><h4>terms &amp; Conditions</h4><p>{{ $invoice->terms }}</p></div>
        @endif
    </div>
    @endif

    <div class="footer">
        {{ $workspace->name }} &mdash; Thank you for your business &mdash; Page <span></span>
    </div>

    <x-free-tier-watermark :invoice="$invoice" />
</body>
</html>

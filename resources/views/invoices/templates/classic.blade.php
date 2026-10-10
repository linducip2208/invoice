<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 40px 40px 60px 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1f2937; font-size: 12px; line-height: 1.5; margin: 0; padding: 0; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 16px; margin-bottom: 24px; }
        .header-left { max-width: 60%; }
        .company-name { font-size: 22px; font-weight: 700; color: {{ $primaryColor }}; margin: 0 0 4px 0; }
        .company-address { font-size: 10px; color: #6b7280; line-height: 1.4; margin: 0; }
        .header-right { text-align: right; }
        .invoice-title { font-size: 28px; font-weight: 800; color: {{ $primaryColor }}; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        .invoice-no { font-size: 13px; color: #374151; font-weight: 600; margin: 0; }
        .info-row { display: flex; gap: 40px; margin-bottom: 28px; }
        .info-block { flex: 1; }
        .info-block h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin: 0 0 6px 0; font-weight: 600; }
        .info-block p { margin: 2px 0; font-size: 11px; }
        .info-block .name { font-weight: 700; font-size: 13px; }
        .meta-row { display: flex; gap: 32px; margin-bottom: 24px; }
        .meta-item { flex: 1; }
        .meta-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin: 0 0 2px 0; }
        .meta-value { font-size: 12px; font-weight: 600; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead th { background: {{ $primaryColor }}; color: #ffffff; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
        thead th:last-child, thead th:nth-last-child(2) { text-align: right; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 11px; vertical-align: top; }
        tbody td.text-right { text-align: right; }
        tbody tr:last-child td { border-bottom: 2px solid {{ $primaryColor }}; }
        .totals { width: 300px; margin-left: auto; }
        .totals table { width: 100%; margin-bottom: 0; }
        .totals td { padding: 6px 10px; font-size: 11px; border-bottom: none; }
        .totals td:last-child { text-align: right; font-weight: 600; }
        .totals .grand-total td { font-size: 14px; font-weight: 800; color: {{ $primaryColor }}; border-top: 2px solid {{ $primaryColor }}; padding-top: 8px; }
        .notes-section { margin-top: 32px; display: flex; gap: 32px; }
        .notes-block { flex: 1; }
        .notes-block h4 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin: 0 0 6px 0; }
        .notes-block p { font-size: 10px; color: #4b5563; margin: 0; line-height: 1.5; }
        .footer { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .footer .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" style="max-height:45px; max-width:150px; margin-bottom:8px;">
            @endif
            <h1 class="company-name">{{ $workspace->name }}</h1>
            <p class="company-address">
                {{ $workspace->address }}@if($workspace->city), {{ $workspace->city }}@endif @if($workspace->state){{ $workspace->state }}@endif @if($workspace->postal_code){{ $workspace->postal_code }}@endif<br>
                @if($workspace->phone){{ __('Phone:') }} {{ $workspace->phone }}<br>@endif
                @if($workspace->email){{ __('Email:') }} {{ $workspace->email }}<br>@endif
                @if($workspace->tax_id){{ __('Tax ID:') }} {{ $workspace->tax_id }}@endif
            </p>
        </div>
        <div class="header-right">
            <h2 class="invoice-title">{{ __('Invoice') }}</h2>
            <p class="invoice-no">{{ $invoice->invoice_no }}</p>
        </div>
    </div>

    <div class="info-row">
        <div class="info-block">
            <h3>{{ __('Bill To') }}</h3>
            <p class="name">{{ $client->company_name ?: $client->name }}</p>
            @if($client->company_name)<p>{{ $client->name }}</p>@endif
            <p>{{ $client->address }}</p>
            @if($client->city || $client->state)<p>{{ $client->city }}@if($client->city && $client->state), @endif{{ $client->state }} @if($client->postal_code){{ $client->postal_code }}@endif</p>@endif
            @if($client->country)<p>{{ $client->country }}</p>@endif
            @if($client->email)<p>{{ $client->email }}</p>@endif
            @if($client->tax_id)<p>{{ __('Tax ID:') }} {{ $client->tax_id }}</p>@endif
        </div>
        <div class="info-block">
            <h3>{{ __('Invoice Details') }}</h3>
            <div class="meta-row">
                <div class="meta-item">
                    <p class="meta-label">{{ __('Issue Date') }}</p>
                    <p class="meta-value">{{ $invoice->issue_date->format('M d, Y') }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">{{ __('Due Date') }}</p>
                    <p class="meta-value">{{ $invoice->due_date->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="meta-row">
                <div class="meta-item">
                    <p class="meta-label">{{ __('Status') }}</p>
                    <p class="meta-value" style="color:{{ $primaryColor }}; text-transform:uppercase;">{{ $invoice->status }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">{{ __('Currency') }}</p>
                    <p class="meta-value">{{ $invoice->currency }}</p>
                </div>
            </div>
        </div>
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
                <tr>
                    <td colspan="6" style="text-align:center; color:#9ca3af; padding:32px;">{{ __("No items") }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>{{ __("Subtotal") }}</td>
                <td>{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
                <tr>
                    <td>{{ __('Tax') }} ({{ number_format($invoice->tax_rate, 2) }}%)</td>
                    <td>{{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->discount_amount > 0)
                <tr>
                    <td>{{ __("Discount") }} @if($invoice->discount_type === 'percent') ({{ number_format($invoice->discount_value, 2) }}%) @endif</td>
                    <td>-{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>{{ __("Total") }}</td>
                <td>{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
            </tr>
            @if($invoice->amount_paid > 0)
            <tr>
                <td>{{ __("Paid") }}</td>
                <td>-{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>{{ __("Balance Due") }}</td>
                <td>{{ number_format($invoice->balance_due, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($invoice->notes || $invoice->terms)
    <div class="notes-section">
        @if($invoice->notes)
        <div class="notes-block">
            <h4>{{ __("Notes") }}</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif
        @if($invoice->terms)
        <div class="notes-block">
            <h4>{{ __('Terms & Conditions') }}</h4>
            <p>{{ $invoice->terms }}</p>
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <span class="page-number"></span> &mdash; {{ $workspace->name }}
    </div>

    <x-free-tier-watermark :invoice="$invoice" />
</body>
</html>

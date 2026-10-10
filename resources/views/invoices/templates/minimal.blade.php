<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 48px 48px 56px 48px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1a1a1a; font-size: 12px; line-height: 1.6; margin: 0; padding: 0; }
        .top-section { text-align: center; margin-bottom: 36px; }
        @if($logoBase64)
        .logo { max-height: 48px; max-width: 160px; margin-bottom: 12px; }
        @endif
        .company { font-size: 18px; font-weight: 300; letter-spacing: 3px; text-transform: uppercase; margin: 0 0 4px 0; color: #1a1a1a; }
        .inv-title { font-size: 11px; font-weight: 400; text-transform: uppercase; letter-spacing: 4px; color: {{ $primaryColor }}; margin: 0 0 8px 0; }
        .inv-no { font-size: 16px; font-weight: 400; letter-spacing: 2px; margin: 0; }
        .info-section { display: flex; gap: 48px; margin-bottom: 36px; }
        .info-left { flex: 1; }
        .info-right { flex: 1; text-align: right; }
        .info-block h4 { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #aaa; margin: 0 0 8px 0; font-weight: 400; }
        .info-block p { margin: 3px 0; font-size: 11px; }
        .info-block .strong { font-weight: 600; }
        .dates { display: flex; justify-content: flex-end; gap: 32px; margin-bottom: 36px; }
        .date-block { text-align: right; }
        .date-block .d-label { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #aaa; margin: 0; }
        .date-block .d-value { font-size: 11px; font-weight: 500; margin: 2px 0 0 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        thead { border-bottom: 2px solid #1a1a1a; }
        thead th { padding: 10px 8px; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; text-align: left; color: #666; font-weight: 500; }
        thead th:last-child, thead th:nth-last-child(2) { text-align: right; }
        tbody td { padding: 10px 8px; border-bottom: 1px solid #f0f0f0; font-size: 11px; vertical-align: top; }
        tbody td.text-right { text-align: right; }
        .totals { width: 280px; margin-left: auto; }
        .totals .row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 11px; }
        .totals .row.total { border-top: 2px solid #1a1a1a; margin-top: 6px; padding-top: 8px; font-size: 14px; font-weight: 700; }
        .notes-section { margin-top: 40px; display: flex; gap: 40px; }
        .notes-col { flex: 1; }
        .notes-col h4 { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #aaa; margin: 0 0 6px 0; font-weight: 400; }
        .notes-col p { font-size: 10px; color: #555; margin: 0; line-height: 1.6; }
        .footer { position: fixed; bottom: -36px; left: 0; right: 0; text-align: center; font-size: 9px; color: #bbb; letter-spacing: 1px; }
        .pgn:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="top-section">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
        @endif
        <h1 class="company">{{ $workspace->name }}</h1>
        <p class="inv-title">{{ __("Invoice") }}</p>
        <p class="inv-no">{{ $invoice->invoice_no }}</p>
    </div>

    <div class="info-section">
        <div class="info-left info-block">
            <h4>{{ __("From") }}</h4>
            <p class="strong">{{ $workspace->name }}</p>
            @if($workspace->address)<p>{{ $workspace->address }}</p>@endif
            <p>{{ $workspace->city }}@if($workspace->state), {{ $workspace->state }}@endif</p>
            @if($workspace->email)<p>{{ $workspace->email }}</p>@endif
        </div>
        <div class="info-right info-block">
            <h4>{{ __("Bill To") }}</h4>
            <p class="strong">{{ $client->company_name ?: $client->name }}</p>
            @if($client->company_name)<p>{{ $client->name }}</p>@endif
            @if($client->address)<p>{{ $client->address }}</p>@endif
            <p>{{ $client->city }}@if($client->state), {{ $client->state }}@endif</p>
            @if($client->email)<p>{{ $client->email }}</p>@endif
        </div>
    </div>

    <div class="dates">
        <div class="date-block"><p class="d-label">{{ __("Issue Date") }}</p><p class="d-value">{{ $invoice->issue_date->format('M d, Y') }}</p></div>
        <div class="date-block"><p class="d-label">{{ __("Due Date") }}</p><p class="d-value">{{ $invoice->due_date->format('M d, Y') }}</p></div>
        <div class="date-block"><p class="d-label">{{ __("Currency") }}</p><p class="d-value">{{ $invoice->currency }}</p></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:50%;">{{ __("Description") }}</th>
                <th style="width:10%;text-align:center;">{{ __("Qty") }}</th>
                <th style="width:15%;text-align:right;">{{ __("Unit Price") }}</th>
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
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; color:#bbb; padding:32px;">{{ __("No items") }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <div class="row"><span>{{ __("Subtotal") }}</span><span>{{ number_format($invoice->subtotal, 2) }}</span></div>
        @if($invoice->tax_amount > 0)
            <div class="row"><span>Tax ({{ number_format($invoice->tax_rate, 2) }}%)</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>
        @endif
        @if($invoice->discount_amount > 0)
            <div class="row"><span>{{ __("Discount") }}</span><span>-{{ number_format($invoice->discount_amount, 2) }}</span></div>
        @endif
        <div class="row total"><span>{{ __("Total") }} {{ $invoice->currency }}</span><span>{{ number_format($invoice->total, 2) }}</span></div>
        @if($invoice->amount_paid > 0)
            <div class="row"><span>{{ __("Paid") }}</span><span>-{{ number_format($invoice->amount_paid, 2) }}</span></div>
            <div class="row total"><span>{{ __("Balance Due") }}</span><span>{{ number_format($invoice->balance_due, 2) }}</span></div>
        @endif
    </div>

    @if($invoice->notes || $invoice->terms)
    <div class="notes-section">
        @if($invoice->notes)
        <div class="notes-col"><h4>{{ __("Notes") }}</h4><p>{{ $invoice->notes }}</p></div>
        @endif
        @if($invoice->terms)
        <div class="notes-col"><h4>terms</h4><p>{{ $invoice->terms }}</p></div>
        @endif
    </div>
    @endif

    <div class="footer">{{ $workspace->name }} &mdash; Page <span class="pgn"></span></div>

    <x-free-tier-watermark :invoice="$invoice" />
</body>
</html>

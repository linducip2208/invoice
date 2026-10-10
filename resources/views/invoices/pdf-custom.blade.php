<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 24px; }
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #1f2937; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        h1, h2, h3, h4 { margin: 0; }
        hr { border: 0; border-top: 1px solid #e5e7eb; margin: 12px 0; }
    </style>
</head>
<body>
@php
    $primary = $layout->settings['primary'] ?? '#A78BFA';
    $blocks = $layout->blocks ?? [];
    $client = $invoice->client;
    $ws = $workspace;
@endphp

@foreach($blocks as $block)
    @php $type = $block['type'] ?? ''; $props = $block['props'] ?? []; @endphp

    @switch($type)
        @case('header')
            <table style="border-bottom:2px solid {{ $primary }}; margin-bottom:14px;"><tr>
                <td>
                    <h2 style="color:{{ $primary }}; font-size:18px;">{{ $ws->name }}</h2>
                    @if($ws->address)<div style="font-size:10px; color:#6b7280;">{{ $ws->address }}, {{ $ws->city ?? '' }}</div>@endif
                </td>
                <td class="text-right">
                    <h1 style="font-size:22px; color:{{ $primary }};">INVOICE</h1>
                    <div style="font-size:11px; color:#6b7280;">{{ $invoice->invoice_no }}</div>
                </td>
            </tr></table>
            @break

        @case('bill_to')
            <div style="margin-bottom:12px;">
                <div style="font-size:9px; text-transform:uppercase; color:#9ca3af; font-weight:bold; letter-spacing:1px;">{{ $props['title'] ?? 'Bill To' }}</div>
                <div style="font-weight:bold;">{{ $client?->name ?? '—' }}</div>
                @if($client?->company_name)<div>{{ $client->company_name }}</div>@endif
                @if($client?->email)<div style="font-size:11px; color:#6b7280;">{{ $client->email }}</div>@endif
                @if($client?->address)<div style="font-size:11px; color:#6b7280;">{{ $client->address }}</div>@endif
            </div>
            @break

        @case('dates')
            <table style="margin-bottom:12px;"><tr>
                <td style="width:50%;">
                    <div style="font-size:9px; color:#9ca3af; text-transform:uppercase; font-weight:bold;">Issue Date</div>
                    <div style="font-weight:bold;">{{ $invoice->issue_date?->format('d M Y') }}</div>
                </td>
                @if($props['show_due'] ?? true)
                <td style="width:50%;">
                    <div style="font-size:9px; color:#9ca3af; text-transform:uppercase; font-weight:bold;">Due Date</div>
                    <div style="font-weight:bold;">{{ $invoice->due_date?->format('d M Y') }}</div>
                </td>
                @endif
            </tr></table>
            @break

        @case('items')
            <table style="margin-bottom:12px;">
                <thead>
                    <tr style="border-bottom:2px solid {{ $primary }};">
                        <th style="text-align:left;">Description</th>
                        @if($props['show_qty'] ?? true)<th class="text-right" style="width:60px;">Qty</th>@endif
                        <th class="text-right" style="width:90px;">Unit Price</th>
                        @if($props['show_tax'] ?? true)<th class="text-right" style="width:60px;">Tax</th>@endif
                        <th class="text-right" style="width:100px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td>{{ $item->description }}</td>
                        @if($props['show_qty'] ?? true)<td class="text-right">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>@endif
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        @if($props['show_tax'] ?? true)<td class="text-right">{{ rtrim(rtrim(number_format($item->tax_rate, 2), '0'), '.') }}%</td>@endif
                        <td class="text-right" style="font-weight:bold;">{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @break

        @case('totals')
            <table style="margin-bottom:12px; width:100%;"><tr><td></td><td style="width:240px;">
                <table style="width:100%; font-size:11px;">
                    <tr><td style="color:#6b7280;">Subtotal</td><td class="text-right" style="font-weight:600;">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td></tr>
                    @if($invoice->tax_amount > 0)<tr><td style="color:#6b7280;">Tax ({{ $invoice->tax_rate }}%)</td><td class="text-right">{{ number_format($invoice->tax_amount, 2) }}</td></tr>@endif
                    @if($invoice->discount_amount > 0)<tr><td style="color:#dc2626;">Discount</td><td class="text-right" style="color:#dc2626;">-{{ number_format($invoice->discount_amount, 2) }}</td></tr>@endif
                    <tr style="border-top:2px solid {{ $primary }};">
                        <td style="font-weight:bold; padding-top:6px;">TOTAL</td>
                        <td class="text-right" style="font-weight:bold; padding-top:6px; color:{{ $primary }}; font-size:14px;">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                    </tr>
                </table>
            </td></tr></table>
            @break

        @case('notes')
            @if($invoice->notes)
            <div style="margin-bottom:10px;">
                <div style="font-size:9px; text-transform:uppercase; color:#9ca3af; font-weight:bold;">{{ $props['title'] ?? 'Notes' }}</div>
                <div style="color:#374151;">{{ $invoice->notes }}</div>
            </div>
            @endif
            @break

        @case('terms')
            @if($invoice->terms)
            <div style="margin-bottom:10px;">
                <div style="font-size:9px; text-transform:uppercase; color:#9ca3af; font-weight:bold;">{{ $props['title'] ?? 'Terms & Conditions' }}</div>
                <div style="color:#4b5563; font-size:10px;">{{ $invoice->terms }}</div>
            </div>
            @endif
            @break

        @case('footer')
            <div style="text-align:center; color:#9ca3af; font-size:11px; padding-top:16px; border-top:1px solid #e5e7eb;">{{ $props['text'] ?? '' }}</div>
            @break

        @case('divider') <hr> @break
        @case('spacer')  <div style="height:{{ ($props['height'] ?? 20) }}px;"></div> @break
        @case('custom_text')
            <div style="text-align:{{ $props['align'] ?? 'left' }}; margin:8px 0; color:#374151;">{{ $props['text'] ?? '' }}</div>
            @break
        @case('signature')
            <table style="margin-top:30px;"><tr><td></td><td class="text-center" style="width:200px;">
                <div style="border-top:1px solid #9ca3af; padding-top:4px; font-size:10px; color:#6b7280;">{{ $props['title'] ?? 'Authorized Signature' }}</div>
            </td></tr></table>
            @break
    @endswitch
@endforeach

<x-free-tier-watermark :invoice="$invoice" />
</body>
</html>

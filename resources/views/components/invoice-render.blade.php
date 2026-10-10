@props(['layout', 'invoice'])
@php
    $blocks  = $layout->blocks ?? [];
    $primary = $layout->settings['primary'] ?? '#A78BFA';
    $ws      = $invoice->workspace;
    $client  = $invoice->client;
@endphp

<div class="invoice-render bg-white p-10 rounded-xl" style="--primary: {{ $primary }}">
    @foreach($blocks as $block)
        @php $props = $block['props'] ?? []; $type = $block['type'] ?? ''; @endphp

        @switch($type)
            @case('header')
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2" style="border-color: {{ $primary }}">
                    <div class="flex items-center gap-3">
                        @if(($props['show_logo'] ?? true) && $ws?->logo_path)
                            <img src="{{ asset('storage/'.$ws->logo_path) }}" alt="logo" class="h-12">
                        @endif
                        <div>
                            <h1 class="text-2xl font-bold" style="color: {{ $primary }}">{{ $ws?->name ?? 'Company' }}</h1>
                            @if($ws?->address)<p class="text-xs text-gray-500">{{ $ws->address }}, {{ $ws->city ?? '' }}</p>@endif
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-3xl font-bold uppercase tracking-wider" style="color: {{ $primary }}">Invoice</h2>
                        <p class="text-sm font-semibold text-gray-700">{{ $invoice->invoice_no }}</p>
                    </div>
                </div>
                @break

            @case('bill_to')
                <div class="mb-5">
                    <p class="text-xs uppercase font-bold text-gray-500 tracking-wider mb-1">{{ $props['title'] ?? 'Bill To' }}</p>
                    <p class="font-bold text-gray-900">{{ $client?->name ?? '—' }}</p>
                    @if($client?->company_name)<p class="text-sm text-gray-700">{{ $client->company_name }}</p>@endif
                    @if($client?->email)<p class="text-xs text-gray-500">{{ $client->email }}</p>@endif
                    @if($client?->address)<p class="text-xs text-gray-500">{{ $client->address }}</p>@endif
                </div>
                @break

            @case('dates')
                <div class="mb-5 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs uppercase font-bold text-gray-500">Issue Date</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->issue_date?->format('d M Y') }}</p>
                    </div>
                    @if($props['show_due'] ?? true)
                    <div>
                        <p class="text-xs uppercase font-bold text-gray-500">Due Date</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->due_date?->format('d M Y') }}</p>
                    </div>
                    @endif
                </div>
                @break

            @case('items')
                <table class="w-full text-sm mb-5">
                    <thead>
                        <tr class="border-b-2" style="border-color: {{ $primary }}">
                            <th class="text-left py-2">Description</th>
                            @if($props['show_qty'] ?? true)<th class="text-right py-2">Qty</th>@endif
                            <th class="text-right py-2">Unit Price</th>
                            @if($props['show_tax'] ?? true)<th class="text-right py-2">Tax %</th>@endif
                            <th class="text-right py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-2">{{ $item->description }}</td>
                            @if($props['show_qty'] ?? true)<td class="text-right">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>@endif
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            @if($props['show_tax'] ?? true)<td class="text-right">{{ rtrim(rtrim(number_format($item->tax_rate, 2), '0'), '.') }}%</td>@endif
                            <td class="text-right font-semibold">{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @break

            @case('totals')
                <div class="flex justify-end mb-5">
                    <div class="w-full max-w-xs space-y-1 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-semibold">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span></div>
                        @if($invoice->tax_amount > 0)<div class="flex justify-between"><span class="text-gray-500">Tax ({{ $invoice->tax_rate }}%)</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>@endif
                        @if($invoice->discount_amount > 0)<div class="flex justify-between text-rose-600"><span>Discount</span><span>-{{ number_format($invoice->discount_amount, 2) }}</span></div>@endif
                        <div class="flex justify-between text-base pt-2 border-t-2" style="border-color: {{ $primary }}">
                            <strong>Total</strong>
                            <strong style="color: {{ $primary }}">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</strong>
                        </div>
                    </div>
                </div>
                @break

            @case('notes')
                @if($invoice->notes)
                <div class="mb-4">
                    <p class="text-xs uppercase font-bold text-gray-500 mb-1">{{ $props['title'] ?? 'Notes' }}</p>
                    <p class="text-sm text-gray-700">{{ $invoice->notes }}</p>
                </div>
                @endif
                @break

            @case('terms')
                @if($invoice->terms)
                <div class="mb-4">
                    <p class="text-xs uppercase font-bold text-gray-500 mb-1">{{ $props['title'] ?? 'Terms & Conditions' }}</p>
                    <p class="text-xs text-gray-600">{{ $invoice->terms }}</p>
                </div>
                @endif
                @break

            @case('footer')
                <div class="mt-8 pt-4 border-t border-gray-200 text-center text-sm text-gray-500">
                    {{ $props['text'] ?? 'Thank you for your business!' }}
                </div>
                @break

            @case('divider')
                <hr class="my-4 border-gray-200">
                @break

            @case('spacer')
                <div style="height: {{ ($props['height'] ?? 20) }}px"></div>
                @break

            @case('custom_text')
                <div style="text-align: {{ $props['align'] ?? 'left' }}" class="my-3 text-sm text-gray-700">{{ $props['text'] ?? '' }}</div>
                @break

            @case('signature')
                <div class="mt-10 flex justify-end">
                    <div class="text-center">
                        <div class="border-t-2 border-gray-300 w-48 pt-2"></div>
                        <p class="text-xs text-gray-500">{{ $props['title'] ?? 'Authorized Signature' }}</p>
                    </div>
                </div>
                @break
        @endswitch
    @endforeach
</div>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="heading-unicorn">📋 {{ $quote->quote_no }}</h2>
            <a href="{{ route('quotes.index') }}" class="text-purple-500 hover:text-pink-500">← Back</a>
        </div>
    </x-slot>
    <div class="max-w-4xl mx-auto card-unicorn">
        <div class="flex justify-between mb-4">
            <div><strong>Client:</strong> {{ $quote->client?->name }}</div>
            <span class="badge-{{ $quote->status_color }}">{{ ucfirst($quote->status) }}</span>
        </div>
        <p>Issue: {{ $quote->issue_date?->format('d M Y') }} · Expiry: {{ $quote->expiry_date?->format('d M Y') ?? '—' }}</p>

        <table class="table-unicorn mt-4">
            <thead><tr><th>Description</th><th>Qty</th><th>Price</th><th>Tax%</th><th class="text-right">Total</th></tr></thead>
            <tbody>
                @foreach($quote->items ?? [] as $i)
                <tr><td>{{ $i['description'] }}</td><td>{{ $i['quantity'] }}</td><td>{{ number_format($i['unit_price'], 2) }}</td><td>{{ $i['tax_rate'] ?? 0 }}%</td><td class="text-right">{{ number_format(($i['quantity'] * $i['unit_price']), 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-right">
            <p>Subtotal: {{ number_format($quote->subtotal, 2) }}</p>
            <p>Tax: {{ number_format($quote->tax_amount, 2) }}</p>
            <p class="font-bold text-xl">Total: {{ $quote->currency }} {{ number_format($quote->total, 2) }}</p>
        </div>

        @if($quote->notes)<div class="mt-4"><strong>Notes:</strong><p>{{ $quote->notes }}</p></div>@endif
        @if($quote->terms)<div class="mt-4"><strong>Terms:</strong><p>{{ $quote->terms }}</p></div>@endif

        <div class="mt-6 flex gap-3">
            @if($quote->status !== 'converted')
            <form method="POST" action="{{ route('quotes.convert', $quote) }}">@csrf<button class="btn-unicorn">→ Convert to Invoice</button></form>
            @else
            <a href="{{ route('invoices.show', $quote->converted_invoice_id) }}" class="btn-unicorn-secondary">View Invoice →</a>
            @endif
        </div>
    </div>
</x-app-layout>

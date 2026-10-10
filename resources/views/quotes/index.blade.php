<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="heading-unicorn flex items-center gap-2">📋 {{ __('Quotes / Estimates') }}</h2>
            <a href="{{ route('quotes.create') }}" class="btn-unicorn">+ {{ __('Quote Baru') }}</a>
        </div>
    </x-slot>
    <div class="max-w-6xl mx-auto card-unicorn">
        <table class="table-unicorn">
            <thead><tr><th>No</th><th>Klien</th><th>Issue</th><th>Expiry</th><th class="text-right">Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($quotes as $q)
                <tr>
                    <td class="font-bold"><a href="{{ route('quotes.show', $q) }}" class="text-purple-700 hover:text-pink-500">{{ $q->quote_no }}</a></td>
                    <td>{{ $q->client?->name }}</td>
                    <td>{{ $q->issue_date?->format('d M Y') }}</td>
                    <td>{{ $q->expiry_date?->format('d M Y') ?? '—' }}</td>
                    <td class="text-right font-bold">{{ $q->currency }} {{ number_format($q->total, 2) }}</td>
                    <td><span class="badge-{{ $q->status_color }}">{{ ucfirst($q->status) }}</span></td>
                    <td>
                        @if($q->status !== 'converted')
                        <form method="POST" action="{{ route('quotes.convert', $q) }}" class="inline">@csrf<button class="text-emerald-600 text-sm font-semibold">→ Invoice</button></form>
                        @endif
                        <form method="POST" action="{{ route('quotes.destroy', $q) }}" class="inline" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="text-rose-500 text-sm ml-2">×</button></form>
                    </td>
                </tr>
                @empty<tr><td colspan="7" class="text-center text-purple-400 py-8">Belum ada quote. <a href="{{ route('quotes.create') }}" class="text-pink-600 font-semibold">Bikin yang pertama →</a></td></tr>@endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $quotes->links() }}</div>
    </div>
</x-app-layout>

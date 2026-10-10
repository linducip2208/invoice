<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ $cn->credit_no }} <span class="text-2xl">↩️</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Credit Note untuk') }} <a href="{{ route('invoices.show', $cn->invoice) }}" class="text-pink-500 font-semibold hover:underline">{{ $cn->invoice?->invoice_no }}</a></p>
            </div>
            <a href="{{ route('credit-notes.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Semua Credit Note') }}</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-5">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        {{-- Status & Actions --}}
        <div class="card-unicorn-glow !p-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs uppercase tracking-wider text-purple-400 mb-1">{{ __('Status') }}</p>
                <span class="badge-{{ $cn->status_color }} text-base px-4 py-1">{{ strtoupper($cn->status) }}</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($cn->status === 'draft')
                    <form method="POST" action="{{ route('credit-notes.issue', $cn) }}">@csrf
                        <button type="submit" class="btn-unicorn text-sm">📤 {{ __('Issue') }}</button>
                    </form>
                @endif
                @if($cn->status === 'issued')
                    <form method="POST" action="{{ route('credit-notes.apply', $cn) }}" onsubmit="return confirm('Apply credit ini ke invoice? Sisa tagihan akan berkurang.')">@csrf
                        <button type="submit" class="btn-unicorn text-sm">✓ {{ __('Apply ke Invoice') }}</button>
                    </form>
                @endif
                @if(in_array($cn->status, ['issued','applied']))
                    <form method="POST" action="{{ route('credit-notes.cancel', $cn) }}" onsubmit="return confirm('Batalkan credit note ini?')">@csrf
                        <button type="submit" class="btn-unicorn-secondary text-sm">✗ {{ __('Cancel') }}</button>
                    </form>
                @endif
                @if($cn->status === 'draft')
                    <form method="POST" action="{{ route('credit-notes.destroy', $cn) }}" onsubmit="return confirm('Hapus draft permanen?')">@csrf @method('DELETE')
                        <button type="submit" class="text-sm text-rose-500 font-semibold hover:underline">🗑️ {{ __('Hapus Draft') }}</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Detail card --}}
        <div class="card-unicorn !p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Klien') }}</p>
                    <p class="font-display font-bold text-lg text-purple-900">{{ $cn->invoice?->client?->name }}</p>
                    @if($cn->invoice?->client?->company_name)
                        <p class="text-sm text-purple-500">{{ $cn->invoice->client->company_name }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Tanggal Issued') }}</p>
                    <p class="font-display font-bold text-lg text-purple-900">{{ $cn->issued_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Jumlah Kredit') }}</p>
                    <p class="font-display font-bold text-2xl text-rose-600">-{{ number_format($cn->amount, 0) }} {{ $cn->currency }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Dibuat oleh') }}</p>
                    <p class="font-semibold text-purple-700">{{ $cn->creator?->name ?? '—' }}</p>
                    <p class="text-xs text-purple-400 mt-0.5">{{ $cn->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-purple-100">
                <p class="text-xs uppercase tracking-wider text-purple-400 mb-1">{{ __('Alasan') }}</p>
                <p class="text-purple-900">{{ $cn->reason }}</p>
            </div>

            @if($cn->notes)
                <div class="mt-5 pt-5 border-t border-purple-100">
                    <p class="text-xs uppercase tracking-wider text-purple-400 mb-1">{{ __('Catatan Internal') }}</p>
                    <p class="text-purple-700 whitespace-pre-line text-sm">{{ $cn->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Invoice context --}}
        <div class="card-unicorn !p-5">
            <p class="text-xs uppercase tracking-wider text-purple-400 mb-3">{{ __('Konteks Invoice') }}</p>
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <a href="{{ route('invoices.show', $cn->invoice) }}" class="font-display font-bold text-lg text-pink-600 hover:underline">{{ $cn->invoice?->invoice_no }}</a>
                    <p class="text-sm text-purple-500">Total: {{ $cn->invoice?->currency }} {{ number_format($cn->invoice?->total, 0) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-purple-400">{{ __('Sisa Tagihan saat ini') }}</p>
                    <p class="font-display font-bold text-xl text-purple-900">{{ $cn->invoice?->currency }} {{ number_format($cn->invoice?->balance_due, 0) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

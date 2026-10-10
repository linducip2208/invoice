<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Credit Notes') }} <span class="text-2xl">↩️</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Refund, return, koreksi tagihan') }}</p>
            </div>
            <a href="{{ route('credit-notes.create') }}" class="btn-unicorn">+ {{ __('Credit Note Baru') }}</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="card-unicorn !p-5">
                <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Issued (belum apply)') }}</p>
                <p class="font-display text-2xl font-bold text-purple-900 mt-1">{{ number_format($stats['total_issued'], 0) }}</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-emerald-50 to-teal-50">
                <p class="text-xs uppercase tracking-wider text-emerald-700">{{ __('Sudah Diterapkan') }}</p>
                <p class="font-display text-2xl font-bold text-emerald-700 mt-1">{{ number_format($stats['total_applied'], 0) }}</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-amber-50 to-yellow-50">
                <p class="text-xs uppercase tracking-wider text-amber-700">{{ __('Draft Pending') }}</p>
                <p class="font-display text-2xl font-bold text-amber-700 mt-1">{{ $stats['count_pending'] }}</p>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" class="card-unicorn !p-4 flex flex-wrap items-end gap-3">
            <div>
                <x-input-label :value="__('Status')" />
                <select name="status" class="input-unicorn rounded-xl px-4 py-2 mt-1">
                    <option value="">{{ __('Semua') }}</option>
                    <option value="draft"     {{ request('status')==='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="issued"    {{ request('status')==='issued' ? 'selected' : '' }}>Issued</option>
                    <option value="applied"   {{ request('status')==='applied' ? 'selected' : '' }}>Applied</option>
                    <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn-unicorn">{{ __('Filter') }}</button>
            @if(request()->hasAny(['status','invoice_id']))
                <a href="{{ route('credit-notes.index') }}" class="btn-unicorn-secondary text-sm">{{ __('Reset') }}</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="card-unicorn !p-0 overflow-hidden">
            @if($creditNotes->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-4xl mb-3">↩️</p>
                    <p class="font-display font-bold text-purple-700">{{ __('Belum ada credit note') }}</p>
                    <p class="text-sm text-purple-400 mt-1">Buat credit note pertama dari halaman invoice.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">Invoice</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">Klien</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">Alasan</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">Tgl</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-purple-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditNotes as $cn)
                                <tr class="border-t border-purple-100 hover:bg-purple-50/40">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('credit-notes.show', $cn) }}" class="text-pink-600 font-bold hover:underline">{{ $cn->credit_no }}</a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('invoices.show', $cn->invoice) }}" class="text-purple-700 font-semibold hover:text-pink-500">{{ $cn->invoice?->invoice_no }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-purple-700 text-sm">{{ $cn->invoice?->client?->name }}</td>
                                    <td class="px-4 py-3 text-sm text-purple-600 max-w-xs truncate">{{ $cn->reason }}</td>
                                    <td class="px-4 py-3 text-sm text-purple-500">{{ $cn->issued_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-rose-600">-{{ number_format($cn->amount, 0) }} {{ $cn->currency }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge-{{ $cn->status_color }} text-xs">{{ ucfirst($cn->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('credit-notes.show', $cn) }}" class="text-xs text-pink-600 font-semibold hover:underline">{{ __('Detail') }} →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $creditNotes->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

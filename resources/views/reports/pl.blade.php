<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Profit & Loss per Klien') }} <span class="text-2xl">💰</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Revenue − Expense − Time Cost = Profit') }}</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn-unicorn-secondary text-sm">← {{ __('Reports') }}</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <form method="GET" class="card-unicorn !p-4 flex flex-wrap items-end gap-3">
            <div>
                <x-input-label :value="__('Periode')" />
                <select name="period" class="input-unicorn rounded-xl px-4 py-2 mt-1">
                    <option value="30"  {{ $period==='30'  ? 'selected' : '' }}>30 hari</option>
                    <option value="90"  {{ $period==='90'  ? 'selected' : '' }}>90 hari</option>
                    <option value="180" {{ $period==='180' ? 'selected' : '' }}>180 hari</option>
                    <option value="365" {{ $period==='365' ? 'selected' : '' }}>1 tahun</option>
                    <option value="ytd" {{ $period==='ytd' ? 'selected' : '' }}>Tahun ini (YTD)</option>
                </select>
            </div>
            <button type="submit" class="btn-unicorn">{{ __('Update') }}</button>
        </form>

        {{-- Total cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card-unicorn !p-5 bg-gradient-to-br from-emerald-50 to-teal-50">
                <p class="text-xs uppercase tracking-wider text-emerald-700">Revenue</p>
                <p class="font-display text-2xl font-bold text-emerald-700 mt-1">{{ number_format($totals->revenue, 0) }}</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-rose-50 to-pink-50">
                <p class="text-xs uppercase tracking-wider text-rose-700">Expense</p>
                <p class="font-display text-2xl font-bold text-rose-700 mt-1">{{ number_format($totals->expense, 0) }}</p>
            </div>
            <div class="card-unicorn !p-5 bg-gradient-to-br from-amber-50 to-yellow-50">
                <p class="text-xs uppercase tracking-wider text-amber-700">Time Cost</p>
                <p class="font-display text-2xl font-bold text-amber-700 mt-1">{{ number_format($totals->time_cost, 0) }}</p>
            </div>
            <div class="card-unicorn-glow !p-5">
                <p class="text-xs uppercase tracking-wider text-purple-400">{{ __('Net Profit') }}</p>
                <p class="font-display text-2xl font-bold {{ $totals->profit >= 0 ? 'text-rainbow' : 'text-rose-700' }} mt-1">
                    {{ $totals->profit >= 0 ? '+' : '' }}{{ number_format($totals->profit, 0) }}
                </p>
                <p class="text-xs text-purple-500 mt-1">Margin: {{ $totals->margin_pct ?? '—' }}%</p>
            </div>
        </div>

        {{-- Per-client table --}}
        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900">{{ __('Per Klien') }} ({{ $rows->count() }})</h3>
            </div>
            @if($rows->isEmpty())
                <p class="p-8 text-center text-purple-400">Tidak ada data untuk periode ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Klien</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold text-emerald-700 uppercase">Revenue</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold text-rose-700 uppercase">Expense</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold text-amber-700 uppercase">Time Cost</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase">Profit</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                @php
                                    $marginColor = match (true) {
                                        $row->margin_pct === null => 'text-gray-400',
                                        $row->margin_pct >= 30 => 'text-emerald-700',
                                        $row->margin_pct >= 10 => 'text-amber-700',
                                        default => 'text-rose-700',
                                    };
                                @endphp
                                <tr class="border-t border-purple-100 hover:bg-purple-50/40">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('clients.show', $row->client) }}" class="text-purple-900 font-semibold hover:text-pink-500">{{ $row->client->name }}</a>
                                        <p class="text-xs text-purple-400">{{ $row->invoice_count }} invoice</p>
                                    </td>
                                    <td class="px-4 py-3 text-right text-emerald-700 font-semibold">{{ number_format($row->revenue, 0) }}</td>
                                    <td class="px-4 py-3 text-right text-rose-700">{{ number_format($row->expense, 0) }}</td>
                                    <td class="px-4 py-3 text-right text-amber-700">{{ number_format($row->time_cost, 0) }}</td>
                                    <td class="px-4 py-3 text-right font-display font-bold {{ $row->profit >= 0 ? 'text-purple-900' : 'text-rose-700' }}">
                                        {{ $row->profit >= 0 ? '+' : '' }}{{ number_format($row->profit, 0) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold {{ $marginColor }}">
                                        {{ $row->margin_pct ?? '—' }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

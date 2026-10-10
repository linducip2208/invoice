<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Aging Report') }} <span class="text-2xl">⏳</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Berapa hari klien telat bayar — dipecah per bucket') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.index') }}" class="btn-unicorn-secondary text-sm">← {{ __('Reports') }}</a>
                <a href="{{ route('reports.aging', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-unicorn text-sm">📥 CSV</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Filter --}}
        <form method="GET" class="card-unicorn !p-4 flex flex-wrap items-end gap-4">
            <div>
                <x-input-label :value="__('Per tanggal')" />
                <x-text-input name="as_of" type="date" :value="request('as_of', $asOf->format('Y-m-d'))" class="mt-1" />
            </div>
            <div>
                <x-input-label :value="__('Mata uang')" />
                <select name="currency" class="input-unicorn rounded-xl px-4 py-2 mt-1">
                    <option value="">{{ __('Semua') }}</option>
                    @foreach($currencies as $c)
                        <option value="{{ $c }}" {{ $currency === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-unicorn">{{ __('Filter') }}</button>
        </form>

        {{-- Bucket summary cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @php
                $bucketColors = [
                    'current'  => ['bg' => 'from-emerald-100 to-teal-100', 'text' => 'text-emerald-700', 'icon' => '✓'],
                    '1_30'     => ['bg' => 'from-yellow-100 to-amber-100', 'text' => 'text-amber-700',   'icon' => '⏰'],
                    '31_60'    => ['bg' => 'from-orange-100 to-rose-100', 'text' => 'text-rose-700',    'icon' => '⚠️'],
                    '61_90'    => ['bg' => 'from-rose-200 to-pink-200',   'text' => 'text-rose-800',    'icon' => '🔥'],
                    'over_90'  => ['bg' => 'from-red-300 to-purple-200',  'text' => 'text-red-900',     'icon' => '🚨'],
                ];
            @endphp
            @foreach($buckets as $key => $b)
                @php $color = $bucketColors[$key]; @endphp
                <div class="card-unicorn !p-4 bg-gradient-to-br {{ $color['bg'] }}">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">{{ $color['icon'] }}</span>
                        <p class="text-xs font-bold uppercase tracking-wider {{ $color['text'] }}">{{ $b['label'] }}</p>
                    </div>
                    <p class="font-display text-2xl font-bold {{ $color['text'] }}">
                        {{ number_format($b['total'], 0) }}
                    </p>
                    <p class="text-xs {{ $color['text'] }} opacity-80 mt-1">{{ $b['invoices']->count() }} invoice</p>
                </div>
            @endforeach
        </div>

        {{-- Grand total --}}
        <div class="card-unicorn-glow !p-6 text-center">
            <p class="text-sm text-purple-500 uppercase tracking-wider">{{ __('Total Belum Lunas') }}</p>
            <p class="font-display text-4xl font-bold text-rainbow mt-1">{{ number_format($grandTotal, 0) }}</p>
            <p class="text-xs text-purple-400 mt-1">Per {{ $asOf->format('d M Y') }}</p>
        </div>

        {{-- Per-client breakdown --}}
        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Per Klien') }}
                </h3>
                <p class="text-sm text-purple-500 mt-1">{{ count($byClient) }} klien dengan invoice belum lunas</p>
            </div>

            @if($byClient->isEmpty())
                <div class="p-12 text-center text-purple-400">
                    <p class="text-4xl mb-3">🎉</p>
                    <p class="font-display text-lg font-bold text-purple-700">{{ __('Tidak ada invoice belum lunas') }}</p>
                    <p class="text-sm mt-1">Semua klien sudah bayar tepat waktu — kerja bagus!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">{{ __('Klien') }}</th>
                                <th class="px-3 py-3 text-right text-[11px] font-bold text-emerald-700 uppercase tracking-wider">{{ __('Belum Jatuh Tempo') }}</th>
                                <th class="px-3 py-3 text-right text-[11px] font-bold text-amber-700 uppercase tracking-wider">1-30</th>
                                <th class="px-3 py-3 text-right text-[11px] font-bold text-rose-700 uppercase tracking-wider">31-60</th>
                                <th class="px-3 py-3 text-right text-[11px] font-bold text-rose-800 uppercase tracking-wider">61-90</th>
                                <th class="px-3 py-3 text-right text-[11px] font-bold text-red-900 uppercase tracking-wider">90+</th>
                                <th class="px-4 py-3 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider">{{ __('Total') }}</th>
                                <th class="px-4 py-3 text-center text-[11px] font-bold text-purple-700 uppercase tracking-wider w-32">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byClient as $row)
                                <tr class="border-t border-purple-100 hover:bg-purple-50/50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('clients.show', $row->client) }}" class="font-semibold text-purple-900 hover:text-pink-500">{{ $row->client->name }}</a>
                                        @if($row->client->company_name)
                                            <p class="text-xs text-purple-500">{{ $row->client->company_name }}</p>
                                        @endif
                                        <p class="text-xs text-purple-400 mt-0.5">{{ $row->invoice_count }} invoice</p>
                                    </td>
                                    <td class="px-3 py-3 text-right text-sm {{ $row->buckets['current'] > 0 ? 'text-emerald-700 font-semibold' : 'text-gray-300' }}">
                                        {{ $row->buckets['current'] > 0 ? number_format($row->buckets['current'], 0) : '—' }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-sm {{ $row->buckets['1_30'] > 0 ? 'text-amber-700 font-semibold' : 'text-gray-300' }}">
                                        {{ $row->buckets['1_30'] > 0 ? number_format($row->buckets['1_30'], 0) : '—' }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-sm {{ $row->buckets['31_60'] > 0 ? 'text-rose-700 font-semibold' : 'text-gray-300' }}">
                                        {{ $row->buckets['31_60'] > 0 ? number_format($row->buckets['31_60'], 0) : '—' }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-sm {{ $row->buckets['61_90'] > 0 ? 'text-rose-800 font-bold' : 'text-gray-300' }}">
                                        {{ $row->buckets['61_90'] > 0 ? number_format($row->buckets['61_90'], 0) : '—' }}
                                    </td>
                                    <td class="px-3 py-3 text-right text-sm {{ $row->buckets['over_90'] > 0 ? 'text-red-900 font-bold' : 'text-gray-300' }}">
                                        @if($row->buckets['over_90'] > 0)
                                            <span class="bg-red-100 px-2 py-0.5 rounded-full">{{ number_format($row->buckets['over_90'], 0) }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-display font-bold text-purple-900">{{ number_format($row->total, 0) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('invoices.index', ['client_id' => $row->client->id, 'status' => 'unpaid']) }}"
                                           class="text-xs text-pink-600 font-semibold hover:underline">
                                            {{ __('Lihat invoice') }} →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="border-t-2 border-purple-300 bg-purple-50/70 font-bold">
                                <td class="px-4 py-3 font-display text-purple-900">{{ __('TOTAL') }}</td>
                                <td class="px-3 py-3 text-right text-emerald-700">{{ number_format($buckets['current']['total'], 0) }}</td>
                                <td class="px-3 py-3 text-right text-amber-700">{{ number_format($buckets['1_30']['total'], 0) }}</td>
                                <td class="px-3 py-3 text-right text-rose-700">{{ number_format($buckets['31_60']['total'], 0) }}</td>
                                <td class="px-3 py-3 text-right text-rose-800">{{ number_format($buckets['61_90']['total'], 0) }}</td>
                                <td class="px-3 py-3 text-right text-red-900">{{ number_format($buckets['over_90']['total'], 0) }}</td>
                                <td class="px-4 py-3 text-right font-display text-rainbow">{{ number_format($grandTotal, 0) }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Detail per bucket --}}
        @foreach($buckets as $key => $b)
            @if($b['invoices']->isNotEmpty())
                <div class="card-unicorn !p-6">
                    <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                        <span class="text-2xl">{{ $bucketColors[$key]['icon'] }}</span>
                        {{ $b['label'] }}
                        <span class="text-sm font-normal text-purple-500">— {{ $b['invoices']->count() }} invoice, total {{ number_format($b['total'], 0) }}</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-purple-50/60">
                                <tr>
                                    <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">No</th>
                                    <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Klien</th>
                                    <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Tgl Terbit</th>
                                    <th class="px-3 py-2 text-left text-[11px] font-bold text-purple-700 uppercase">Jatuh Tempo</th>
                                    <th class="px-3 py-2 text-right text-[11px] font-bold text-purple-700 uppercase">Hari</th>
                                    <th class="px-3 py-2 text-right text-[11px] font-bold text-purple-700 uppercase">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($b['invoices'] as $inv)
                                    <tr class="border-t border-purple-100 hover:bg-purple-50/40">
                                        <td class="px-3 py-2">
                                            <a href="{{ route('invoices.show', $inv) }}" class="text-pink-600 font-semibold hover:underline">{{ $inv->invoice_no }}</a>
                                        </td>
                                        <td class="px-3 py-2 text-purple-700">{{ $inv->client?->name }}</td>
                                        <td class="px-3 py-2 text-purple-500 text-xs">{{ $inv->issue_date->format('d M Y') }}</td>
                                        <td class="px-3 py-2 text-purple-500 text-xs">{{ $inv->due_date->format('d M Y') }}</td>
                                        <td class="px-3 py-2 text-right">
                                            @if($inv->days_overdue > 0)
                                                <span class="badge-red text-xs">{{ $inv->days_overdue }}</span>
                                            @else
                                                <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-right font-bold text-purple-900">{{ number_format($inv->balance_due, 0) }} {{ $inv->currency }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

    </div>
</x-app-layout>

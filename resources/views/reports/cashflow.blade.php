<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Cashflow Forecast') }} <span class="text-2xl">📈</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Proyeksi pendapatan') }} {{ $days }} {{ __('hari ke depan') }}</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn-unicorn-secondary text-sm">← {{ __('Reports') }}</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <form method="GET" class="card-unicorn !p-4 flex flex-wrap items-end gap-3">
            <div>
                <x-input-label :value="__('Periode')" />
                <select name="days" class="input-unicorn rounded-xl px-4 py-2 mt-1">
                    @foreach([30, 60, 90, 180] as $d)
                        <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} hari</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label :value="__('Skenario')" />
                <select name="scenario" class="input-unicorn rounded-xl px-4 py-2 mt-1">
                    <option value="optimistic" {{ $scenario === 'optimistic' ? 'selected' : '' }}>😎 Optimis (semua bayar tepat waktu)</option>
                    <option value="realistic"  {{ $scenario === 'realistic' ? 'selected' : '' }}>📊 Realistis (rata-rata historis)</option>
                    <option value="pessimistic" {{ $scenario === 'pessimistic' ? 'selected' : '' }}>😰 Pesimis (tambah 14 hari)</option>
                </select>
            </div>
            <button type="submit" class="btn-unicorn">{{ __('Update') }}</button>
        </form>

        <div class="card-unicorn-glow !p-6 text-center">
            <p class="text-sm text-purple-500 uppercase tracking-wider">{{ __('Total Proyeksi') }} {{ $days }} {{ __('hari') }}</p>
            <p class="font-display text-4xl font-bold text-rainbow mt-1">{{ number_format($totalProjected, 0) }}</p>
            <p class="text-xs text-purple-400 mt-1">Skenario: {{ ucfirst($scenario) }}</p>
        </div>

        {{-- Weekly bars --}}
        <div class="card-unicorn !p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-5 flex items-center gap-2">
                <span class="text-pink-400">✦</span> {{ __('Per Minggu') }}
            </h3>
            @php $maxInflow = $weeks->max('inflow') ?: 1; @endphp
            <div class="space-y-3">
                @foreach($weeks as $w)
                    <div>
                        <div class="flex items-center justify-between text-xs text-purple-600 mb-1">
                            <span class="font-semibold">{{ $w->label }}</span>
                            <span class="font-bold text-purple-900">{{ number_format($w->inflow, 0) }}</span>
                        </div>
                        <div class="h-6 bg-purple-50 rounded-lg overflow-hidden relative">
                            <div class="h-full rounded-lg flex items-center px-2 text-xs font-bold text-white"
                                 style="width: {{ $w->inflow > 0 ? max(2, $w->inflow / $maxInflow * 100) : 0 }}%; background: linear-gradient(90deg, #FF7AC6, #A78BFA);">
                                @if($w->invoices->count())
                                    {{ $w->invoices->count() }} invoice
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Detail table --}}
        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                    <span class="text-sky-400">✦</span> {{ __('Detail per Minggu') }}
                </h3>
            </div>
            <div class="divide-y divide-purple-100">
                @foreach($weeks as $w)
                    @if($w->invoices->isNotEmpty())
                        <details class="p-4 hover:bg-purple-50/30">
                            <summary class="cursor-pointer flex items-center justify-between flex-wrap gap-2">
                                <div>
                                    <p class="font-semibold text-purple-900">{{ $w->label }}</p>
                                    <p class="text-xs text-purple-500">{{ $w->invoices->count() }} invoice expected</p>
                                </div>
                                <span class="font-display font-bold text-purple-900">{{ number_format($w->inflow, 0) }}</span>
                            </summary>
                            <div class="mt-3 space-y-2">
                                @foreach($w->invoices as $inv)
                                    <div class="flex items-center justify-between text-sm py-2 px-3 bg-white rounded-lg">
                                        <a href="{{ route('invoices.show', $inv) }}" class="text-pink-600 font-semibold">{{ $inv->invoice_no }}</a>
                                        <span class="text-purple-600">{{ $inv->client?->name }}</span>
                                        <span class="font-bold text-purple-900">{{ number_format($inv->balance_due, 0) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

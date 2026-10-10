<x-tabler.layout :title="__('Cashflow Forecast')" :pretitle="__('Reports')">
    <x-slot name="actions">
        <a href="{{ route('reports.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Reports') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Periode')" name="days">
                            <select id="days" name="days" class="form-select">
                                @foreach([30, 60, 90, 180] as $d)
                                    <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} {{ __('hari') }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-5">
                        <x-tabler.field :label="__('Skenario')" name="scenario">
                            <select id="scenario" name="scenario" class="form-select">
                                <option value="optimistic" {{ $scenario === 'optimistic' ? 'selected' : '' }}>{{ __('Optimis (semua bayar tepat waktu)') }}</option>
                                <option value="realistic" {{ $scenario === 'realistic' ? 'selected' : '' }}>{{ __('Realistis (rata-rata historis)') }}</option>
                                <option value="pessimistic" {{ $scenario === 'pessimistic' ? 'selected' : '' }}>{{ __('Pesimis (tambah 14 hari)') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-list justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-decks row-cards mb-3">
        <x-tabler.stat :label="__('Total Proyeksi') . ' ' . $days . ' ' . __('hari')" :value="number_format($totalProjected, 0)" icon="chart-line" />
    </div>
    <p class="text-muted small mb-3">{{ __('Skenario') }}: {{ ucfirst($scenario) }} · {{ __('Proyeksi pendapatan') }} {{ $days }} {{ __('hari ke depan') }}</p>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="chart-line" /> {{ __('Per Minggu') }}</h3>
        </div>
        <div class="card-body">
            @php $maxInflow = $weeks->max('inflow') ?: 1; @endphp
            @foreach($weeks as $w)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-semibold">{{ $w->label }}</span>
                        <span class="fw-bold">{{ number_format($w->inflow, 0) }}</span>
                    </div>
                    <div class="progress" style="height: 1.5rem;">
                        <div class="progress-bar bg-primary" style="width: {{ $w->inflow > 0 ? max(2, $w->inflow / $maxInflow * 100) : 0 }}%;">
                            @if($w->invoices->count())
                                <span class="small">{{ $w->invoices->count() }} {{ __('invoice') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="clipboard-list" /> {{ __('Detail per Minggu') }}</h3>
        </div>
        <div class="list-group list-group-flush">
            @foreach($weeks as $w)
                @if($w->invoices->isNotEmpty())
                    <details class="list-group-item">
                        <summary class="d-flex justify-content-between align-items-center flex-wrap gap-2" style="cursor: pointer;">
                            <div>
                                <div class="fw-semibold">{{ $w->label }}</div>
                                <div class="text-muted small">{{ $w->invoices->count() }} {{ __('invoice expected') }}</div>
                            </div>
                            <span class="fw-bold">{{ number_format($w->inflow, 0) }}</span>
                        </summary>
                        <div class="mt-3 d-grid gap-2">
                            @foreach($w->invoices as $inv)
                                <div class="d-flex justify-content-between align-items-center small p-2 border rounded">
                                    <a href="{{ route('invoices.show', $inv) }}" class="fw-semibold">{{ $inv->invoice_no }}</a>
                                    <span class="text-muted">{{ $inv->client?->name }}</span>
                                    <span class="fw-bold">{{ number_format($inv->balance_due, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endif
            @endforeach
        </div>
    </div>
</x-tabler.layout>

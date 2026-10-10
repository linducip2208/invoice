<x-tabler.layout :title="__('Profit & Loss per Klien')" :pretitle="__('Reports')">
    <x-slot name="actions">
        <a href="{{ route('reports.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Reports') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Periode')" name="period">
                            <select id="period" name="period" class="form-select">
                                <option value="30" {{ $period === '30' ? 'selected' : '' }}>30 {{ __('hari') }}</option>
                                <option value="90" {{ $period === '90' ? 'selected' : '' }}>90 {{ __('hari') }}</option>
                                <option value="180" {{ $period === '180' ? 'selected' : '' }}>180 {{ __('hari') }}</option>
                                <option value="365" {{ $period === '365' ? 'selected' : '' }}>1 {{ __('tahun') }}</option>
                                <option value="ytd" {{ $period === 'ytd' ? 'selected' : '' }}>{{ __('Tahun ini (YTD)') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-list justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-decks row-cards mb-3">
        <x-tabler.stat :label="__('Revenue')" :value="number_format($totals->revenue, 0)" icon="chart-line" />
        <x-tabler.stat :label="__('Expense')" :value="number_format($totals->expense, 0)" icon="receipt" />
        <x-tabler.stat :label="__('Time Cost')" :value="number_format($totals->time_cost, 0)" icon="clock" />
        <x-tabler.stat :label="__('Net Profit')" :value="($totals->profit >= 0 ? '+' : '') . number_format($totals->profit, 0)" icon="coins" />
    </div>
    <p class="text-muted small mb-3">{{ __('Revenue − Expense − Time Cost = Profit') }} · {{ __('Margin') }}: {{ $totals->margin_pct ?? '—' }}%</p>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Per Klien') }} ({{ $rows->count() }})</h3>
        </div>
        @if($rows->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="inbox" :title="__('Tidak ada data untuk periode ini.')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Klien') }}</th>
                            <th class="text-end">{{ __('Revenue') }}</th>
                            <th class="text-end">{{ __('Expense') }}</th>
                            <th class="text-end">{{ __('Time Cost') }}</th>
                            <th class="text-end">{{ __('Profit') }}</th>
                            <th class="text-end">{{ __('Margin') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            @php
                                $marginColor = match (true) {
                                    $row->margin_pct === null => 'text-muted',
                                    $row->margin_pct >= 30 => 'text-success',
                                    $row->margin_pct >= 10 => 'text-warning',
                                    default => 'text-danger',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('clients.show', $row->client) }}" class="fw-semibold">{{ $row->client->name }}</a>
                                    <div class="text-muted small">{{ $row->invoice_count }} {{ __('invoice') }}</div>
                                </td>
                                <td class="text-end text-success fw-semibold">{{ number_format($row->revenue, 0) }}</td>
                                <td class="text-end text-danger">{{ number_format($row->expense, 0) }}</td>
                                <td class="text-end">{{ number_format($row->time_cost, 0) }}</td>
                                <td class="text-end fw-bold {{ $row->profit >= 0 ? '' : 'text-danger' }}">{{ $row->profit >= 0 ? '+' : '' }}{{ number_format($row->profit, 0) }}</td>
                                <td class="text-end fw-bold {{ $marginColor }}">{{ $row->margin_pct ?? '—' }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-tabler.layout>

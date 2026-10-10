<x-tabler.layout :title="__('Aging Report')" :pretitle="__('Reports')">
    <x-slot name="actions">
        <a href="{{ route('reports.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Reports') }}
        </a>
        <a href="{{ route('reports.aging', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-primary">
            <x-ti name="download" /> CSV
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Per tanggal')" name="as_of">
                            <input type="date" id="as_of" name="as_of" value="{{ request('as_of', $asOf->format('Y-m-d')) }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Mata uang')" name="currency">
                            <select id="currency" name="currency" class="form-select">
                                <option value="">{{ __('Semua') }}</option>
                                @foreach($currencies as $c)
                                    <option value="{{ $c }}" {{ $currency === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-list justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @php
        $bucketIcons = ['current' => 'check', '1_30' => 'clock', '31_60' => 'alert-circle', '61_90' => 'hourglass', 'over_90' => 'alert-circle'];
    @endphp
    <div class="row row-decks row-cards mb-3">
        @foreach($buckets as $key => $b)
            <x-tabler.stat :label="$b['label']" :value="number_format($b['total'], 0)" :icon="$bucketIcons[$key] ?? 'chart-bar'" />
        @endforeach
    </div>

    <div class="card mb-3">
        <div class="card-body text-center">
            <div class="subheader">{{ __('Total Belum Lunas') }}</div>
            <div class="h1 mb-0">{{ number_format($grandTotal, 0) }}</div>
            <div class="text-muted small">{{ __('Per') }} {{ $asOf->format('d M Y') }}</div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="users" /> {{ __('Per Klien') }}</h3>
            <span class="card-subtitle ms-2">{{ count($byClient) }} {{ __('klien dengan invoice belum lunas') }}</span>
        </div>
        @if($byClient->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="circle-check" :title="__('Tidak ada invoice belum lunas')" :body="__('Semua klien sudah bayar tepat waktu — kerja bagus!')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Klien') }}</th>
                            <th class="text-end">{{ __('Belum Jatuh Tempo') }}</th>
                            <th class="text-end">1-30</th>
                            <th class="text-end">31-60</th>
                            <th class="text-end">61-90</th>
                            <th class="text-end">90+</th>
                            <th class="text-end">{{ __('Total') }}</th>
                            <th class="text-center">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byClient as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('clients.show', $row->client) }}" class="fw-semibold">{{ $row->client->name }}</a>
                                    @if($row->client->company_name)
                                        <div class="text-muted small">{{ $row->client->company_name }}</div>
                                    @endif
                                    <div class="text-muted small">{{ $row->invoice_count }} {{ __('invoice') }}</div>
                                </td>
                                <td class="text-end {{ $row->buckets['current'] > 0 ? 'text-success fw-semibold' : 'text-muted' }}">{{ $row->buckets['current'] > 0 ? number_format($row->buckets['current'], 0) : '—' }}</td>
                                <td class="text-end {{ $row->buckets['1_30'] > 0 ? 'fw-semibold' : 'text-muted' }}">{{ $row->buckets['1_30'] > 0 ? number_format($row->buckets['1_30'], 0) : '—' }}</td>
                                <td class="text-end {{ $row->buckets['31_60'] > 0 ? 'fw-semibold' : 'text-muted' }}">{{ $row->buckets['31_60'] > 0 ? number_format($row->buckets['31_60'], 0) : '—' }}</td>
                                <td class="text-end {{ $row->buckets['61_90'] > 0 ? 'fw-bold' : 'text-muted' }}">{{ $row->buckets['61_90'] > 0 ? number_format($row->buckets['61_90'], 0) : '—' }}</td>
                                <td class="text-end">
                                    @if($row->buckets['over_90'] > 0)
                                        <span class="badge bg-red">{{ number_format($row->buckets['over_90'], 0) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ number_format($row->total, 0) }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('invoices.index', ['client_id' => $row->client->id, 'status' => 'unpaid']) }}" class="btn btn-sm">{{ __('Lihat invoice') }}</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-light fw-bold">
                            <td>{{ __('TOTAL') }}</td>
                            <td class="text-end text-success">{{ number_format($buckets['current']['total'], 0) }}</td>
                            <td class="text-end">{{ number_format($buckets['1_30']['total'], 0) }}</td>
                            <td class="text-end">{{ number_format($buckets['31_60']['total'], 0) }}</td>
                            <td class="text-end">{{ number_format($buckets['61_90']['total'], 0) }}</td>
                            <td class="text-end text-danger">{{ number_format($buckets['over_90']['total'], 0) }}</td>
                            <td class="text-end">{{ number_format($grandTotal, 0) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @foreach($buckets as $key => $b)
        @if($b['invoices']->isNotEmpty())
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><x-ti :name="$bucketIcons[$key] ?? 'chart-bar'" /> {{ $b['label'] }}</h3>
                    <span class="card-subtitle ms-2">{{ $b['invoices']->count() }} {{ __('invoice') }}, {{ __('total') }} {{ number_format($b['total'], 0) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>{{ __('No') }}</th>
                                <th>{{ __('Klien') }}</th>
                                <th>{{ __('Tgl Terbit') }}</th>
                                <th>{{ __('Jatuh Tempo') }}</th>
                                <th class="text-end">{{ __('Hari') }}</th>
                                <th class="text-end">{{ __('Sisa') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($b['invoices'] as $inv)
                                <tr>
                                    <td><a href="{{ route('invoices.show', $inv) }}" class="fw-semibold">{{ $inv->invoice_no }}</a></td>
                                    <td class="text-muted">{{ $inv->client?->name }}</td>
                                    <td class="text-muted small">{{ $inv->issue_date->format('d M Y') }}</td>
                                    <td class="text-muted small">{{ $inv->due_date->format('d M Y') }}</td>
                                    <td class="text-end">
                                        @if($inv->days_overdue > 0)
                                            <span class="badge bg-red">{{ $inv->days_overdue }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($inv->balance_due, 0) }} {{ $inv->currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
</x-tabler.layout>

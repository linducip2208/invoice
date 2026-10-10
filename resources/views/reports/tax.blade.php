<x-tabler.layout :title="__('Tax Summary')" :pretitle="__('Reports')">
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
                        <x-tabler.field :label="__('Bulan')" name="period">
                            <input type="month" id="period" name="period" value="{{ $period }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-list justify-content-end">
                            <span class="text-muted small"><x-ti name="calendar" /> {{ $start->format('d M Y') }} — {{ $end->format('d M Y') }}</span>
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-decks row-cards mb-3">
        <x-tabler.stat :label="__('PPN Keluaran')" :value="number_format($totalPpn, 0)" icon="percentage" />
        <x-tabler.stat :label="__('PPh 23 Dipotong')" :value="number_format($totalPph23, 0)" icon="receipt" />
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="file-text" /> {{ __('Status e-Faktur') }}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted small">{{ __('Total tax_amount untuk faktur output') }} · {{ __('Klien wajib setor ke kas negara') }}</p>
            <div class="datagrid">
                @foreach(['approved' => 'Approved', 'draft' => 'Draft', 'rejected' => 'Rejected', 'not_applicable' => 'N/A'] as $key => $label)
                    @if(($efakturStats[$key] ?? 0) > 0)
                        <div class="datagrid-item">
                            <div class="datagrid-title">{{ $label }}</div>
                            <div class="datagrid-content fw-bold">{{ $efakturStats[$key] ?? 0 }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Detail Faktur') }} ({{ $taxDetails->count() }})</h3>
            <span class="card-subtitle ms-2">{{ __('Rekap PPN keluaran & PPh 23 untuk SPT') }}</span>
        </div>
        @if($taxDetails->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="file-text" :title="__('Tidak ada faktur dengan tax details di periode ini.')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('No Invoice') }}</th>
                            <th>{{ __('Klien') }}</th>
                            <th>{{ __('NPWP') }}</th>
                            <th>{{ __('Tgl') }}</th>
                            <th class="text-end">{{ __('DPP') }}</th>
                            <th class="text-end">{{ __('PPN') }}</th>
                            <th class="text-end">{{ __('PPh 23') }}</th>
                            <th class="text-center">{{ __('e-Faktur') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxDetails as $td)
                            <tr>
                                <td><a href="{{ route('invoices.show', $td->invoice) }}" class="fw-semibold">{{ $td->invoice?->invoice_no }}</a></td>
                                <td class="text-muted">{{ $td->invoice?->client?->name }}</td>
                                <td class="text-muted small font-monospace">{{ $td->invoice?->client?->tax_id ?: '—' }}</td>
                                <td class="text-muted small">{{ $td->invoice?->issue_date?->format('d M Y') }}</td>
                                <td class="text-end">{{ number_format($td->invoice?->subtotal ?? 0, 0) }}</td>
                                <td class="text-end text-info fw-semibold">{{ number_format($td->ppn_amount, 0) }}</td>
                                <td class="text-end text-warning fw-semibold">{{ number_format($td->pph23_amount, 0) }}</td>
                                <td class="text-center">
                                    @php $eb = match ($td->efaktur_status) {'approved' => 'success', 'draft' => 'warning', 'rejected' => 'danger', default => 'secondary'}; @endphp
                                    <span class="badge bg-{{ $eb }}">{{ ucfirst($td->efaktur_status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-light fw-bold">
                            <td colspan="5" class="text-end">{{ __('TOTAL') }}</td>
                            <td class="text-end text-info">{{ number_format($totalPpn, 0) }}</td>
                            <td class="text-end text-warning">{{ number_format($totalPph23, 0) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-tabler.layout>

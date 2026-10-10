<x-tabler.layout :title="__('Credit Notes')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('credit-notes.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Credit Note Baru') }}
        </a>
    </x-slot>

    {{-- Stats --}}
    <div class="row g-3 mb-3">
        <x-tabler.stat :label="__('Issued (belum apply)')" :value="number_format($stats['total_issued'], 0)" icon="receipt-refund" />
        <x-tabler.stat :label="__('Sudah Diterapkan')" :value="number_format($stats['total_applied'], 0)" icon="check" />
        <x-tabler.stat :label="__('Draft Pending')" :value="$stats['count_pending']" icon="clock" />
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('credit-notes.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select id="status" name="status" class="form-select">
                                <option value="">{{ __('Semua') }}</option>
                                <option value="draft"     {{ request('status')==='draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                                <option value="issued"    {{ request('status')==='issued' ? 'selected' : '' }}>{{ __('Issued') }}</option>
                                <option value="applied"   {{ request('status')==='applied' ? 'selected' : '' }}>{{ __('Applied') }}</option>
                                <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-list justify-content-end">
                            @if(request()->hasAny(['status','invoice_id']))
                                <a href="{{ route('credit-notes.index') }}" class="btn btn-sm">{{ __('Reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('No') }}</th>
                        <th>{{ __('Invoice') }}</th>
                        <th>{{ __('Klien') }}</th>
                        <th>{{ __('Alasan') }}</th>
                        <th>{{ __('Tgl') }}</th>
                        <th class="text-end">{{ __('Jumlah') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($creditNotes as $cn)
                        <tr>
                            <td>
                                <a href="{{ route('credit-notes.show', $cn) }}" class="fw-bold">{{ $cn->credit_no }}</a>
                            </td>
                            <td>
                                <a href="{{ route('invoices.show', $cn->invoice) }}" class="fw-semibold">{{ $cn->invoice?->invoice_no }}</a>
                            </td>
                            <td class="text-muted small">{{ $cn->invoice?->client?->name }}</td>
                            <td class="small text-muted text-truncate" style="max-width: 14rem;">{{ $cn->reason }}</td>
                            <td class="small text-muted">{{ $cn->issued_at->format('d M Y') }}</td>
                            <td class="text-end fw-bold text-danger">-{{ number_format($cn->amount, 0) }} {{ $cn->currency }}</td>
                            <td class="text-center">
                                <x-tabler.status-badge :status="$cn->status" />
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('credit-notes.show', $cn) }}" class="btn btn-sm">{{ __('Detail') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-tabler.empty icon="receipt-refund" :title="__('Belum ada credit note')"
                                    :body="__('Buat credit note pertama dari halaman invoice.')"
                                    :actionUrl="route('credit-notes.create')" :actionLabel="__('Credit Note Baru')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($creditNotes->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $creditNotes->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

<x-tabler.layout :title="__('Invoices')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('exports.invoices') }}" class="btn"><x-ti name="download" /> {{ __('Export CSV') }}</a>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Create Invoice') }}</a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('invoices.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select id="status" name="status" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                @foreach(['draft','sent','paid','partial','overdue','cancelled'] as $s)
                                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ __(ucfirst($s)) }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Customer')" name="client_id">
                            <select id="client_id" name="client_id" class="form-select">
                                <option value="">{{ __('All customers') }}</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" @selected((string) request('client_id')===(string) $c->id)>{{ $c->name }}{{ $c->company_name ? ' · '.$c->company_name : '' }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Search')" name="search">
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Invoice # or client…') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('From')" name="date_from">
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('To')" name="date_to">
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-12"><div class="btn-list justify-content-end">
                        @if(request()->anyFilled(['status','search','date_from','date_to','client_id','sort','order']))
                            <a href="{{ route('invoices.index') }}" class="btn">{{ __('Reset') }}</a>
                        @endif
                        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    </div></div>
                </div>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('invoices.bulk-destroy') }}" id="bulk-form">
        @csrf
        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ __('Invoice list (:n)', ['n' => $invoices->total()]) }}</h3>
                <div class="card-actions">
                    <button type="submit" formaction="{{ route('invoices.bulk-send') }}" class="btn btn-sm btn-outline-primary" onclick="return confirm('{{ __('Kirim (email + PDF) invoice terpilih? Maks 10 per aksi.') }}')">{{ __('Bulk send') }}</button>
                    <button type="submit" formaction="{{ route('invoices.bulk-remind') }}" class="btn btn-sm btn-outline-warning">{{ __('Bulk remind') }}</button>
                    <button type="submit" formaction="{{ route('invoices.bulk-mark-sent') }}" class="btn btn-sm btn-outline-success" onclick="return confirm('{{ __('Tandai terpilih sebagai terkirim? Tanpa kirim email.') }}')">{{ __('Bulk mark sent') }}</button>
                    <button type="submit" formaction="{{ route('invoices.bulk-pdf') }}" class="btn btn-sm btn-outline-secondary">{{ __('Bulk PDF (ZIP)') }}</button>
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Delete selected drafts? Only drafts will be removed.') }}')">{{ __('Bulk delete drafts') }}</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr>
                        <th class="w-1"><input type="checkbox" class="form-check-input" id="check-all" aria-label="{{ __('Select all') }}"></th>
                        @php $link = fn($col,$label) => ['col'=>$col,'label'=>$label,'active'=>$sort===$col,'dir'=>$sort===$col&&$order==='asc'?'desc':'asc']; @endphp
                        @foreach([['invoice_no',__('Invoice #')],['issue_date',__('Issue')],['due_date',__('Due')],['total',__('Total')],['balance_due',__('Balance')]] as [$col,$label])
                            @php $q = array_merge(request()->query(), ['sort'=>$col,'order'=>$sort===$col&&$order==='asc'?'desc':'asc']); @endphp
                            <th @if(in_array($col,['total','balance_due'])) class="text-end" @endif><a href="{{ route('invoices.index', $q) }}">{{ $label }} @if($sort===$col){{ $order==='asc'?'↑':'↓' }}@endif</a></th>
                        @endforeach
                        <th>{{ __('Client') }}</th><th class="text-center">{{ __('Status') }}</th><th class="text-end">{{ __('Actions') }}</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="{{ $invoice->id }}" class="form-check-input row-check" aria-label="{{ $invoice->invoice_no }}"></td>
                                <td><a href="{{ route('invoices.show', $invoice) }}" class="fw-semibold">{{ $invoice->invoice_no }}</a></td>
                                <td class="text-muted">{{ $invoice->issue_date?->format('M d, Y') }}</td>
                                <td class="text-muted">{{ $invoice->due_date?->format('M d, Y') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($invoice->total, 2) }}</td>
                                <td class="text-end fw-semibold {{ $invoice->balance_due > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($invoice->balance_due, 2) }}</td>
                                <td class="text-muted">{{ $invoice->client?->name ?? '—' }}</td>
                                <td class="text-center"><x-tabler.status-badge :status="$invoice->status" /></td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm">{{ __('View') }}</a>
                                    @if($invoice->status === 'draft')
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                    @endif
                                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-secondary">{{ __('PDF') }}</a>
                                    @if($invoice->status === 'draft')
                                        <x-tabler.delete-button :action="route('invoices.destroy', $invoice)" :confirm="__('Delete this invoice?')" :title="__('Delete')" />
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9"><x-tabler.empty icon="file-invoice" :title="__('No invoices found.')" :body="__('Create your first invoice to get started.')" :actionUrl="route('invoices.create')" :actionLabel="__('Create Invoice')" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($invoices->hasPages())<div class="card-footer d-flex justify-content-center">{{ $invoices->links() }}</div>@endif
        </div>
    </form>
</x-tabler.layout>
@push('scripts')
<script>
document.getElementById('check-all')?.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(function (c) { c.checked = document.getElementById('check-all').checked; });
});
</script>
@endpush

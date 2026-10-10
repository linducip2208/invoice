<x-tabler.layout :title="$client->name" :pretitle="__('Sales')" :breadcrumbs="[['label' => __('Customers'), 'url' => route('clients.index')], ['label' => $client->name]]">
    <x-slot name="actions">
        <a href="{{ route('clients.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-warning">
            <x-ti name="pencil" /> {{ __('Edit') }}
        </a>
        <x-tabler.delete-button :action="route('clients.destroy', $client)"
            :confirm="__('Delete this client?')" :label="__('Delete')" />
    </x-slot>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Client Details') }}</h3>
            @if($client->is_active)
                <span class="badge bg-green ms-2">{{ __('Active') }}</span>
            @else
                <span class="badge bg-red ms-2">{{ __('Inactive') }}</span>
            @endif
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="avatar">{{ strtoupper(mb_substr($client->name, 0, 1)) }}</span>
                <div>
                    <div class="fw-semibold">{{ $client->name }}</div>
                    @if($client->company_name)
                        <div class="text-muted small">{{ $client->company_name }}</div>
                    @endif
                </div>
            </div>
            <div class="datagrid">
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Name') }}</div>
                    <div class="datagrid-content">{{ $client->name }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Company') }}</div>
                    <div class="datagrid-content">{{ $client->company_name ?? '—' }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Email') }}</div>
                    <div class="datagrid-content">{{ $client->email ?? '—' }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Phone') }}</div>
                    <div class="datagrid-content">{{ $client->phone ?? '—' }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Address') }}</div>
                    <div class="datagrid-content">
                        {{ $client->address ?? '—' }}
                        @if($client->city || $client->state || $client->postal_code)
                            <br>{{ collect([$client->city, $client->state, $client->postal_code])->filter()->join(', ') }}
                        @endif
                        @if($client->country) <br>{{ $client->country }} @endif
                    </div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Tax ID') }}</div>
                    <div class="datagrid-content">{{ $client->tax_id ?? '—' }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Total Invoices') }}</div>
                    <div class="datagrid-content">{{ $client->invoices_count }}</div>
                </div>
                @if($client->notes)
                    <div class="datagrid-item">
                        <div class="datagrid-title">{{ __('Notes') }}</div>
                        <div class="datagrid-content">{{ $client->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Invoices') }}</h3>
            <div class="ms-auto">
                <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="btn btn-primary btn-sm">
                    <x-ti name="plus" /> {{ __('New Invoice') }}
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Invoice No') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Issue') }}</th>
                        <th>{{ __('Due') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                        <th class="text-end">{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td><a href="{{ route('invoices.show', $invoice) }}" class="fw-semibold">{{ $invoice->invoice_no }}</a></td>
                            <td><x-tabler.status-badge :status="$invoice->status" /></td>
                            <td class="text-muted">{{ $invoice->issue_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="text-muted">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="text-end fw-semibold">{{ number_format($invoice->total, 2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($invoice->balance_due, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-tabler.empty icon="file-invoice" :title="__('No invoices yet for this sparkle.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $invoices->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

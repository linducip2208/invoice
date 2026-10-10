<x-tabler.layout :title="__('Dashboard')" :pretitle="__('Overview')" :subtitle="__('A magical overview of your business')">
    <x-slot name="actions">
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('New Invoice') }}
        </a>
    </x-slot>

    @php $currency = workspace()?->currency ?? 'IDR'; @endphp

    <div class="row row-deck row-cards mb-3">
        <x-tabler.stat :label="__('Total Revenue')" :value="$currency . ' ' . number_format($totalRevenue, 0)" icon="wallet" />
        <x-tabler.stat :label="__('Outstanding')" :value="number_format($outstandingAmount, 0)" icon="hourglass" />
        <x-tabler.stat :label="__('Paid')" :value="$paidCount" icon="check" />
        <x-tabler.stat :label="__('Overdue')" :value="$overdueCount" icon="alert-circle" />
        <x-tabler.stat :label="__('Clients')" :value="$clientCount" icon="users" />
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Recent Invoices') }}</h3>
            <div class="card-actions">
                <a href="{{ route('invoices.index') }}" class="btn btn-sm">{{ __('View All') }}</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Invoice #') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Due Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentInvoices as $invoice)
                        <tr>
                            <td><a href="{{ route('invoices.show', $invoice) }}" class="fw-semibold">{{ $invoice->invoice_no }}</a></td>
                            <td class="text-muted">{{ $invoice->client?->name ?? '—' }}</td>
                            <td class="fw-semibold"><span class="text-muted small me-1">{{ $invoice->currency }}</span>{{ number_format($invoice->total, 2) }}</td>
                            <td><x-tabler.status-badge :status="$invoice->status" /></td>
                            <td class="text-muted">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-tabler.empty icon="file-invoice" :title="__('No invoices yet!')"
                                    :body="__('Conjure your first sparkly invoice to get started.')"
                                    :actionUrl="route('invoices.create')" :actionLabel="__('Create First Invoice')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @php
        $quickActions = [
            ['label' => __('Add Client'), 'href' => route('clients.create'), 'icon' => 'users'],
            ['label' => __('New Product'), 'href' => route('products.create'), 'icon' => 'package'],
            ['label' => __('Recurring'), 'href' => route('recurring.create'), 'icon' => 'repeat'],
            ['label' => __('Workspace'), 'href' => route('workspace.settings'), 'icon' => 'settings'],
        ];
    @endphp
    <div class="row row-deck row-cards">
        @foreach($quickActions as $action)
            <div class="col-sm-6 col-lg-3">
                <a href="{{ $action['href'] }}" class="card card-link">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="avatar"><x-ti :name="$action['icon']" /></span>
                        <div>
                            <div class="fw-semibold">{{ $action['label'] }}</div>
                            <div class="text-muted small">{{ __('Get started') }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</x-tabler.layout>

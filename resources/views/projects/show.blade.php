<x-tabler.layout :title="$project->name">
    <x-slot name="actions">
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-warning">
            <x-ti name="pencil" /> {{ __('Edit') }}
        </a>
        <a href="{{ route('projects.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Semua Project') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <span class="avatar" style="background: {{ $project->color ?? '#A78BFA' }};">{{ strtoupper(mb_substr($project->name, 0, 1)) }}</span>
                <div>
                    <h3 class="card-title mb-1">{{ $project->name }}</h3>
                    <div class="text-muted small">
                        <x-ti name="user" /> {{ $project->client?->name }}
                        <span class="badge bg-{{ match($project->status){'active'=>'green','on_hold'=>'yellow','completed'=>'blue','cancelled'=>'red',default=>'secondary'} }} ms-1">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-3">
        <x-tabler.stat :label="__('Total Tagihan')" :value="number_format($stats['total_invoiced'], 0)" icon="file-invoice" />
        <x-tabler.stat :label="__('Sudah Lunas')" :value="number_format($stats['total_paid'], 0)" icon="check" />
        <x-tabler.stat :label="__('Total Jam')" :value="number_format($stats['total_hours'], 1)" icon="clock" />
        <x-tabler.stat :label="__('Pengeluaran')" :value="number_format($stats['total_expense'], 0)" icon="receipt" />
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="datagrid">
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Sisa Tagihan') }}</div>
                    <div class="datagrid-content">{{ number_format($stats['total_unpaid'], 0) }}</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Invoice') }}</div>
                    <div class="datagrid-content">{{ $stats['invoice_count'] }} invoice</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Time Entries') }}</div>
                    <div class="datagrid-content">{{ $stats['time_count'] }} entries</div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Expenses') }}</div>
                    <div class="datagrid-content">{{ $stats['expense_count'] }} item</div>
                </div>
            </div>
        </div>
    </div>

    @if($stats['total_invoiced'] > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Margin') }}</h3>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="h1 mb-1 {{ $stats['margin'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $stats['margin'] >= 0 ? '+' : '' }}{{ number_format($stats['margin'], 0) }}
                        </div>
                        <div class="text-muted">{{ $stats['margin_pct'] }}% margin</div>
                    </div>
                    @if($project->budget_amount)
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Budget Used</span>
                                <span>{{ number_format($stats['total_invoiced'], 0) }} / {{ number_format($project->budget_amount, 0) }}</span>
                            </div>
                            <div class="progress">
                                @php $pct = min(100, $stats['total_invoiced'] / $project->budget_amount * 100); @endphp
                                <div class="progress-bar" style="width: {{ $pct }}%;"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($project->description)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Deskripsi') }}</h3>
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-line;">{{ $project->description }}</p>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Invoice di Project Ini') }}</h3>
            <div class="ms-auto">
                <a href="{{ route('invoices.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                    <x-ti name="plus" /> Invoice
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Invoice') }}</th>
                        <th>{{ __('Tanggal') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $inv)
                        <tr>
                            <td><a href="{{ route('invoices.show', $inv) }}" class="fw-semibold">{{ $inv->invoice_no }}</a></td>
                            <td class="text-muted">{{ $inv->issue_date->format('d M Y') }}</td>
                            <td><x-tabler.status-badge :status="$inv->status" /></td>
                            <td class="text-end fw-semibold">{{ number_format($inv->total, 0) }} {{ $inv->currency }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-tabler.empty icon="file-invoice" :title="__('Belum ada invoice di project ini.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-tabler.layout>

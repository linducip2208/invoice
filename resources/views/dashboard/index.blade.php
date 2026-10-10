<x-tabler.layout :title="__('Dashboard')" :pretitle="__('Workspace')" :subtitle="__('A professional overview of your receivables')">
    <x-slot name="actions">
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('New Invoice') }}
        </a>
        <a href="{{ route('quotes.create') }}" class="btn">
            <x-ti name="plus" /> {{ __('New Quotation') }}
        </a>
    </x-slot>

    @php $currency = workspace()?->currency ?? 'IDR'; @endphp

    <div class="row row-deck row-cards mb-3">
        <x-tabler.stat :label="__('Collected')" :value="$currency . ' ' . number_format($totalRevenue, 0)" icon="wallet" />
        <x-tabler.stat :label="__('Outstanding')" :value="$currency . ' ' . number_format($outstandingAmount, 0)" icon="hourglass" />
        <x-tabler.stat :label="__('Paid invoices')" :value="$paidCount" icon="check" />
        <x-tabler.stat :label="__('Overdue')" :value="$overdueCount" icon="alert-circle" />
    </div>

    <div class="row row-deck row-cards mb-3">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Collections (8 weeks)') }}</h3>
                </div>
                <div class="card-body">
                    @php
                        $max = max(1, max($trend));
                        $w = 560; $h = 160; $pad = 8;
                        $stepX = ($w - $pad * 2) / 7;
                        $points = [];
                        foreach ($trend as $i => $v) {
                            $x = $pad + $i * $stepX;
                            $y = $h - $pad - (($v / $max) * ($h - $pad * 2));
                            $points[] = [$x, $y, $v];
                        }
                        $line = implode(' ', array_map(fn($p) => $p[0] . ',' . $p[1], $points));
                        $area = $pad . ',' . ($h - $pad) . ' ' . $line . ' ' . ($w - $pad) . ',' . ($h - $pad);
                    @endphp
                    @if(array_sum($trend) > 0)
                        <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-100" style="height: 12rem;" role="img" aria-label="{{ __('Collections trend') }}">
                            <polygon points="{{ $area }}" fill="rgba(124,92,240,0.15)" />
                            <polyline points="{{ $line }}" fill="none" stroke="#7c5cf0" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
                            @foreach($points as $i => $p)
                                <circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="3.5" fill="#7c5cf0">
                                    <title>{{ $trendLabels[$i] }}: {{ $currency }} {{ number_format($p[2], 0) }}</title>
                                </circle>
                            @endforeach
                        </svg>
                        <div class="d-flex justify-content-between text-muted small mt-1" aria-hidden="true">
                            <span>{{ $trendLabels[0] }}</span>
                            <span>{{ $trendLabels[7] }}</span>
                        </div>
                    @else
                        <x-tabler.empty icon="chart-line" :title="__('No collections yet')" :body="__('Recorded payments will appear here as a weekly trend.')" />
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Overdue invoices') }}</h3>
                    <div class="card-actions">
                        <a href="{{ route('reports.aging') }}" class="btn btn-sm">{{ __('View') }}</a>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($overdueInvoices as $inv)
                        <a href="{{ route('invoices.show', $inv) }}" class="list-group-item list-group-item-action">
                            <div class="row align-items-center g-2">
                                <div class="col text-truncate">
                                    <div class="fw-semibold text-truncate">{{ $inv->invoice_no }}</div>
                                    <div class="text-muted small text-truncate">{{ $inv->client?->name }} · {{ $inv->due_date?->format('d M Y') }}</div>
                                </div>
                                <div class="col-auto text-end">
                                    <div class="fw-bold">{{ number_format($inv->balance_due, 0) }}</div>
                                    <div class="text-muted small">{{ $inv->currency }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-3 text-muted">{{ __('No overdue invoices. Good work.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mb-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Invoice status') }}</h3>
                </div>
                <div class="card-body">
                    @php
                        $slices = [
                            ['label' => __('Paid'), 'color' => '#2fb344', 'value' => $statusCounts['paid'] ?? 0],
                            ['label' => __('Sent'), 'color' => '#4299e1', 'value' => ($statusCounts['sent'] ?? 0) + ($statusCounts['partial'] ?? 0)],
                            ['label' => __('Draft'), 'color' => '#a8a3e0', 'value' => $statusCounts['draft'] ?? 0],
                            ['label' => __('Overdue'), 'color' => '#d63939', 'value' => $statusCounts['overdue'] ?? 0],
                            ['label' => __('Cancelled'), 'color' => '#6c757d', 'value' => $statusCounts['cancelled'] ?? 0],
                        ];
                        $donutTotal = max(1, array_sum(array_column($slices, 'value')));
                        $offset = 25;
                    @endphp
                    @if(array_sum(array_column($slices, 'value')) > 0)
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <svg width="120" height="120" viewBox="0 0 42 42" role="img" aria-label="{{ __('Invoice status distribution') }}">
                                    <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#e9ecef" stroke-width="6" />
                                    @foreach($slices as $s)
                                        @if($s['value'] > 0)
                                            <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="{{ $s['color'] }}" stroke-width="6"
                                                stroke-dasharray="{{ round($s['value'] / $donutTotal * 100, 1) }} {{ round(100 - $s['value'] / $donutTotal * 100, 1) }}"
                                                stroke-dashoffset="{{ $offset }}" stroke-linecap="butt" />
                                            @php $offset -= $s['value'] / $donutTotal * 100; @endphp
                                        @endif
                                    @endforeach
                                    <text x="21" y="23" text-anchor="middle" font-size="7" font-weight="bold" fill="currentColor">{{ $donutTotal }}</text>
                                </svg>
                            </div>
                            <div class="col">
                                @foreach($slices as $s)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge-dot" style="background: {{ $s['color'] }}"></span>
                                        <span class="text-muted">{{ $s['label'] }}</span>
                                        <span class="ms-auto fw-semibold">{{ $s['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('No invoices yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Receivables aging') }}</h3>
                    <div class="card-actions">
                        <a href="{{ route('reports.aging') }}" class="btn btn-sm">{{ __('View') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $buckets = [
                            __('Current') => $aging['current'],
                            __('1–30 days') => $aging['1_30'],
                            __('31–60 days') => $aging['31_60'],
                            __('61–90 days') => $aging['61_90'],
                            __('90+ days') => $aging['over_90'],
                        ];
                        $maxBucket = max(1, max($buckets));
                    @endphp
                    @if(array_sum($buckets) > 0)
                        @foreach($buckets as $label => $value)
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-3 col-md-2 text-muted small">{{ $label }}</div>
                                <div class="col-6 col-md-8">
                                    <div class="progress" style="height: 0.65rem;" role="progressbar"
                                        aria-valuenow="{{ round($value) }}" aria-valuemin="0" aria-valuemax="{{ round($maxBucket) }}"
                                        aria-label="{{ $label }}: {{ number_format($value, 0) }}">
                                        <div class="progress-bar bg-primary" style="width: {{ round($value / $maxBucket * 100, 1) }}%"></div>
                                    </div>
                                </div>
                                <div class="col-3 col-md-2 text-end fw-semibold text-nowrap">{{ number_format($value, 0) }}</div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">{{ __('Nothing outstanding.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row row-deck row-cards mb-3">
        <div class="col-lg-6">
            <div class="card h-100">
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
                                <th class="text-end">{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Due Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentInvoices as $invoice)
                                <tr>
                                    <td><a href="{{ route('invoices.show', $invoice) }}" class="fw-semibold">{{ $invoice->invoice_no }}</a></td>
                                    <td class="text-muted">{{ $invoice->client?->name ?? '—' }}</td>
                                    <td class="text-end fw-semibold"><span class="text-muted small me-1">{{ $invoice->currency }}</span>{{ number_format($invoice->total, 2) }}</td>
                                    <td><x-tabler.status-badge :status="$invoice->status" /></td>
                                    <td class="text-muted">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-tabler.empty icon="file-invoice" :title="__('No invoices yet')"
                                            :body="__('Create your first invoice to get started.')"
                                            :actionUrl="route('invoices.create')" :actionLabel="__('Create Invoice')" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Quotes awaiting response') }}</h3>
                    <div class="card-actions">
                        <a href="{{ route('quotes.index') }}" class="btn btn-sm">{{ __('View') }}</a>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($pendingQuotes as $quote)
                        <a href="{{ route('quotes.show', $quote) }}" class="list-group-item list-group-item-action">
                            <div class="row align-items-center g-2">
                                <div class="col text-truncate">
                                    <div class="fw-semibold text-truncate">{{ $quote->quote_no }}</div>
                                    <div class="text-muted small text-truncate">{{ $quote->client?->name }}</div>
                                </div>
                                <div class="col-auto text-end">
                                    <div class="fw-bold">{{ number_format($quote->total, 0) }}</div>
                                    <div class="text-muted small">{{ $quote->currency }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-3 text-muted">{{ __('No pending quotes.') }}</div>
                    @endforelse
                </div>
            </div>
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

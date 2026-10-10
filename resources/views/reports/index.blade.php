<x-tabler.layout :title="__('Reports')" :pretitle="__('Reports')">
    <x-slot name="actions">
        <form method="GET">
            <select name="period" onchange="this.form.submit()" class="form-select">
                <option value="7" @selected($period === '7')>{{ __('Last 7 days') }}</option>
                <option value="30" @selected($period === '30')>{{ __('Last 30 days') }}</option>
                <option value="90" @selected($period === '90')>{{ __('Last 90 days') }}</option>
                <option value="365" @selected($period === '365')>{{ __('Last 12 months') }}</option>
                <option value="ytd" @selected($period === 'ytd')>{{ __('Year to date') }}</option>
            </select>
        </form>
    </x-slot>

    @php $cur = workspace()?->currency ?? 'IDR'; @endphp

    <div class="row row-decks row-cards mb-3">
        <x-tabler.stat :label="__('Invoiced')" :value="$cur . ' ' . number_format($totals['invoiced'], 0)" icon="file-invoice" />
        <x-tabler.stat :label="__('Collected')" :value="$cur . ' ' . number_format($totals['collected'], 0)" icon="credit-card" />
        <x-tabler.stat :label="__('Outstanding')" :value="$cur . ' ' . number_format($totals['outstanding'], 0)" icon="hourglass" />
        <x-tabler.stat :label="__('Overdue')" :value="$cur . ' ' . number_format($totals['overdue'], 0)" icon="alert-circle" />
    </div>
    <p class="text-muted small mb-3">{{ $totals['invoiceCount'] }} {{ __('invoices') }} · {{ $totals['paymentCount'] }} {{ __('payments') }} · {{ __('All-time pending') }}</p>

    @if($monthly->isNotEmpty())
        @php $maxMonthly = max($monthly->pluck('total')->toArray()) ?: 1; @endphp
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><x-ti name="chart-bar" /> {{ __('Monthly Invoiced (last 12 months)') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-end gap-2 overflow-auto pb-2" style="min-height: 11rem;">
                    @foreach($monthly as $m)
                        @php $h = max(8, ($m->total / $maxMonthly) * 100); @endphp
                        <div class="flex-fill text-center" style="min-width: 40px;">
                            <div class="small fw-semibold">{{ number_format($m->total / 1000, 1) }}k</div>
                            <div class="rounded-top bg-primary mx-auto" style="height: {{ $h }}px; opacity: .8;" title="{{ $m->total }}"></div>
                            <div class="small text-muted mt-1 text-nowrap">{{ \Carbon\Carbon::parse($m->month . '-01')->format('M y') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row row-decks row-cards">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><x-ti name="clipboard-list" /> {{ __('Invoice Status') }}</h3>
                </div>
                <div class="card-body">
                    @foreach(['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'] as $st)
                        @php $row = $statusBreakdown->get($st); $count = $row->count ?? 0; $total = $row->total ?? 0; @endphp
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="badge bg-{{ match ($st) {'draft' => 'secondary', 'sent' => 'info', 'paid' => 'success', 'partial' => 'warning', 'overdue' => 'danger', default => 'secondary'} }}" style="min-width: 80px;">{{ ucfirst($st) }}</span>
                            <div class="progress flex-fill">
                                <div class="progress-bar bg-primary" style="width: {{ $count ? min(100, $count * 8) : 0 }}%"></div>
                            </div>
                            <span class="fw-bold" style="min-width: 40px;" align="right">{{ $count }}</span>
                            <span class="text-muted small text-end" style="min-width: 80px;">{{ number_format($total, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><x-ti name="wallet" /> {{ __('Payments by Method') }}</h3>
                </div>
                <div class="card-body">
                    @if($methodBreakdown->isEmpty())
                        <x-tabler.empty icon="wallet" :title="__('No payments in this period.')" />
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($methodBreakdown as $m)
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <strong>{{ ucwords(str_replace('_', ' ', $m->payment_method)) }}</strong>
                                            <div class="text-muted small">{{ $m->count }} {{ __('payments') }}</div>
                                        </div>
                                        <div class="col-auto fw-bold text-success">{{ number_format($m->total, 0) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="users" /> {{ __('Top Clients') }}</h3>
        </div>
        <div class="card-body">
            @if($topClients->where('revenue', '>', 0)->isEmpty())
                <x-tabler.empty icon="users" :title="__('No client revenue yet — invite some sparkles!')" />
            @else
                <div class="list-group list-group-flush">
                    @foreach($topClients as $client)
                        @if($client->revenue)
                            <a href="{{ route('clients.show', $client) }}" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="col text-truncate">
                                        <strong>{{ $client->name }}</strong>
                                        <div class="text-muted small">{{ $client->invoice_count }} {{ __('invoices') }}</div>
                                    </div>
                                    <div class="col-auto fw-bold text-success">{{ number_format($client->revenue, 0) }}</div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-tabler.layout>

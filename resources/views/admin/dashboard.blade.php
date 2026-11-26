<x-tabler.admin-layout :title="__('Dashboard')" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <div class="btn-group" role="group" aria-label="{{ __('Mata uang tampil') }}">
            <a href="{{ route('admin.dashboard', ['cur' => 'IDR']) }}" class="btn btn-sm {{ $displayCurrency === 'IDR' ? 'btn-primary' : 'btn-outline-primary' }}">IDR</a>
            <a href="{{ route('admin.dashboard', ['cur' => 'USD']) }}" class="btn btn-sm {{ $displayCurrency === 'USD' ? 'btn-primary' : 'btn-outline-primary' }}">USD</a>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Create plan') }}</a>
        <a href="{{ route('admin.coupons.index') }}" class="btn"><x-ti name="percentage" /> {{ __('Create coupon') }}</a>
        <a href="{{ route('admin.manual-payments.index') }}" class="btn">{{ __('Pending payments') }}</a>
    </x-slot>

    {{-- KPI --}}
    <div class="row row-cards mb-3" aria-label="{{ __('Key metrics') }}">
        <x-tabler.stat :label="__('Total Users')" :value="number_format($totalUsers)" icon="users" />
        <x-tabler.stat :label="__('Total Workspaces')" :value="number_format($totalWorkspaces)" icon="building" />
        <x-tabler.stat :label="__('Active Paid Subs')" :value="number_format($activeSubs)" icon="credit-card" />
        <x-tabler.stat :label="__('Free Workspaces')" :value="number_format($freeWorkspaces)" icon="wallet" />
    </div>
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-4">
            <div class="card invocely-kpi"><div class="card-body">
                <div class="subheader">{{ __('MRR This Month (IDR)') }}</div>
                <div class="h1 mb-0">Rp{{ number_format($mrrIdr, 0, ',', '.') }}</div>
                <div class="small {{ $growthIdr >= 0 ? 'text-success' : 'text-danger' }}">{{ ($growthIdr >= 0 ? '+' : '') . $growthIdr }}% {{ __('vs last month') }} (Rp{{ number_format($lastMrrIdr, 0, ',', '.') }})</div>
            </div></div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card invocely-kpi"><div class="card-body">
                <div class="subheader">{{ __('MRR This Month (USD)') }}</div>
                <div class="h1 mb-0">${{ number_format($mrrUsd, 2) }}</div>
                <div class="small {{ $growthUsd >= 0 ? 'text-success' : 'text-danger' }}">{{ ($growthUsd >= 0 ? '+' : '') . $growthUsd }}% {{ __('vs last month') }} (${{ number_format($lastMrrUsd, 2) }})</div>
                @if($mrrUsdIdr !== null)
                    <div class="small text-muted mt-1">≈ Rp{{ number_format($mrrUsdIdr, 0, ',', '.') }} <span title="{{ __('Kurs') }} 1 USD = Rp{{ number_format($fxRate, 0, ',', '.') }}{{ $fxDate ? ' · ' . $fxDate : '' }}">({{ __('kurs') }} {{ $fxDate ?? __('hari ini') }})</span></div>
                @endif
            </div></div>
        </div>
        <div class="col-sm-12 col-lg-4">
            <div class="card invocely-kpi"><div class="card-body">
                <div class="subheader">{{ __('Total MRR (setara :cur)', ['cur' => $displayCurrency]) }}</div>
                @if($mrrTotalDisplay !== null)
                    <div class="h1 mb-0">{{ $displayCurrency === 'USD' ? '$' . number_format($mrrTotalDisplay, 2) : 'Rp' . number_format($mrrTotalDisplay, 0, ',', '.') }}</div>
                    <div class="small text-muted">{{ __('Kurs') }} 1 USD = Rp{{ number_format($fxRate, 0, ',', '.') }}{{ $fxDate ? ' · ' . $fxDate : '' }} · <a href="{{ route('admin.settings') }}">{{ __('Ubah di Settings') }}</a></div>
                @else
                    <div class="h1 mb-0 text-muted">—</div>
                    <div class="small text-muted">{{ __('Kurs belum tersedia. Atur di Settings.') }}</div>
                @endif
                <div class="small {{ $growthIdr >= 0 && $growthUsd >= 0 ? 'text-success' : 'text-muted' }} mt-1">{{ __('Growth') }}: {{ $growthIdr }}% IDR · {{ $growthUsd }}% USD</div>
                <div class="small text-muted mt-1">{{ __('Conversion') }}: {{ $conversion }}% · {{ __('Churn (30d)') }}: {{ $churnRate === null ? __('n/a — insufficient data') : $churnRate . '% (' . $churned . ')' }}</div>
            </div></div>
        </div>
    </div>

    {{-- ANALYTICS --}}
    <div class="row row-cards mb-3">
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header"><h3 class="card-title">{{ __('User & workspace growth (12 weeks)') }}</h3><div class="card-actions"><a href="{{ route('admin.analytics') }}" class="btn btn-sm">{{ __('Full analytics') }}</a></div></div>
                <div class="card-body">
                    <div class="subheader mb-2">{{ __('Cumulative totals per week') }}</div>
                    @php $maxU = max(1, collect($userGrowth)->max('value')); $maxW = max(1, collect($workspaceGrowth)->max('value')); @endphp
                    <div class="mb-1 fw-semibold small">{{ __('Users') }}</div>
                    <div class="bar-chart mb-3" role="img" aria-label="{{ __('User growth chart') }}">
                        @foreach($userGrowth as $p)<div style="height:{{ max(4, round($p['value'] / $maxU * 100)) }}%" title="{{ $p['label'] }}: {{ $p['value'] }}"></div>@endforeach
                    </div>
                    <div class="mb-1 fw-semibold small">{{ __('Workspaces') }}</div>
                    <div class="bar-chart" role="img" aria-label="{{ __('Workspace growth chart') }}">
                        @foreach($workspaceGrowth as $p)<div class="alt" style="height:{{ max(4, round($p['value'] / $maxW * 100)) }}%" title="{{ $p['label'] }}: {{ $p['value'] }}"></div>@endforeach
                    </div>
                    <div class="d-flex justify-content-between text-muted small mt-1"><span>{{ $userGrowth[0]['label'] ?? '' }}</span><span>{{ $userGrowth[count($userGrowth)-1]['label'] ?? '' }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header"><h3 class="card-title">{{ __('MRR trends + distribution') }}</h3><div class="card-actions"><a href="{{ route('admin.revenue') }}" class="btn btn-sm">{{ __('Revenue reports') }}</a></div></div>
                <div class="card-body">
                    <div class="table-responsive"><table class="table table-sm table-vcenter mb-3">
                        <thead><tr><th>{{ __('Week') }}</th><th class="text-end">{{ __('IDR') }}</th><th class="text-end">{{ __('USD') }}</th></tr></thead>
                        <tbody>@foreach(array_slice($mrrSeries, -6) as $r)<tr><td class="text-muted">{{ $r['label'] }}</td><td class="text-end">Rp{{ number_format($r['idr'], 0, ',', '.') }}</td><td class="text-end">${{ number_format($r['usd'], 2) }}</td></tr>@endforeach</tbody>
                    </table></div>
                    <div class="row g-2">
                        <div class="col-sm-6"><div class="subheader mb-1">{{ __('Plan distribution') }}</div><ul class="list-unstyled small mb-0">@foreach($planDistribution as $p)<li class="d-flex justify-content-between"><span>{{ $p->name }}</span><strong>{{ number_format($p->subscriptions_count) }}</strong></li>@endforeach</ul></div>
                        <div class="col-sm-6"><div class="subheader mb-1">{{ __('Revenue by currency') }}</div><ul class="list-unstyled small mb-0">@forelse($revenueByCurrency as $r)<li class="d-flex justify-content-between"><span>{{ $r->currency }} ({{ $r->cnt }}×)</span><strong>{{ $r->currency === 'IDR' ? 'Rp' . number_format($r->total, 0, ',', '.') : '$' . number_format($r->total, 2) }}</strong></li>@empty<li class="text-muted">{{ __('No paid revenue yet.') }}</li>@endforelse</ul></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUBSCRIPTIONS --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Subscription management') }}</h3>
            <div class="card-actions">
                <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('Filter subscriptions') }}">
                    <button type="button" class="btn active" data-sub-filter="all">{{ __('All') }}</button>
                    <button type="button" class="btn" data-sub-filter="active">{{ __('Active') }}</button>
                    <button type="button" class="btn" data-sub-filter="pending">{{ __('Pending') }}</button>
                    <button type="button" class="btn" data-sub-filter="expired">{{ __('Expired') }}</button>
                </div>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-primary">{{ __('Manage subscriptions') }}</a>
            </div>
        </div>
        <div class="card-body py-2">
            <div class="row g-2 text-center">
                <div class="col-6 col-md-3"><div class="fw-bold h3 mb-0 text-success">{{ number_format($activeSubs) }}</div><div class="text-muted small">{{ __('Active') }}</div></div>
                <div class="col-6 col-md-3"><div class="fw-bold h3 mb-0 text-warning">{{ number_format($pendingSubs) }}</div><div class="text-muted small">{{ __('Pending') }}</div></div>
                <div class="col-6 col-md-3"><div class="fw-bold h3 mb-0 text-danger">{{ number_format($expiredSubs) }}</div><div class="text-muted small">{{ __('Expired/Cancelled') }}</div></div>
                <div class="col-6 col-md-3"><div class="fw-bold h3 mb-0">{{ count($upcomingRenewals) }}</div><div class="text-muted small">{{ __('Renewals ≤14d') }}</div></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table" id="recent-subs-table">
                <thead><tr><th>{{ __('Workspace') }}</th><th>{{ __('Plan') }}</th><th>{{ __('Status') }}</th><th>{{ __('Period') }}</th><th>{{ __('Next Billing') }}</th></tr></thead>
                <tbody>
                    @forelse($recentSubs as $sub)
                        <tr data-status="{{ $sub->status }}">
                            <td class="fw-semibold">{{ $sub->workspace?->name ?? '—' }}</td>
                            <td class="text-muted">{{ $sub->plan?->name ?? '—' }}</td>
                            <td><x-tabler.status-badge :status="$sub->status" /></td>
                            <td class="text-muted">{{ ucfirst($sub->billing_period) }}</td>
                            <td class="text-muted">{{ $sub->current_period_ends_at?->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-tabler.empty icon="repeat" :title="__('No subscriptions yet')" :body="__('New subscriptions will appear here.')" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($upcomingRenewals))
            <div class="card-body border-top">
                <div class="subheader mb-2">{{ __('Upcoming renewals (14 days)') }}</div>
                <div class="row g-2">@foreach($upcomingRenewals as $s)<div class="col-md-6"><div class="d-flex justify-content-between small border rounded px-2 py-1"><span>{{ $s->workspace?->name }} · {{ $s->plan?->name }}</span><span class="text-muted">{{ $s->current_period_ends_at?->format('d M Y') }}</span></div></div>@endforeach</div>
            </div>
        @endif
    </div>

    {{-- PAYMENTS --}}
    <div class="row row-cards mb-3">
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header"><h3 class="card-title">{{ __('Payment monitoring') }}</h3><div class="card-actions"><a href="{{ route('admin.manual-payments.index') }}" class="btn btn-sm">{{ __('Verify payments') }}</a></div></div>
                <div class="card-body">
                    <div class="subheader mb-2">{{ __('Pending manual payments (with proof)') }}</div>
                    @forelse($pendingPayments as $p)
                        <div class="d-flex justify-content-between align-items-center border rounded px-2 py-1 mb-1">
                            <span class="small">{{ $p->workspace?->name }} · {{ $p->plan?->name }} <span class="text-muted">({{ $p->currency }} {{ $p->billing_period }})</span></span>
                            <span class="btn-list">
                                <form method="POST" action="{{ route('admin.manual-payments.approve', $p) }}">@csrf<button class="btn btn-sm btn-success">{{ __('Approve') }}</button></form>
                                <form method="POST" action="{{ route('admin.manual-payments.reject', $p) }}">@csrf<button class="btn btn-sm" onclick="return confirm('Reject?')">{{ __('Reject') }}</button></form>
                            </span>
                        </div>
                    @empty
                        <p class="text-muted small mb-3">{{ __('No pending proofs. All clear.') }}</p>
                    @endforelse
                    <div class="subheader mb-1 mt-2">{{ __('Recent payment activity') }}</div>
                    <ul class="list-unstyled small mb-0">@forelse($recentInvoices as $inv)<li class="d-flex justify-content-between border-bottom py-1"><span>{{ $inv->reference_no }} · {{ $inv->workspace?->name }}</span><span class="{{ $inv->status === 'paid' ? 'text-success' : 'text-muted' }}">{{ $inv->currency }} {{ number_format($inv->amount, 0) }} · {{ $inv->status }}</span></li>@empty<li class="text-muted">{{ __('No invoices yet.') }}</li>@endforelse</ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100"><div class="card-header"><h3 class="card-title">{{ __('Activity center') }}</h3><div class="card-actions"><a href="{{ route('admin.audit-logs') }}" class="btn btn-sm">{{ __('Audit logs') }}</a></div></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6"><div class="subheader mb-1">{{ __('New user registrations') }}</div><ul class="list-unstyled small mb-0">@forelse($newUsers as $u)<li class="border-bottom py-1">{{ $u->name }}<br><span class="text-muted">{{ $u->email }} · {{ $u->created_at?->diffForHumans() }}</span></li>@empty<li class="text-muted">{{ __('None yet.') }}</li>@endforelse</ul></div>
                        <div class="col-sm-6"><div class="subheader mb-1">{{ __('New workspaces') }}</div><ul class="list-unstyled small mb-0">@forelse($newWorkspaces as $w)<li class="border-bottom py-1">{{ $w->name }}<br><span class="text-muted">{{ $w->created_at?->diffForHumans() }}</span></li>@empty<li class="text-muted">{{ __('None yet.') }}</li>@endforelse</ul></div>
                    </div>
                    <div class="subheader mt-3 mb-1">{{ __('Administrative actions (workspace audit)') }}</div>
                    <ul class="list-unstyled small mb-0">@forelse($recentAudit as $a)<li class="border-bottom py-1 d-flex justify-content-between"><span>{{ $a->action }} — {{ \Illuminate\Support\Str::limit($a->description, 60) }}</span><span class="text-muted">{{ $a->created_at?->diffForHumans() }}</span></li>@empty<li class="text-muted">{{ __('No audit entries yet.') }}</li>@endforelse</ul>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('Quick actions') }}</h3></div>
        <div class="card-body"><div class="btn-list flex-wrap">
            <a href="{{ route('admin.plans.create') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Create plan') }}</a>
            <a href="{{ route('admin.coupons.index') }}" class="btn"><x-ti name="percentage" /> {{ __('Create coupon') }}</a>
            <a href="{{ route('admin.manual-payments.index') }}" class="btn">{{ __('View pending payments') }}</a>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn">{{ __('Manage subscriptions') }}</a>
            <a href="{{ route('admin.users.index') }}" class="btn">{{ __('Manage users') }}</a>
            <a href="{{ route('admin.workspaces.index') }}" class="btn">{{ __('Manage workspaces') }}</a>
        </div></div>
    </div>
</x-tabler.admin-layout>
@push('scripts')
<script>
(function () {
    var btns = document.querySelectorAll('[data-sub-filter]');
    var rows = document.querySelectorAll('#recent-subs-table tbody tr[data-status]');
    btns.forEach(function (b) {
        b.addEventListener('click', function () {
            btns.forEach(function (x) { x.classList.remove('active'); });
            b.classList.add('active');
            var f = b.getAttribute('data-sub-filter');
            rows.forEach(function (r) {
                var s = (r.getAttribute('data-status') || '').toLowerCase();
                var show = f === 'all' || (f === 'expired' ? (s === 'expired' || s === 'cancelled') : s === f);
                r.style.display = show ? '' : 'none';
            });
        });
    });
})();
</script>
@endpush

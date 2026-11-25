<x-tabler.admin-layout :title="__('Analytics')">
<x-slot name="actions"><a href="{{ route('admin.dashboard') }}" class="btn">{{ __('Back to dashboard') }}</a></x-slot>
<div class="row row-cards mb-3">
<div class="col-lg-8"><div class="card"><div class="card-header"><h3 class="card-title">{{ __('Monthly growth (12 months)') }}</h3></div>
<div class="card-body"><div class="table-responsive"><table class="table table-vcenter">
<thead><tr><th>{{ __('Month') }}</th><th class="text-end">{{ __('New users') }}</th><th class="text-end">{{ __('New workspaces') }}</th><th class="text-end">{{ __('MRR IDR') }}</th><th class="text-end">{{ __('MRR USD') }}</th></tr></thead>
<tbody>@foreach($months as $m)<tr><td class="fw-semibold">{{ $m['label'] }}</td><td class="text-end">{{ $m['users'] }}</td><td class="text-end">{{ $m['workspaces'] }}</td><td class="text-end">Rp{{ number_format($m['mrr_idr'],0,',','.') }}</td><td class="text-end">${{ number_format($m['mrr_usd'],2) }}</td></tr>@endforeach</tbody>
</table></div></div></div></div>
<div class="col-lg-4"><div class="card h-100"><div class="card-header"><h3 class="card-title">{{ __('Plan distribution') }}</h3></div>
<div class="card-body"><ul class="list-unstyled mb-0">@foreach($planDistribution as $p)<li class="d-flex justify-content-between border-bottom py-1"><span>{{ $p->name }}</span><strong>{{ $p->subscriptions_count }}</strong></li>@endforeach</ul>
<div class="subheader mt-3 mb-1">{{ __('Revenue by currency') }}</div>
<ul class="list-unstyled small mb-0">@forelse($revenueByCurrency as $r)<li class="d-flex justify-content-between"><span>{{ $r->currency }} ({{ $r->cnt }}×)</span><strong>{{ $r->currency==='IDR' ? 'Rp'.number_format($r->total,0,',','.') : '$'.number_format($r->total,2) }}</strong></li>@empty<li class="text-muted">{{ __('No revenue yet.') }}</li>@endforelse</ul>
<div class="subheader mt-3 mb-1">{{ __('Status breakdown') }}</div>
<ul class="list-unstyled small mb-0">@foreach($statusBreakdown as $s)<li class="d-flex justify-content-between"><span>{{ $s->status }}</span><strong>{{ $s->cnt }}</strong></li>@endforeach</ul>
</div></div></div>
</div>
</x-tabler.admin-layout>

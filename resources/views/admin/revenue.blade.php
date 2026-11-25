<x-tabler.admin-layout :title="__('Revenue Reports')">
<x-slot name="actions"><a href="{{ route('admin.dashboard') }}" class="btn">{{ __('Back to dashboard') }}</a></x-slot>
<div class="card mb-3"><div class="card-header"><h3 class="card-title">{{ __('Monthly revenue (12 months, live)') }}</h3></div>
<div class="card-body"><div class="table-responsive"><table class="table table-vcenter">
<thead><tr><th>{{ __('Month') }}</th><th class="text-end">{{ __('IDR') }}</th><th class="text-end">{{ __('USD') }}</th><th class="text-end">{{ __('Invoices') }}</th></tr></thead>
<tbody>@foreach($byMonth as $m)<tr><td class="fw-semibold">{{ $m['label'] }}</td><td class="text-end">Rp{{ number_format($m['idr'],0,',','.') }}</td><td class="text-end">${{ number_format($m['usd'],2) }}</td><td class="text-end">{{ $m['count'] }}</td></tr>@endforeach</tbody>
</table></div></div></div>
<div class="row row-cards">
<div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">{{ __('Pending subscriptions') }}</h3></div>
<div class="table-responsive"><table class="table table-vcenter card-table"><thead><tr><th>{{ __('Workspace') }}</th><th>{{ __('Plan') }}</th><th>{{ __('Updated') }}</th></tr></thead>
<tbody>@forelse($pending as $s)<tr><td>{{ $s->workspace?->name }}</td><td class="text-muted">{{ $s->plan?->name }}</td><td class="text-muted">{{ $s->updated_at?->diffForHumans() }}</td></tr>@empty<tr><td colspan="3" class="text-muted">{{ __('No pending.') }}</td></tr>@endforelse</tbody></table></div>
<div class="card-footer">{{ $pending->links() }}</div></div></div>
<div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">{{ __('Subscription invoices') }}</h3></div>
<div class="table-responsive"><table class="table table-vcenter card-table"><thead><tr><th>{{ __('Ref') }}</th><th>{{ __('Workspace') }}</th><th class="text-end">{{ __('Amount') }}</th><th>{{ __('Status') }}</th></tr></thead>
<tbody>@forelse($invoices as $inv)<tr><td class="small">{{ $inv->reference_no }}</td><td>{{ $inv->workspace?->name }}</td><td class="text-end">{{ $inv->currency }} {{ number_format($inv->amount,0) }}</td><td><x-tabler.status-badge :status="$inv->status" /></td></tr>@empty<tr><td colspan="4" class="text-muted">{{ __('No invoices.') }}</td></tr>@endforelse</tbody></table></div>
<div class="card-footer">{{ $invoices->links() }}</div></div></div>
</div>
</x-tabler.admin-layout>

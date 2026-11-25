<x-tabler.admin-layout :title="__('Audit Logs')">
<x-slot name="actions"><form method="GET" class="d-flex gap-2"><input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search action…') }}" aria-label="{{ __('Search audit logs') }}"><button class="btn btn-primary">{{ __('Search') }}</button></form></x-slot>
<div class="card"><div class="table-responsive"><table class="table table-vcenter card-table">
<thead><tr><th>{{ __('When') }}</th><th>{{ __('Action') }}</th><th>{{ __('Description') }}</th><th>{{ __('User') }}</th><th>{{ __('Workspace') }}</th></tr></thead>
<tbody>@forelse($logs as $l)<tr><td class="text-muted small">{{ $l->created_at?->format('d M Y H:i') }}</td><td class="fw-semibold">{{ $l->action }}</td><td class="text-muted small">{{ \Illuminate\Support\Str::limit($l->description,80) }}</td><td class="small">{{ $l->user?->email ?? '—' }}</td><td class="small">{{ $l->workspace?->name ?? '—' }}</td></tr>@empty<tr><td colspan="5"><x-tabler.empty icon="scroll" :title="__('No audit entries')" :body="__('Workspace actions will appear here.')" /></td></tr>@endforelse</tbody>
</table></div>@if($logs->hasPages())<div class="card-footer">{{ $logs->links() }}</div>@endif</div>
</x-tabler.admin-layout>

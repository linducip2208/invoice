<x-tabler.admin-layout :title="$plan->name" :pretitle="__('Plan detail')">
    <x-slot name="actions">
        <a href="{{ route('admin.plans.index') }}" class="btn">{{ __('Semua Plan') }}</a>
        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-outline-warning">{{ __('Edit') }}</a>
    </x-slot>

    <div class="row g-3 mb-3">
        <div class="col-lg-5">
            <div class="card"><div class="card-body">
                <dl class="row g-2 mb-0">
                    <dt class="col-5 text-muted small">Slug</dt><dd class="col-7"><code>{{ $plan->slug }}</code></dd>
                    <dt class="col-5 text-muted small">Tagline</dt><dd class="col-7">{{ $plan->tagline }}</dd>
                    <dt class="col-5 text-muted small">IDR/bln</dt><dd class="col-7">Rp{{ number_format($plan->price_idr_monthly, 0, ',', '.') }}</dd>
                    <dt class="col-5 text-muted small">IDR/thn</dt><dd class="col-7">Rp{{ number_format($plan->price_idr_yearly, 0, ',', '.') }}</dd>
                    <dt class="col-5 text-muted small">USD/bln</dt><dd class="col-7">${{ number_format($plan->price_usd_monthly, 2) }}</dd>
                    <dt class="col-5 text-muted small">USD/thn</dt><dd class="col-7">${{ number_format($plan->price_usd_yearly, 2) }}</dd>
                    <dt class="col-5 text-muted small">Publik</dt><dd class="col-7">{{ $plan->is_public ? __('Ya') : __('Tidak') }}</dd>
                    <dt class="col-5 text-muted small">Default</dt><dd class="col-7">{{ $plan->is_default ? __('Ya') : __('Tidak') }}</dd>
                </dl>
                @if($plan->description)<p class="text-muted small mt-2 mb-0">{{ $plan->description }}</p>@endif
            </div></div>
        </div>
        <div class="col-lg-7">
            <div class="card"><div class="card-header"><h3 class="card-title">{{ __('Subscriptions') }} ({{ $subscriptions->total() }})</h3></div>
                <div class="table-responsive"><table class="table table-vcenter card-table">
                    <thead><tr><th>{{ __('Workspace') }}</th><th>{{ __('Status') }}</th><th>{{ __('Periode') }}</th></tr></thead>
                    <tbody>
                        @forelse($subscriptions as $s)
                            <tr>
                                <td><a href="{{ route('admin.workspaces.show', $s->workspace) }}">{{ $s->workspace?->name ?? '—' }}</a></td>
                                <td><x-tabler.status-badge :status="$s->status" /></td>
                                <td class="text-muted">{{ ucfirst($s->billing_period) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">{{ __('Belum ada subscription.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
                @if($subscriptions->hasPages())<div class="card-footer">{{ $subscriptions->links() }}</div>@endif
            </div>
        </div>
    </div>
</x-tabler.admin-layout>

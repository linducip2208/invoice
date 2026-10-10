<x-tabler.layout :title="__('Dashboard')" :pretitle="__('Superadmin')">
    <div class="row row-cards mb-3">
        <x-tabler.stat :label="__('Total Users')" :value="number_format($totalUsers)" icon="users" />
        <x-tabler.stat :label="__('Total Workspaces')" :value="number_format($totalWorkspaces)" icon="building" />
        <x-tabler.stat :label="__('Active Paid Subs')" :value="number_format($activeSubs)" icon="credit-card" />
        <x-tabler.stat :label="__('Free Workspaces')" :value="number_format($freeWorkspaces)" icon="wallet" />
    </div>

    <div class="row row-cards mb-3">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">{{ __('MRR This Month (IDR)') }}</div>
                    <div class="h1 mb-0">Rp{{ number_format($mrrIdr, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">{{ __('MRR This Month (USD)') }}</div>
                    <div class="h1 mb-0">${{ number_format($mrrUsd, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Recent Subscriptions') }}</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Workspace') }}</th>
                        <th>{{ __('Plan') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Period') }}</th>
                        <th>{{ __('Next Billing') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubs as $sub)
                        <tr>
                            <td class="fw-semibold">{{ $sub->workspace?->name ?? '—' }}</td>
                            <td class="text-muted">{{ $sub->plan?->name ?? '—' }}</td>
                            <td><x-tabler.status-badge :status="$sub->status" /></td>
                            <td class="text-muted">{{ ucfirst($sub->billing_period) }}</td>
                            <td class="text-muted">{{ $sub->current_period_ends_at?->format('d M Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-tabler.empty icon="repeat" :title="__('No subscriptions yet')" :body="__('New subscriptions will appear here.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-tabler.layout>

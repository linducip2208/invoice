<x-admin-layout title="Dashboard">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Total Users', 'value' => $totalUsers, 'icon' => '👥'],
                ['label' => 'Total Workspaces', 'value' => $totalWorkspaces, 'icon' => '🏢'],
                ['label' => 'Active Paid Subs', 'value' => $activeSubs, 'icon' => '💳'],
                ['label' => 'Free Workspaces', 'value' => $freeWorkspaces, 'icon' => '🆓'],
            ];
        @endphp
        @foreach($stats as $s)
            <div class="card-unicorn">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">{{ $s['icon'] }}</span>
                    <div>
                        <p class="text-xs text-purple-400 font-bold uppercase">{{ $s['label'] }}</p>
                        <p class="text-2xl font-display font-bold text-purple-900">{{ number_format($s['value']) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="card-unicorn">
            <p class="text-xs text-purple-400 font-bold uppercase">MRR This Month (IDR)</p>
            <p class="text-3xl font-display font-bold text-purple-900 mt-2">Rp{{ number_format($mrrIdr, 0, ',', '.') }}</p>
        </div>
        <div class="card-unicorn">
            <p class="text-xs text-purple-400 font-bold uppercase">MRR This Month (USD)</p>
            <p class="text-3xl font-display font-bold text-purple-900 mt-2">${{ number_format($mrrUsd, 2) }}</p>
        </div>
    </div>

    <div class="mt-6 card-unicorn">
        <h2 class="font-display font-bold text-lg text-purple-900 mb-4">✦ Recent Subscriptions</h2>
        <table class="table-unicorn">
            <thead>
                <tr><th>Workspace</th><th>Plan</th><th>Status</th><th>Period</th><th>Next Billing</th></tr>
            </thead>
            <tbody>
                @forelse($recentSubs as $sub)
                    <tr>
                        <td class="font-bold text-purple-900">{{ $sub->workspace?->name ?? '—' }}</td>
                        <td>{{ $sub->plan?->name ?? '—' }}</td>
                        <td><span class="badge-{{ $sub->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($sub->status) }}</span></td>
                        <td class="text-purple-600">{{ ucfirst($sub->billing_period) }}</td>
                        <td class="text-purple-500">{{ $sub->current_period_ends_at?->format('d M Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-purple-400 py-8">No subscriptions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

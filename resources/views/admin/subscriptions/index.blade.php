<x-admin-layout title="Subscriptions">
    <form method="GET" class="mb-4 flex gap-2 flex-wrap">
        <select name="status" class="input-unicorn rounded-xl px-4 py-2">
            <option value="">All status</option>
            @foreach(['trial','active','past_due','cancelled','expired'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="plan" class="input-unicorn rounded-xl px-4 py-2">
            <option value="">All plans</option>
            @foreach($plans as $p)
                <option value="{{ $p->slug }}" {{ request('plan')===$p->slug?'selected':'' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <button class="btn-unicorn">Filter</button>
    </form>

    <div class="card-unicorn">
        <table class="table-unicorn">
            <thead><tr><th>Workspace</th><th>Plan</th><th>Status</th><th>Period</th><th>Currency</th><th>Next billing</th><th></th></tr></thead>
            <tbody>
                @foreach($subscriptions as $sub)
                    <tr>
                        <td><a href="{{ route('admin.workspaces.show', $sub->workspace) }}" class="font-bold text-purple-700 hover:text-pink-500">{{ $sub->workspace?->name }}</a></td>
                        <td>{{ $sub->plan?->name }}</td>
                        <td><span class="badge-{{ $sub->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($sub->status) }}</span></td>
                        <td class="capitalize">{{ $sub->billing_period }}</td>
                        <td>{{ $sub->currency }}</td>
                        <td class="text-purple-500 text-sm">{{ $sub->current_period_ends_at?->format('d M Y') ?? '—' }}</td>
                        <td><a href="{{ route('admin.subscriptions.show', $sub) }}" class="text-purple-600 hover:text-pink-500 font-semibold text-sm">Manage</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $subscriptions->links() }}</div>
    </div>
</x-admin-layout>

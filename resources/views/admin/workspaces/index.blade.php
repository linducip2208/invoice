<x-admin-layout title="Workspaces">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="input-unicorn rounded-xl px-4 py-2 flex-1">
        <button class="btn-unicorn">Search</button>
    </form>
    <div class="card-unicorn">
        <table class="table-unicorn">
            <thead><tr><th>Name</th><th>Plan</th><th>Status</th><th>Invoices</th><th>Clients</th><th>Users</th><th></th></tr></thead>
            <tbody>
                @foreach($workspaces as $ws)
                    <tr>
                        <td class="font-bold">{{ $ws->name }}</td>
                        <td>{{ $ws->subscription?->plan?->name ?? 'Free' }}</td>
                        <td>{{ $ws->subscription?->status ?? '—' }}</td>
                        <td>{{ $ws->invoices_count }}</td>
                        <td>{{ $ws->clients_count }}</td>
                        <td>{{ $ws->users_count }}</td>
                        <td><a href="{{ route('admin.workspaces.show', $ws) }}" class="text-purple-600 hover:text-pink-500 font-semibold text-sm">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $workspaces->links() }}</div>
    </div>
</x-admin-layout>

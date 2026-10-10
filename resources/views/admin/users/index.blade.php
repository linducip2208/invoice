<x-admin-layout title="Users">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="input-unicorn rounded-xl px-4 py-2 flex-1">
        <button class="btn-unicorn">Search</button>
    </form>
    <div class="card-unicorn">
        <table class="table-unicorn">
            <thead><tr><th>Name</th><th>Email</th><th>Workspaces</th><th>Role</th><th>Joined</th><th></th></tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td class="font-bold">{{ $u->name }}</td>
                        <td class="text-purple-600">{{ $u->email }}</td>
                        <td>{{ $u->workspaces_count }}</td>
                        <td>@if($u->is_superadmin)<span class="badge-warning">Superadmin</span>@else<span class="text-purple-400">User</span>@endif</td>
                        <td class="text-purple-500 text-sm">{{ $u->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $u) }}" class="text-purple-600 hover:text-pink-500 font-semibold text-sm">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-admin-layout>

<x-admin-layout title="{{ $user->name }}">
    <div class="card-unicorn">
        <h3 class="font-display font-bold text-xl">{{ $user->name }} <span class="text-sm text-purple-500">{{ $user->email }}</span></h3>
        <p class="text-sm text-purple-500 mt-1">Joined: {{ $user->created_at->format('d M Y H:i') }}</p>
        <p class="text-sm mt-1">Superadmin: <strong>{{ $user->is_superadmin ? 'Yes' : 'No' }}</strong></p>
    </div>

    <div class="mt-6 card-unicorn">
        <h4 class="font-display font-bold text-lg mb-3">Workspaces ({{ $user->workspaces->count() }})</h4>
        <table class="table-unicorn">
            <thead><tr><th>Name</th><th>Plan</th><th>Status</th><th>Role</th></tr></thead>
            <tbody>
                @foreach($user->workspaces as $ws)
                    <tr>
                        <td><a href="{{ route('admin.workspaces.show', $ws) }}" class="font-bold text-purple-700 hover:text-pink-500">{{ $ws->name }}</a></td>
                        <td>{{ $ws->subscription?->plan?->name ?? 'Free' }}</td>
                        <td>{{ $ws->subscription?->status ?? '—' }}</td>
                        <td>{{ $ws->pivot->role }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>

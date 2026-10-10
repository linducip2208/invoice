<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">📜 {{ __('Audit Log') }}</h2></x-slot>
    <div class="max-w-6xl mx-auto card-unicorn">
        <table class="table-unicorn">
            <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-purple-500 text-sm">{{ $log->created_at->diffForHumans() }}</td>
                    <td class="font-semibold">{{ $log->user?->name ?? 'System' }}</td>
                    <td><span class="badge-unicorn">{{ $log->action }}</span></td>
                    <td>{{ $log->description }}</td>
                    <td class="font-mono text-xs">{{ $log->ip }}</td>
                </tr>
                @empty<tr><td colspan="5" class="text-center text-purple-400 py-8">Belum ada aktivitas tercatat.</td></tr>@endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-app-layout>

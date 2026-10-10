<x-tabler.layout :title="__('Audit Log')" :pretitle="__('Workspace')">
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Time') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('IP') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-muted">{{ $log->created_at->diffForHumans() }}</td>
                            <td class="fw-semibold">{{ $log->user?->name ?? 'System' }}</td>
                            <td><span class="badge bg-blue-lt">{{ $log->action }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td class="font-monospace small">{{ $log->ip }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-tabler.empty icon="history" :title="__('Belum ada aktivitas tercatat.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $logs->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

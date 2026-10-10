<x-tabler.layout :title="$user->name" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <a href="{{ route('admin.users.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Users') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <span class="avatar avatar-md">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                <div>
                    <h3 class="card-title mb-1">{{ $user->name }}
                        @if($user->is_superadmin)
                            <span class="badge bg-yellow-lt ms-1">{{ __('Superadmin') }}</span>
                        @endif
                    </h3>
                    <div class="text-muted">{{ $user->email }}</div>
                    <div class="text-muted small mt-1">{{ __('Joined') }}: {{ $user->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Workspaces') }} ({{ $user->workspaces->count() }})</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Plan') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Role') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->workspaces as $ws)
                        <tr>
                            <td>
                                <a href="{{ route('admin.workspaces.show', $ws) }}" class="fw-semibold">{{ $ws->name }}</a>
                            </td>
                            <td class="text-muted">{{ $ws->subscription?->plan?->name ?? __('Free') }}</td>
                            <td>
                                @if($ws->subscription?->status)
                                    <x-tabler.status-badge :status="$ws->subscription->status" />
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="badge bg-blue-lt">{{ $ws->pivot->role }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-tabler.empty icon="building" :title="__('No workspaces')" :body="__('This user has no workspaces yet.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-tabler.layout>

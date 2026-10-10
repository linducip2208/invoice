<x-tabler.layout :title="__('Workspaces')" :pretitle="__('Superadmin')">
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.workspaces.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-9">
                        <x-tabler.field :label="__('Search')" name="search">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Name or email…') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-list justify-content-end">
                            @if(request()->filled('search'))
                                <a href="{{ route('admin.workspaces.index') }}" class="btn">{{ __('Reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <x-ti name="search" /> {{ __('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Plan') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Invoices') }}</th>
                        <th class="text-center">{{ __('Clients') }}</th>
                        <th class="text-center">{{ __('Users') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workspaces as $ws)
                        <tr>
                            <td class="fw-semibold">{{ $ws->name }}</td>
                            <td class="text-muted">{{ $ws->subscription?->plan?->name ?? __('Free') }}</td>
                            <td>
                                @if($ws->subscription?->status)
                                    <x-tabler.status-badge :status="$ws->subscription->status" />
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ $ws->invoices_count }}</span></td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ $ws->clients_count }}</span></td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ $ws->users_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.workspaces.show', $ws) }}" class="btn btn-sm">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-tabler.empty icon="building" :title="__('No workspaces found')"
                                    :body="__('Try a different search keyword.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($workspaces->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $workspaces->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

<x-tabler.layout :title="__('Users')" :pretitle="__('Superadmin')">
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
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
                                <a href="{{ route('admin.users.index') }}" class="btn">{{ __('Reset') }}</a>
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
                        <th>{{ __('Email') }}</th>
                        <th class="text-center">{{ __('Workspaces') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Joined') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-sm">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                                    <span class="fw-semibold">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $u->email }}</td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ $u->workspaces_count }}</span></td>
                            <td>
                                @if($u->is_superadmin)
                                    <span class="badge bg-yellow-lt">{{ __('Superadmin') }}</span>
                                @else
                                    <span class="text-muted">{{ __('User') }}</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $u->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-tabler.empty icon="users" :title="__('No users found')"
                                    :body="__('Try a different search keyword.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $users->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

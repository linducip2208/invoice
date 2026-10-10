<x-tabler.layout :title="__('Team Members')" :pretitle="__('Workspace')">
    @php $maxUsers = $workspace->planFeature('max_users'); @endphp

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Invite Teammate') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('team.invite') }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Email')" name="email">
                            <input type="email" id="email" name="email" placeholder="email@example.com" required class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Role')" name="role">
                            <select id="role" name="role" class="form-select">
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100">
                            <x-ti name="plus" /> {{ __('Send Invite') }}
                        </button>
                    </div>
                </div>
            </form>
            <p class="form-hint mt-2">{{ __('Plan limit:') }} {{ $maxUsers ?? '∞' }} · {{ __('Currently:') }} {{ $members->count() + $pending->count() }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Active Members') }} ({{ $members->count() }})</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $m)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-sm">{{ strtoupper(mb_substr($m->name, 0, 1)) }}</span>
                                    <span class="fw-semibold">{{ $m->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $m->email }}</td>
                            <td><span class="badge bg-blue-lt">{{ $m->pivot->role }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <x-tabler.empty icon="users" :title="__('No members yet')" :body="__('Invite your first teammate.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pending->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Pending Invites') }} ({{ $pending->count() }})</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Expires') }}</th>
                            <th>{{ __('Link') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $inv)
                            <tr>
                                <td>{{ $inv->email }}</td>
                                <td><span class="badge bg-yellow-lt">{{ $inv->role }}</span></td>
                                <td class="text-muted">{{ $inv->expires_at?->diffForHumans() }}</td>
                                <td>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ url('/team/accept/' . $inv->token) }}'); this.textContent='{{ __('Copied') }}'" class="btn btn-sm">
                                        <x-ti name="link" /> {{ __('Copy Link') }}
                                    </button>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('team.invite.revoke', $inv) }}" class="d-inline" onsubmit="return confirm('Revoke?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('Revoke') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-tabler.layout>

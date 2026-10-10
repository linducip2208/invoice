<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">👥 {{ __('Team Members') }}</h2></x-slot>
    <div class="max-w-4xl mx-auto space-y-4">
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-3">+ {{ __('Invite Teammate') }}</h3>
            <form method="POST" action="{{ route('team.invite') }}" class="flex gap-2 flex-wrap">
                @csrf
                <input type="email" name="email" placeholder="email@example.com" required class="input-unicorn rounded-xl px-4 py-2 flex-1">
                <select name="role" class="input-unicorn rounded-xl px-3 py-2"><option value="member">Member</option><option value="admin">Admin</option></select>
                <button class="btn-unicorn">Send Invite</button>
            </form>
            @php $maxUsers = $workspace->planFeature('max_users'); @endphp
            <p class="text-xs text-purple-500 mt-2">Plan limit: {{ $maxUsers ?? '∞' }} users · Currently: {{ $members->count() + $pending->count() }}</p>
        </div>

        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-3">Active Members ({{ $members->count() }})</h3>
            <table class="table-unicorn">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                <tbody>
                    @foreach($members as $m)
                    <tr><td class="font-bold">{{ $m->name }}</td><td>{{ $m->email }}</td><td><span class="badge-unicorn">{{ $m->pivot->role }}</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($pending->isNotEmpty())
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-3">Pending Invites ({{ $pending->count() }})</h3>
            <table class="table-unicorn">
                <thead><tr><th>Email</th><th>Role</th><th>Expires</th><th>Link</th><th></th></tr></thead>
                <tbody>
                    @foreach($pending as $inv)
                    <tr>
                        <td>{{ $inv->email }}</td>
                        <td>{{ $inv->role }}</td>
                        <td class="text-purple-500 text-sm">{{ $inv->expires_at?->diffForHumans() }}</td>
                        <td><button onclick="navigator.clipboard.writeText('{{ url('/team/accept/' . $inv->token) }}'); this.textContent='✓'" class="text-purple-600 text-sm">Copy Link</button></td>
                        <td><form method="POST" action="{{ route('team.invite.revoke', $inv) }}" onsubmit="return confirm('Revoke?');">@csrf @method('DELETE')<button class="text-rose-500 text-sm">×</button></form></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🔌 {{ __('API Tokens') }}</h2></x-slot>
    <div class="max-w-4xl mx-auto space-y-4">
        @if($newToken)
        <div class="card-unicorn bg-emerald-50 border-emerald-200">
            <p class="text-sm font-semibold text-emerald-800 mb-2">⚠️ Token baru — copy SEKARANG, tidak akan ditampilkan lagi:</p>
            <code class="block bg-white p-3 rounded-lg font-mono text-sm break-all">{{ $newToken }}</code>
        </div>
        @endif

        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-3">+ {{ __('Buat Token Baru') }}</h3>
            <form method="POST" action="{{ route('api-tokens.store') }}" class="flex gap-3">
                @csrf
                <input type="text" name="name" placeholder="Nama token (mis: Mobile App, n8n)" required class="input-unicorn rounded-xl px-4 py-2 flex-1">
                <button class="btn-unicorn">Generate</button>
            </form>
        </div>

        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-3">Existing Tokens</h3>
            <table class="table-unicorn">
                <thead><tr><th>Name</th><th>Prefix</th><th>Last Used</th><th>Created</th><th></th></tr></thead>
                <tbody>
                    @forelse($tokens as $t)
                    <tr>
                        <td class="font-bold">{{ $t->name }}</td>
                        <td class="font-mono text-xs">{{ $t->prefix }}_***</td>
                        <td class="text-purple-500 text-sm">{{ $t->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="text-purple-500 text-sm">{{ $t->created_at->diffForHumans() }}</td>
                        <td><form method="POST" action="{{ route('api-tokens.destroy', $t) }}" onsubmit="return confirm('Revoke?');">@csrf @method('DELETE')<button class="text-rose-500 text-sm">Revoke</button></form></td>
                    </tr>
                    @empty<tr><td colspan="5" class="text-center text-purple-400 py-8">Belum ada token.</td></tr>@endforelse
                </tbody>
            </table>
        </div>

        <div class="card-unicorn text-sm text-purple-600">
            <p class="font-bold">Cara pakai:</p>
            <code class="block bg-purple-50 p-2 rounded mt-2 font-mono text-xs">curl -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/v1/invoices') }}</code>
            <p class="mt-2 text-xs text-purple-400">REST API endpoint coming. Token format: <code>inv_xxxxxx_yyy...</code></p>
        </div>
    </div>
</x-app-layout>

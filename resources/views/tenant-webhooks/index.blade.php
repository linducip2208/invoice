<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🪝 {{ __('Outbound Webhooks') }}</h2></x-slot>
    <div class="max-w-4xl mx-auto space-y-4">
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-3">+ {{ __('Tambah Webhook') }}</h3>
            <form method="POST" action="{{ route('tenant-webhooks.store') }}" class="space-y-3">
                @csrf
                <input type="url" name="url" placeholder="https://yourapp.com/webhook" required class="input-unicorn rounded-xl px-4 py-2 w-full">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm">
                    @foreach(['invoice.created','invoice.sent','invoice.paid','invoice.viewed','payment.received','client.created'] as $ev)
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-purple-50">
                        <input type="checkbox" name="events[]" value="{{ $ev }}" class="rounded border-purple-300">
                        <span class="font-mono text-xs">{{ $ev }}</span>
                    </label>
                    @endforeach
                </div>
                <button class="btn-unicorn">Add Webhook</button>
            </form>
        </div>

        <div class="card-unicorn">
            <table class="table-unicorn">
                <thead><tr><th>URL</th><th>Events</th><th>Status</th><th>Last Fire</th><th></th></tr></thead>
                <tbody>
                    @forelse($webhooks as $w)
                    <tr>
                        <td class="font-mono text-xs truncate max-w-xs">{{ $w->url }}</td>
                        <td>{{ count($w->events ?? []) }}</td>
                        <td><span class="badge-{{ $w->is_active ? 'success' : 'warning' }}">{{ $w->is_active ? 'Active' : 'Paused' }}</span></td>
                        <td class="text-purple-500 text-sm">{{ $w->last_fired_at?->diffForHumans() ?? 'Never' }}</td>
                        <td>
                            <form method="POST" action="{{ route('tenant-webhooks.toggle', $w) }}" class="inline">@csrf<button class="text-purple-600 text-sm">{{ $w->is_active ? 'Pause' : 'Resume' }}</button></form>
                            <form method="POST" action="{{ route('tenant-webhooks.destroy', $w) }}" class="inline" onsubmit="return confirm('Hapus?');">@csrf @method('DELETE')<button class="text-rose-500 text-sm ml-2">×</button></form>
                        </td>
                    </tr>
                    @empty<tr><td colspan="5" class="text-center text-purple-400 py-8">Belum ada webhook.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

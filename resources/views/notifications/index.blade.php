<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="heading-unicorn">🔔 {{ __('Notifications') }}</h2>
            <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm('Hapus semua?');">@csrf @method('DELETE')<button class="btn-unicorn-secondary text-sm">Clear all</button></form>
        </div>
    </x-slot>
    <div class="max-w-3xl mx-auto space-y-2">
        @forelse($notifications as $n)
        <div class="card-unicorn flex items-start gap-3 {{ $n->read_at ? 'opacity-60' : '' }}">
            <span class="text-2xl">{{ $n->icon }}</span>
            <div class="flex-1">
                <p class="font-bold text-purple-900">{{ $n->title }}</p>
                <p class="text-sm text-purple-600">{{ $n->message }}</p>
                <p class="text-xs text-purple-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                @if($n->action_url)<a href="{{ $n->action_url }}" class="text-pink-600 text-sm font-semibold">Lihat →</a>@endif
            </div>
        </div>
        @empty
        <div class="card-unicorn text-center py-12 text-purple-400">🔕 Belum ada notifikasi.</div>
        @endforelse
        <div>{{ $notifications->links() }}</div>
    </div>
</x-app-layout>

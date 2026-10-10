<x-tabler.layout :title="__('Notifications')" :pretitle="__('Overview')">
    <x-slot name="actions">
        <form method="POST" action="{{ route('notifications.clear') }}" class="d-inline" onsubmit="return confirm('Hapus semua?');">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><x-ti name="trash" /> {{ __('Clear all') }}</button>
        </form>
    </x-slot>

    @forelse($notifications as $n)
        <div class="card mb-2 {{ $n->read_at ? 'opacity-50' : '' }}">
            <div class="card-body d-flex gap-3">
                <span class="avatar"><x-ti name="bell" /></span>
                <div class="flex-fill">
                    <div class="fw-semibold">{{ $n->title }}</div>
                    <div class="text-muted">{{ $n->message }}</div>
                    <div class="text-muted small mt-1">{{ $n->created_at->diffForHumans() }}</div>
                    @if($n->action_url)
                        <a href="{{ $n->action_url }}" class="btn btn-sm mt-2">{{ __('Lihat') }}</a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="bell" :title="__('Belum ada notifikasi.')" />
            </div>
        </div>
    @endforelse

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-3">{{ $notifications->links() }}</div>
    @endif
</x-tabler.layout>

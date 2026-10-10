<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">📰 {{ __('Activity Feed') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <p class="text-sm text-purple-500 mb-4">{{ __('Timeline 50 event terakhir di workspace.') }}</p>
        @forelse($logs as $log)
        <div class="flex gap-3 py-3 border-b border-purple-100 last:border-0">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-200 to-purple-200 flex items-center justify-center flex-shrink-0 text-sm">
                {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-sm text-purple-700"><strong>{{ $log->user?->name ?? 'System' }}</strong> · <span class="badge-unicorn text-xs">{{ $log->action }}</span></p>
                <p class="text-sm text-purple-600">{{ $log->description }}</p>
                <p class="text-xs text-purple-400">{{ $log->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <p class="text-center text-purple-400 py-8">Belum ada aktivitas.</p>
        @endforelse
    </div>
</x-app-layout>

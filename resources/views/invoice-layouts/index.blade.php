<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Invoice Layouts') }} <span class="text-2xl">🎨</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Drag-drop builder untuk template invoice kamu') }}</p>
            </div>
            <a href="{{ route('invoice-layouts.create') }}" class="btn-unicorn">+ {{ __('Layout Baru') }}</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto">
        @if($layouts->isEmpty())
            <div class="card-unicorn text-center py-16">
                <div class="text-7xl mb-4 inline-block animate-unicorn-float">🎨</div>
                <h3 class="font-display text-xl font-bold text-purple-900">{{ __('Belum ada layout custom') }}</h3>
                <p class="mt-2 text-sm text-purple-500">{{ __('Bikin layout invoice kamu sendiri pakai drag & drop builder.') }}</p>
                <div class="mt-5">
                    <a href="{{ route('invoice-layouts.create') }}" class="btn-unicorn">✦ {{ __('Bikin Layout Pertama') }}</a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($layouts as $l)
                    <div class="card-unicorn">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-display font-bold text-lg text-purple-900">{{ $l->name }}</h3>
                                <p class="text-xs text-purple-500 mt-1">{{ count($l->blocks ?? []) }} blok · {{ $l->updated_at->diffForHumans() }}</p>
                            </div>
                            @if($l->is_default)<span class="badge-unicorn">Default</span>@endif
                        </div>
                        <p class="text-sm text-purple-600 mt-3 line-clamp-2">{{ $l->description ?: '—' }}</p>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('invoice-layouts.edit', $l) }}" class="btn-unicorn text-xs">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('invoice-layouts.destroy', $l) }}" onsubmit="return confirm('Hapus layout?');">
                                @csrf @method('DELETE')
                                <button class="btn-unicorn-danger text-xs">{{ __('Hapus') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">{{ $layouts->links() }}</div>
        @endif
    </div>
</x-app-layout>

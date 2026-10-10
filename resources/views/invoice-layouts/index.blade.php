<x-tabler.layout :title="__('Invoice Layouts')" :pretitle="__('Workspace')">
    <x-slot name="actions">
        <a href="{{ route('invoice-layouts.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Layout Baru') }}
        </a>
    </x-slot>

    <p class="text-muted small mb-3">{{ __('Drag-drop builder untuk template invoice kamu') }}</p>

    @if($layouts->isEmpty())
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="template" :title="__('Belum ada layout custom')" :body="__('Bikin layout invoice kamu sendiri pakai drag & drop builder.')" :actionUrl="route('invoice-layouts.create')" :actionLabel="__('Bikin Layout Pertama')" />
            </div>
        </div>
    @else
        <div class="row row-decks row-cards">
            @foreach($layouts as $l)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-start">
                                <div class="col">
                                    <h3 class="card-title mb-1">{{ $l->name }}</h3>
                                    <div class="text-muted small">{{ count($l->blocks ?? []) }} {{ __('blok') }} · {{ $l->updated_at->diffForHumans() }}</div>
                                </div>
                                <div class="col-auto">
                                    @if($l->is_default)
                                        <span class="badge bg-primary">{{ __('Default') }}</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-muted small mt-2">{{ $l->description ?: '—' }}</p>
                            <div class="btn-list mt-3">
                                <a href="{{ route('invoice-layouts.edit', $l) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                <x-tabler.delete-button :action="route('invoice-layouts.destroy', $l)" :confirm="__('Hapus layout?')" :label="__('Hapus')" />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($layouts->hasPages())
            <div class="card mt-3">
                <div class="card-footer d-flex justify-content-center">{{ $layouts->links() }}</div>
            </div>
        @endif
    @endif
</x-tabler.layout>

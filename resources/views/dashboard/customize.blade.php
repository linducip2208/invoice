<x-tabler.layout :title="__('Customize Dashboard')" :pretitle="__('Dashboard')" :subtitle="__('Atur widget yang muncul di dashboard kamu')">
    <x-slot name="actions">
        <a href="{{ route('dashboard') }}" class="btn">
            <x-ti name="layout-dashboard" /> {{ __('Dashboard') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Tambah Widget') }}</h3></div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($registry as $key => $info)
                    <div class="col-md-6 col-lg-4">
                        <form method="POST" action="{{ route('dashboard.widgets.store') }}" class="d-flex align-items-center gap-2 border rounded p-2 h-100">
                            @csrf
                            <input type="hidden" name="widget_type" value="{{ $key }}">
                            <input type="hidden" name="size" value="{{ $info['sizes'][0] }}">
                            <div class="flex-fill">
                                <div class="fw-semibold small">{{ $info['name'] }}</div>
                                <div class="text-muted small text-truncate">{{ $info['desc'] }}</div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ __('Tambah Widget') }}">
                                <x-ti name="plus" />
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Widget Aktif') }} ({{ $widgets->count() }})</h3>
            <div class="card-subtitle">{{ __('Drag handle untuk reorder · ubah size lewat dropdown') }}</div>
        </div>
        @if($widgets->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="layout-dashboard" :title="__('Belum ada widget aktif.')" />
            </div>
        @else
            <div class="list-group list-group-flush" x-data="widgetReorder()">
                @foreach($widgets as $w)
                    <div class="list-group-item d-flex align-items-center gap-2" data-id="{{ $w->id }}">
                        <span class="text-muted cursor-move drag-handle" title="{{ __('Drag untuk reorder') }}"><x-ti name="grip-vertical" /></span>
                        <div class="flex-fill">
                            <div class="fw-semibold">{{ $registry[$w->widget_type]['name'] ?? $w->widget_type }}</div>
                            <div class="text-muted small">{{ $registry[$w->widget_type]['desc'] ?? '' }}</div>
                        </div>
                        <form method="POST" action="{{ route('dashboard.widgets.update', $w) }}" class="d-flex align-items-center gap-2">
                            @csrf @method('PUT')
                            <select name="size" onchange="this.form.submit()" class="form-select form-select-sm">
                                @foreach($registry[$w->widget_type]['sizes'] ?? ['md'] as $size)
                                    <option value="{{ $size }}" {{ $w->size === $size ? 'selected' : '' }}>{{ strtoupper($size) }}</option>
                                @endforeach
                            </select>
                        </form>
                        <form method="POST" action="{{ route('dashboard.widgets.destroy', $w) }}" onsubmit="return confirm('Hapus widget?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><x-ti name="trash" /></button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js" defer></script>
    <script>
        function widgetReorder() {
            return {
                init() {
                    this.$nextTick(() => {
                        if (typeof Sortable === 'undefined') { setTimeout(() => this.init(), 100); return; }
                        const list = this.$el;
                        Sortable.create(list, {
                            handle: '.drag-handle',
                            animation: 180,
                            onEnd: () => {
                                const ids = [...list.querySelectorAll('[data-id]')].map(el => el.dataset.id);
                                fetch('{{ route("dashboard.widgets.reorder") }}', {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    },
                                    body: JSON.stringify({ ids })
                                });
                            }
                        });
                    });
                }
            };
        }
    </script>
    @endpush
</x-tabler.layout>

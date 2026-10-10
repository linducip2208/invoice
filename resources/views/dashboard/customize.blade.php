<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Customize Dashboard') }} <span class="text-2xl">🎨</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ Atur widget yang muncul di dashboard kamu</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-unicorn-secondary text-sm">← {{ __('Dashboard') }}</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        @if(session('success'))<div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>@endif

        <div class="card-unicorn !p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">+ Tambah Widget</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($registry as $key => $info)
                    <form method="POST" action="{{ route('dashboard.widgets.store') }}" class="flex items-center gap-3 p-3 rounded-xl border border-purple-200 hover:border-pink-300 hover:bg-pink-50/40">
                        @csrf
                        <input type="hidden" name="widget_type" value="{{ $key }}">
                        <input type="hidden" name="size" value="{{ $info['sizes'][0] }}">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-purple-900 text-sm">{{ $info['name'] }}</p>
                            <p class="text-xs text-purple-500 truncate">{{ $info['desc'] }}</p>
                        </div>
                        <button type="submit" class="text-pink-600 font-bold text-xl hover:text-pink-700 shrink-0">+</button>
                    </form>
                @endforeach
            </div>
        </div>

        <div class="card-unicorn !p-0 overflow-hidden">
            <div class="p-6 border-b border-purple-100">
                <h3 class="font-display font-bold text-lg text-purple-900">Widget Aktif ({{ $widgets->count() }})</h3>
                <p class="text-xs text-purple-500 mt-1">Drag handle untuk reorder · ubah size lewat dropdown</p>
            </div>
            @if($widgets->isEmpty())
                <p class="p-8 text-center text-purple-400">Belum ada widget aktif.</p>
            @else
                <div class="divide-y divide-purple-100" x-data="widgetReorder()">
                    @foreach($widgets as $w)
                        <div class="p-4 flex items-center gap-3" data-id="{{ $w->id }}">
                            <span class="text-purple-300 cursor-move drag-handle" title="Drag untuk reorder">⋮⋮</span>
                            <div class="flex-1">
                                <p class="font-semibold text-purple-900">{{ $registry[$w->widget_type]['name'] ?? $w->widget_type }}</p>
                                <p class="text-xs text-purple-500">{{ $registry[$w->widget_type]['desc'] ?? '' }}</p>
                            </div>
                            <form method="POST" action="{{ route('dashboard.widgets.update', $w) }}" class="flex items-center gap-2">
                                @csrf @method('PUT')
                                <select name="size" onchange="this.form.submit()" class="text-xs input-unicorn rounded-lg px-2 py-1">
                                    @foreach($registry[$w->widget_type]['sizes'] ?? ['md'] as $size)
                                        <option value="{{ $size }}" {{ $w->size === $size ? 'selected' : '' }}>{{ strtoupper($size) }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form method="POST" action="{{ route('dashboard.widgets.destroy', $w) }}" onsubmit="return confirm('Hapus widget?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-500 text-sm font-semibold">✗</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

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
</x-app-layout>

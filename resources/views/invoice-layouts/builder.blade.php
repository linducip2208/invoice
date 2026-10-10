<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Invoice Builder') }} <span class="text-2xl">🎨</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Drag block dari kiri, drop ke canvas, atur urut & properties') }}</p>
            </div>
            <a href="{{ route('invoice-layouts.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    @php
        $layoutInitial = [
            'id'          => $layout->id,
            'name'        => $layout->name ?? 'Layout Baru',
            'description' => $layout->description,
            'blocks'      => is_array($layout->blocks ?? null) && count($layout->blocks) > 0
                ? $layout->blocks
                : \App\Models\InvoiceLayout::defaultBlocks(),
            'settings'    => is_array($layout->settings ?? null) && count($layout->settings) > 0
                ? $layout->settings
                : ['paper' => 'A4', 'font' => 'Inter', 'primary' => '#A78BFA'],
            'is_default'  => (bool) ($layout->is_default ?? false),
        ];
    @endphp

    <script>window.__invoiceLayoutInitial = @json($layoutInitial);</script>

    <div x-data="builderApp(window.__invoiceLayoutInitial)" x-init="init()" class="max-w-7xl mx-auto">

        <form method="POST" action="{{ $layout->id ? route('invoice-layouts.update', $layout) : route('invoice-layouts.store') }}" @submit="syncForm">
            @csrf
            @if($layout->id) @method('PUT') @endif
            <input type="hidden" name="name"        :value="model.name">
            <input type="hidden" name="description" :value="model.description">
            <input type="hidden" name="blocks"      :value="JSON.stringify(model.blocks)">
            <input type="hidden" name="settings"    :value="JSON.stringify(model.settings)">
            <input type="hidden" name="is_default"  :value="model.is_default ? 1 : 0">
            <button type="submit" class="btn-unicorn fixed bottom-6 right-6 z-50 shadow-lg px-6 py-3">
                💾 {{ __('Simpan Layout') }}
            </button>

            <input type="text" x-model="model.name" placeholder="Nama layout" class="text-xl font-display font-bold text-purple-900 bg-transparent border-0 border-b-2 border-purple-200 focus:border-pink-400 focus:ring-0 px-0 mb-4 w-full max-w-md">
        </form>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <!-- Block palette -->
            <div class="lg:col-span-3 card-unicorn">
                <h3 class="font-display font-bold text-sm text-purple-900 mb-3 uppercase tracking-wider">✦ Tambah Block</h3>
                <div id="palette" class="space-y-2">
                    <template x-for="b in palette" :key="b.type">
                        <div :data-block-type="b.type" class="palette-item p-3 rounded-lg bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 cursor-grab text-sm text-purple-700 font-semibold hover:shadow-md transition flex items-center gap-2">
                            <span x-text="b.icon"></span>
                            <span x-text="b.label"></span>
                        </div>
                    </template>
                </div>

                <h3 class="font-display font-bold text-sm text-purple-900 mt-6 mb-3 uppercase tracking-wider">✦ Settings</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-purple-500 font-bold">Paper</label>
                        <select x-model="model.settings.paper" class="input-unicorn rounded-lg px-3 py-1.5 text-sm w-full mt-1">
                            <option>A4</option><option>Letter</option><option>Legal</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-purple-500 font-bold">Primary Color</label>
                        <input type="color" x-model="model.settings.primary" class="h-9 w-full rounded-lg border-2 border-purple-200 cursor-pointer mt-1">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-purple-700">
                        <input type="checkbox" x-model="model.is_default" class="rounded border-purple-300">
                        Set sebagai default
                    </label>
                </div>
            </div>

            <!-- Canvas -->
            <div class="lg:col-span-6">
                <div class="bg-white rounded-2xl shadow-unicorn-glow p-8 min-h-[800px]" :style="`--primary:${model.settings.primary}`">
                    <div id="canvas" class="space-y-3 min-h-[600px]">
                        <template x-for="(block, i) in model.blocks" :key="block._id ?? (block._id = i + '-' + Math.random())">
                            <div :data-block-id="block._id" @click="selected = block._id" :class="selected === block._id ? 'ring-2 ring-pink-400' : 'ring-1 ring-purple-100'" class="p-4 rounded-xl bg-gradient-to-br from-white to-purple-50/30 cursor-move group transition">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2 text-xs font-bold text-purple-500 uppercase tracking-wider">
                                        <span class="cursor-grab">⋮⋮</span>
                                        <span x-text="blockMeta(block.type).icon + ' ' + blockMeta(block.type).label"></span>
                                    </div>
                                    <button type="button" @click.stop="removeBlock(i)" class="text-rose-400 hover:text-rose-600 opacity-0 group-hover:opacity-100 transition text-lg">×</button>
                                </div>
                                <div class="text-xs text-purple-700" x-html="renderPreview(block)"></div>
                            </div>
                        </template>
                        <div x-show="model.blocks.length === 0" class="text-center py-20 text-purple-400 border-2 border-dashed border-purple-200 rounded-xl">
                            ✦ Drag block dari panel kiri ke sini ✦
                        </div>
                    </div>
                </div>
            </div>

            <!-- Properties panel -->
            <div class="lg:col-span-3 card-unicorn">
                <h3 class="font-display font-bold text-sm text-purple-900 mb-3 uppercase tracking-wider">✦ Properties</h3>
                <div x-show="!selectedBlock" class="text-sm text-purple-400">Pilih block di canvas untuk edit properti.</div>
                <div x-show="selectedBlock" class="space-y-3">
                    <div class="text-xs text-purple-500 font-bold" x-text="selectedBlock?.type"></div>
                    <template x-for="(propValue, propKey) in (selectedBlock?.props ?? {})" :key="propKey">
                        <div>
                            <label class="text-xs text-purple-500 font-bold uppercase" x-text="propKey"></label>
                            <template x-if="typeof propValue === 'boolean'">
                                <label class="flex items-center gap-2 text-sm text-purple-700 mt-1">
                                    <input type="checkbox" :checked="propValue" @change="updateProp(propKey, $event.target.checked)" class="rounded border-purple-300">
                                    <span x-text="propValue ? 'On' : 'Off'"></span>
                                </label>
                            </template>
                            <template x-if="typeof propValue === 'string'">
                                <input type="text" :value="propValue" @input="updateProp(propKey, $event.target.value)" class="input-unicorn rounded-lg px-3 py-1.5 text-sm w-full mt-1">
                            </template>
                        </div>
                    </template>
                </div>
                <div class="mt-6 pt-4 border-t border-purple-100">
                    <label class="text-xs text-purple-500 font-bold uppercase">Description</label>
                    <textarea x-model="model.description" rows="3" class="input-unicorn rounded-lg px-3 py-2 text-sm w-full mt-1" placeholder="Notes for your team..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <script>
        const blockPalette = [
            { type: 'header',     label: 'Header',      icon: '🦄', defaultProps: { show_logo: true, align: 'left' } },
            { type: 'bill_to',    label: 'Bill To',     icon: '📍', defaultProps: { title: 'Bill To' } },
            { type: 'dates',      label: 'Dates',       icon: '📅', defaultProps: { show_due: true } },
            { type: 'items',      label: 'Items Table', icon: '📋', defaultProps: { show_tax: true, show_qty: true } },
            { type: 'totals',     label: 'Totals',      icon: '💰', defaultProps: {} },
            { type: 'notes',      label: 'Notes',       icon: '📝', defaultProps: { title: 'Notes' } },
            { type: 'terms',      label: 'Terms',       icon: '📜', defaultProps: { title: 'Terms & Conditions' } },
            { type: 'footer',     label: 'Footer',      icon: '✨', defaultProps: { text: 'Thank you for your business!' } },
            { type: 'divider',    label: 'Divider',     icon: '➖', defaultProps: {} },
            { type: 'spacer',     label: 'Spacer',      icon: '⬜', defaultProps: { height: 20 } },
            { type: 'custom_text','label': 'Custom Text','icon': '✏️', defaultProps: { text: 'Custom text...', align: 'left' } },
            { type: 'signature',  label: 'Signature',   icon: '✍️', defaultProps: { title: 'Authorized Signature' } },
        ];

        window.builderApp = function (initial) {
            return {
                model: Object.assign(
                    { id: null, name: 'Layout Baru', description: null, blocks: [], settings: { paper: 'A4', font: 'Inter', primary: '#A78BFA' }, is_default: false },
                    initial || {}
                ),
                selected: null,
                palette: blockPalette,
                init() {
                    if (! Array.isArray(this.model.blocks) || this.model.blocks.length === 0) {
                        this.model.blocks = blockPalette.slice(0, 8).map(b => ({ type: b.type, props: { ...(b.defaultProps ?? {}) } }));
                    }
                    this.model.blocks = this.model.blocks.map(b => ({ ...b, _id: Math.random().toString(36).slice(2) }));
                    this.$nextTick(() => this.initSortable());
                },
                initSortable() {
                    if (typeof Sortable === 'undefined') { setTimeout(() => this.initSortable(), 50); return; }
                    Sortable.create(document.getElementById('palette'), {
                        group: { name: 'blocks', pull: 'clone', put: false },
                        sort: false,
                        onClone: () => {},
                    });
                    Sortable.create(document.getElementById('canvas'), {
                        group: 'blocks',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        onAdd: (evt) => {
                            const type = evt.item.dataset?.blockType;
                            evt.item.remove();
                            if (type) {
                                const meta = blockPalette.find(b => b.type === type);
                                const newBlock = { type, props: { ...(meta?.defaultProps ?? {}) }, _id: Math.random().toString(36).slice(2) };
                                this.model.blocks.splice(evt.newIndex, 0, newBlock);
                            }
                        },
                        onUpdate: (evt) => {
                            const moved = this.model.blocks.splice(evt.oldIndex, 1)[0];
                            this.model.blocks.splice(evt.newIndex, 0, moved);
                        },
                    });
                },
                blockMeta(type) {
                    return blockPalette.find(b => b.type === type) ?? { label: type, icon: '?' };
                },
                get selectedBlock() {
                    return this.model.blocks.find(b => b._id === this.selected);
                },
                updateProp(key, value) {
                    if (this.selectedBlock) this.selectedBlock.props[key] = value;
                },
                removeBlock(i) {
                    this.model.blocks.splice(i, 1);
                },
                renderPreview(block) {
                    const previews = {
                        header: `<div style="display:flex;justify-content:space-between"><strong>${block.props.show_logo ? '[LOGO]' : ''} Company Name</strong><div>INV-001</div></div>`,
                        bill_to: `<div><em>${block.props.title}</em>: Client Name</div>`,
                        dates: `<div>Issue: 2026-01-01 ${block.props.show_due ? ' · Due: 2026-01-31' : ''}</div>`,
                        items: `<div style="border:1px dashed #c4b5fd;padding:6px;border-radius:6px">Item × Qty × Price ${block.props.show_tax ? '× Tax' : ''} = Total</div>`,
                        totals: `<div style="text-align:right"><strong>Subtotal · Tax · TOTAL</strong></div>`,
                        notes: `<div><em>${block.props.title}</em>: ...</div>`,
                        terms: `<div><em>${block.props.title}</em>: ...</div>`,
                        footer: `<div style="text-align:center;color:#a78bfa">${block.props.text}</div>`,
                        divider: `<hr style="border-color:#e9d5ff">`,
                        spacer: `<div style="height:${block.props.height}px"></div>`,
                        custom_text: `<div style="text-align:${block.props.align}">${block.props.text}</div>`,
                        signature: `<div>${block.props.title} ___________________</div>`,
                    };
                    return previews[block.type] ?? `<div>${block.type}</div>`;
                },
                syncForm() {
                    // strip _id before submitting
                    this.model.blocks = this.model.blocks.map(({ _id, ...rest }) => rest);
                },
            };
        }
    </script>
</x-app-layout>

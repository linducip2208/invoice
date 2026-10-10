<x-tabler.layout :title="__('Invoice Builder')" :pretitle="__('Workspace')">
    <x-slot name="actions">
        <a href="{{ route('invoice-layouts.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali') }}
        </a>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

    @php
        $layoutInitial = [
            'id' => $layout->id,
            'name' => $layout->name ?? 'Layout Baru',
            'description' => $layout->description,
            'blocks' => is_array($layout->blocks ?? null) && count($layout->blocks) > 0
                ? $layout->blocks
                : \App\Models\InvoiceLayout::defaultBlocks(),
            'settings' => is_array($layout->settings ?? null) && count($layout->settings) > 0
                ? $layout->settings
                : ['paper' => 'A4', 'font' => 'Inter', 'primary' => '#A78BFA'],
            'is_default' => (bool) ($layout->is_default ?? false),
        ];
    @endphp

    <script>window.__invoiceLayoutInitial = @json($layoutInitial);</script>

    <div x-data="builderApp(window.__invoiceLayoutInitial)" x-init="init()">

        <form method="POST" action="{{ $layout->id ? route('invoice-layouts.update', $layout) : route('invoice-layouts.store') }}" @submit="syncForm">
            @csrf
            @if($layout->id) @method('PUT') @endif
            <input type="hidden" name="name" :value="model.name">
            <input type="hidden" name="description" :value="model.description">
            <input type="hidden" name="blocks" :value="JSON.stringify(model.blocks)">
            <input type="hidden" name="settings" :value="JSON.stringify(model.settings)">
            <input type="hidden" name="is_default" :value="model.is_default ? 1 : 0">
            <button type="submit" class="btn btn-primary position-fixed bottom-0 end-0 m-4 shadow" style="z-index: 50;">
                <x-ti name="check" /> {{ __('Simpan Layout') }}
            </button>

            <div class="card mb-3">
                <div class="card-body">
                    <input type="text" x-model="model.name" placeholder="Nama layout" class="form-control form-control-lg border-0 border-bottom rounded-0 px-0 w-100" style="max-width: 28rem;">
                    <div class="text-muted small mt-1">{{ __('Drag block dari kiri, drop ke canvas, atur urut & properties') }}</div>
                </div>
            </div>
        </form>

        <div class="row row-decks row-cards">
            <!-- Block palette -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-uppercase"><x-ti name="plus" /> {{ __('Tambah Block') }}</h3>
                    </div>
                    <div class="card-body">
                        <div id="palette" class="d-grid gap-2">
                            <template x-for="b in palette" :key="b.type">
                                <div :data-block-type="b.type" class="palette-item p-3 rounded border bg-light cursor-pointer d-flex align-items-center gap-2 small fw-semibold text-muted" style="cursor: grab;">
                                    <span x-text="b.label"></span>
                                </div>
                            </template>
                        </div>

                        <h3 class="card-title text-uppercase mt-4 mb-3"><x-ti name="settings" /> {{ __('Settings') }}</h3>
                        <div class="d-grid gap-3">
                            <div>
                                <label class="form-label">{{ __('Paper') }}</label>
                                <select x-model="model.settings.paper" class="form-select">
                                    <option>A4</option><option>Letter</option><option>Legal</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">{{ __('Primary Color') }}</label>
                                <input type="color" x-model="model.settings.primary" class="form-control form-control-color w-100">
                            </div>
                            <label class="form-check">
                                <input type="checkbox" x-model="model.is_default" class="form-check-input">
                                <span class="form-check-label">{{ __('Set sebagai default') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body p-4" :style="`--primary:${model.settings.primary}`" style="min-height: 800px;">
                        <div id="canvas" class="d-grid gap-3" style="min-height: 600px;">
                            <template x-for="(block, i) in model.blocks" :key="block._id ?? (block._id = i + '-' + Math.random())">
                                <div :data-block-id="block._id" @click="selected = block._id" :class="selected === block._id ? 'border-primary' : ''" class="p-3 rounded border bg-white" style="cursor: move;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2 small fw-bold text-muted text-uppercase">
                                            <span style="cursor: grab;">...</span>
                                            <span x-text="blockMeta(block.type).label"></span>
                                        </div>
                                        <button type="button" @click.stop="removeBlock(i)" class="btn btn-sm btn-outline-danger"><x-ti name="x" /></button>
                                    </div>
                                    <div class="small text-muted" x-html="renderPreview(block)"></div>
                                </div>
                            </template>
                            <div x-show="model.blocks.length === 0" class="text-center py-5 text-muted border border-dashed rounded">
                                {{ __('Drag block dari panel kiri ke sini') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Properties panel -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-uppercase"><x-ti name="settings" /> {{ __('Properties') }}</h3>
                    </div>
                    <div class="card-body">
                        <div x-show="!selectedBlock" class="text-muted small">{{ __('Pilih block di canvas untuk edit properti.') }}</div>
                        <div x-show="selectedBlock" class="d-grid gap-3">
                            <div class="small text-muted fw-bold" x-text="selectedBlock?.type"></div>
                            <template x-for="(propValue, propKey) in (selectedBlock?.props ?? {})" :key="propKey">
                                <div>
                                    <label class="form-label text-uppercase small" x-text="propKey"></label>
                                    <template x-if="typeof propValue === 'boolean'">
                                        <label class="form-check mt-1">
                                            <input type="checkbox" :checked="propValue" @change="updateProp(propKey, $event.target.checked)" class="form-check-input">
                                            <span class="form-check-label" x-text="propValue ? 'On' : 'Off'"></span>
                                        </label>
                                    </template>
                                    <template x-if="typeof propValue === 'string'">
                                        <input type="text" :value="propValue" @input="updateProp(propKey, $event.target.value)" class="form-control mt-1">
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="mt-4 pt-3 border-top">
                            <label class="form-label text-uppercase small">{{ __('Description') }}</label>
                            <textarea x-model="model.description" rows="3" class="form-control mt-1" placeholder="Notes for your team..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const blockPalette = [
            { type: 'header', label: 'Header', icon: '', defaultProps: { show_logo: true, align: 'left' } },
            { type: 'bill_to', label: 'Bill To', icon: '', defaultProps: { title: 'Bill To' } },
            { type: 'dates', label: 'Dates', icon: '', defaultProps: { show_due: true } },
            { type: 'items', label: 'Items Table', icon: '', defaultProps: { show_tax: true, show_qty: true } },
            { type: 'totals', label: 'Totals', icon: '', defaultProps: {} },
            { type: 'notes', label: 'Notes', icon: '', defaultProps: { title: 'Notes' } },
            { type: 'terms', label: 'Terms', icon: '', defaultProps: { title: 'Terms & Conditions' } },
            { type: 'footer', label: 'Footer', icon: '', defaultProps: { text: 'Thank you for your business!' } },
            { type: 'divider', label: 'Divider', icon: '', defaultProps: {} },
            { type: 'spacer', label: 'Spacer', icon: '', defaultProps: { height: 20 } },
            { type: 'custom_text', label: 'Custom Text', icon: '', defaultProps: { text: 'Custom text...', align: 'left' } },
            { type: 'signature', label: 'Signature', icon: '', defaultProps: { title: 'Authorized Signature' } },
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
</x-tabler.layout>

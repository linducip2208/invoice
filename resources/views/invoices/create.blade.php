<x-tabler.layout :title="__('Create Invoice')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('invoices.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
    </x-slot>

    <!-- Helper banner: drag-drop builder location -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col d-flex gap-3">
                    <x-ti name="sparkles" class="icon icon-lg" />
                    <div class="small">
                        <p class="fw-bold mb-1">Drag & drop di halaman ini:</p>
                        <ul class="text-muted mt-1 mb-0 small">
                            <li><strong>Drag handle</strong> di kolom kiri tabel Items → reorder baris</li>
                            <li><strong>Custom Print Layout</strong> picker → ada di section "Template & Colors" di bawah</li>
                            <li><strong>Design layout baru</strong> → @if(workspace()?->planFeature('drag_drop_builder'))<a href="{{ route('invoice-layouts.index') }}" class="fw-bold">/invoice-layouts</a>@else<span class="text-warning fw-bold">Upgrade ke Business untuk drag-drop builder</span>@endif</li>
                        </ul>
                    </div>
                </div>
                <div class="col-auto">
                    @if(workspace()?->planFeature('drag_drop_builder'))
                        <a href="{{ route('invoice-layouts.create') }}" class="btn btn-sm">+ Bikin Layout</a>
                    @else
                        <a href="{{ route('billing.show') }}" class="btn btn-sm btn-primary"><x-ti name="sparkles" /> Upgrade</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('invoices.store') }}" data-prevent-double-submit x-data="invoiceForm({{ Js::from([]) }}, {{ Js::from($products->keyBy('id')->toArray()) }})">
        @csrf

        <!-- Client & Dates -->
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ __('Client & Dates') }}</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Client *')" name="client_id">
                            <select id="client_id" name="client_id" required class="form-select @error('client_id') is-invalid @enderror">
                                <option value="">{{ __('Select client…') }}</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}{{ $client->company_name ? ' (' . $client->company_name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Currency *')" name="currency">
                            <select id="currency" name="currency" x-model="currency" class="form-select">
                                <option value="IDR">IDR — Indonesian Rupiah</option>
                                <option value="USD">USD — US Dollar</option>
                                <option value="EUR">EUR — Euro</option>
                                <option value="GBP">GBP — British Pound</option>
                                <option value="SGD">SGD — Singapore Dollar</option>
                                <option value="AUD">AUD — Australian Dollar</option>
                                <option value="JPY">JPY — Japanese Yen</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Issue Date *')" name="issue_date" type="date" :value="old('issue_date', now()->format('Y-m-d'))" attrs="required" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Due Date *')" name="due_date" type="date" :value="old('due_date', now()->addDays(30)->format('Y-m-d'))" attrs="required" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Template & Colors -->
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ __('Template & Colors') }}</h3>
                <div class="row g-2 mb-3">
                    @php $templates = ['classic', 'modern', 'minimal', 'bold', 'elegant']; @endphp
                    @foreach ($templates as $tpl)
                        <div class="col-6 col-sm-4 col-lg-2">
                            <label class="form-selectgroup-item w-100">
                                <input type="radio" name="template" value="{{ $tpl }}" {{ old('template', 'classic') === $tpl ? 'checked' : '' }} class="form-selectgroup-input">
                                <span class="form-selectgroup-label text-center text-capitalize">{{ $tpl }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('template') <p class="text-danger small mb-3">{{ $message }}</p> @enderror

                <div class="row g-3">
                    <div class="col-sm-6">
                        <x-tabler.field :label="__('Primary Color')" name="primary_color">
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', '#A78BFA') }}" class="form-control form-control-color" style="width: 4rem;">
                                <input type="text" value="{{ old('primary_color', '#A78BFA') }}" id="primary_color_text" class="form-control form-control-sm" style="max-width: 8rem;" oninput="document.getElementById('primary_color').value = this.value">
                            </div>
                        </x-tabler.field>
                    </div>
                    <div class="col-sm-6">
                        <x-tabler.field :label="__('Accent Color')" name="accent_color">
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', '#FF7AC6') }}" class="form-control form-control-color" style="width: 4rem;">
                                <input type="text" value="{{ old('accent_color', '#FF7AC6') }}" id="accent_color_text" class="form-control form-control-sm" style="max-width: 8rem;" oninput="document.getElementById('accent_color').value = this.value">
                            </div>
                        </x-tabler.field>
                    </div>
                </div>

                <!-- Custom Layout Picker (drag-drop builder output) -->
                @if(isset($layouts) && $layouts->isNotEmpty())
                    <div class="mt-3 pt-3 border-top">
                        <x-tabler.field :label="__('Custom Print Layout (drag-drop builder)')" name="invoice_layout_id">
                            <select id="invoice_layout_id" name="invoice_layout_id" class="form-select">
                                <option value="">— {{ __('Pakai template di atas (default)') }} —</option>
                                @foreach($layouts as $l)
                                    <option value="{{ $l->id }}" {{ old('invoice_layout_id') == $l->id ? 'selected' : '' }}>
                                        {{ $l->name }}{{ $l->is_default ? ' (default)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                        <p class="small text-muted mt-1">
                            {{ __('Pilih layout custom yang sudah kamu design. Override template di atas.') }}
                            @if(workspace()?->planFeature('drag_drop_builder'))
                                <a href="{{ route('invoice-layouts.create') }}" class="fw-semibold">+ {{ __('Bikin layout baru') }}</a>
                            @endif
                        </p>
                    </div>
                @elseif(workspace()?->planFeature('drag_drop_builder'))
                    <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between gap-3 p-3 rounded bg-light">
                        <p class="mb-0">{{ __('Custom drag-drop layout untuk hasil print/PDF unik.') }}</p>
                        <a href="{{ route('invoice-layouts.create') }}" class="btn btn-sm">+ {{ __('Design Layout') }}</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Items') }}</h3>
                <div class="card-actions">
                    <button type="button" @click="addItem()" class="btn btn-sm btn-primary">
                        <x-ti name="plus" /> {{ __('Add Row') }}
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th class="w-1"></th>
                            <th class="w-1">#</th>
                            <th>{{ __('Description *') }}</th>
                            <th class="text-end" style="width: 6rem;">{{ __('Qty *') }}</th>
                            <th class="text-end" style="width: 8rem;">{{ __('Unit *') }}</th>
                            <th class="text-end" style="width: 5rem;">{{ __('Tax %') }}</th>
                            <th class="text-end" style="width: 7rem;">{{ __('Total') }}</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody id="invoiceItemsBody" x-ref="itemsBody">
                        <template x-for="(item, index) in items" :key="item._uid">
                            <tr class="invoice-item-row" :data-uid="item._uid">
                                <td class="text-muted cursor-move drag-handle text-center user-select-none" title="Drag untuk reorder"><x-ti name="dots-vertical" /></td>
                                <td class="text-muted small" x-text="index + 1"></td>
                                <td>
                                    <select class="form-select form-select-sm"
                                            :name="'items[' + index + '][product_id]'"
                                            x-model="item.product_id"
                                            @change="onProductSelect(index, $event.target.value)">
                                        <option value="">— {{ __('Pick product') }} —</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" required
                                           :name="'items[' + index + '][description]'"
                                           x-model="item.description"
                                           placeholder="{{ __('Description') }}"
                                           class="mt-1 form-control form-control-sm">
                                </td>
                                <td><input type="number" step="0.01" min="0.01" required :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" class="form-control form-control-sm text-end"></td>
                                <td><input type="number" step="0.01" min="0" required :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" class="form-control form-control-sm text-end"></td>
                                <td><input type="number" step="0.01" min="0" max="100" :name="'items[' + index + '][tax_rate]'" x-model.number="item.tax_rate" class="form-control form-control-sm text-end"></td>
                                <td class="text-end fw-bold small" x-text="formatCurrency(lineTotal(index))"></td>
                                <td class="text-center">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="btn btn-sm btn-link text-danger p-0"><x-ti name="x" /></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            @error('items')
                <div class="card-body pt-0"><p class="mt-1 small text-danger mb-0">{{ $message }}</p></div>
            @enderror
        </div>

        <!-- Summary -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h3 class="card-title mb-3">{{ __('Tax & Discount') }}</h3>
                        <x-tabler.field :label="__('Tax Rate (%)')" name="tax_rate">
                            <input type="number" step="0.01" min="0" max="100" id="tax_rate" name="tax_rate" x-model.number="tax_rate" value="{{ old('tax_rate', 0) }}" class="form-control">
                        </x-tabler.field>
                        <x-tabler.field :label="__('Discount Type')" name="discount_type">
                            <select id="discount_type" name="discount_type" x-model="discount_type" class="form-select">
                                <option value="none">{{ __('None') }}</option>
                                <option value="fixed">{{ __('Fixed Amount') }}</option>
                                <option value="percent">{{ __('Percentage (%)') }}</option>
                            </select>
                        </x-tabler.field>
                        <div x-show="discount_type !== 'none'">
                            <x-tabler.field :label="__('Discount Value')" name="discount_value">
                                <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" x-model.number="discount_value" value="{{ old('discount_value', 0) }}" class="form-control">
                            </x-tabler.field>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <h3 class="card-title mb-3">{{ __('Summary') }}</h3>
                        <div class="rounded bg-light p-3">
                            <div class="d-flex justify-content-between small mb-1"><span class="text-muted">{{ __('Subtotal') }}</span><span class="fw-semibold" x-text="formatCurrency(subtotal)"></span></div>
                            <div class="d-flex justify-content-between small mb-1" x-show="taxAmount > 0"><span class="text-muted">{{ __('Tax') }} <span x-text="'(' + tax_rate + '%)'"></span></span><span x-text="formatCurrency(taxAmount)"></span></div>
                            <div class="d-flex justify-content-between small mb-1" x-show="discountAmount > 0"><span class="text-muted">{{ __('Discount') }}</span><span class="text-danger fw-semibold" x-text="'-' + formatCurrency(discountAmount)"></span></div>
                            <div class="d-flex justify-content-between h4 border-top pt-2 mt-2 mb-0">
                                <span>{{ __('Total') }}</span>
                                <span x-text="formatCurrency(total)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Notes')" name="notes">
                            <textarea id="notes" name="notes" rows="4" class="form-control" placeholder="{{ __('Sparkly notes for your client…') }}">{{ old('notes') }}</textarea>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Terms')" name="terms">
                            <textarea id="terms" name="terms" rows="4" class="form-control" placeholder="{{ __('Payment terms…') }}">{{ old('terms') }}</textarea>
                        </x-tabler.field>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2">
            <a href="{{ route('invoices.index') }}" class="btn">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Create Invoice') }}</button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js" defer></script>
    <script>
        const __uid = () => Math.random().toString(36).slice(2, 10);

        function invoiceForm(initialItems, productsMap) {
            const defaultItems = initialItems.length > 0 ? initialItems.map(function (item) {
                return {
                    _uid: __uid(),
                    product_id: item.product_id || '',
                    description: item.description || '',
                    quantity: parseFloat(item.quantity) || 1,
                    unit_price: parseFloat(item.unit_price) || 0,
                    tax_rate: parseFloat(item.tax_rate) || 0
                };
            }) : [{ _uid: __uid(), product_id: '', description: '', quantity: 1, unit_price: 0, tax_rate: 0 }];

            return {
                items: defaultItems,
                tax_rate: {{ old('tax_rate', 0) }},
                discount_type: '{{ old('discount_type', 'none') }}',
                discount_value: {{ old('discount_value', 0) }},
                currency: '{{ old('currency', workspace()?->currency ?? 'IDR') }}',

                init() {
                    var primaryEl = document.getElementById('primary_color');
                    var primaryText = document.getElementById('primary_color_text');
                    var accentEl = document.getElementById('accent_color');
                    var accentText = document.getElementById('accent_color_text');
                    if (primaryEl && primaryText) {
                        primaryEl.addEventListener('input', function () { primaryText.value = this.value; });
                        primaryText.addEventListener('input', function () { primaryEl.value = this.value; });
                    }
                    if (accentEl && accentText) {
                        accentEl.addEventListener('input', function () { accentText.value = this.value; });
                        accentEl.addEventListener('input', function () { accentEl.value = this.value; });
                    }
                    this.$nextTick(() => this.initSortable());
                },

                initSortable() {
                    if (typeof Sortable === 'undefined') { setTimeout(() => this.initSortable(), 80); return; }
                    const tbody = document.getElementById('invoiceItemsBody');
                    if (! tbody) return;
                    Sortable.create(tbody, {
                        handle: '.drag-handle',
                        animation: 180,
                        ghostClass: 'opacity-40',
                        onEnd: (evt) => {
                            if (evt.oldIndex === evt.newIndex) return;
                            const moved = this.items.splice(evt.oldIndex, 1)[0];
                            this.items.splice(evt.newIndex, 0, moved);
                        },
                    });
                },

                addItem() { this.items.push({ _uid: __uid(), product_id: '', description: '', quantity: 1, unit_price: 0, tax_rate: 0 }); this.$nextTick(() => this.initSortable()); },
                removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); },

                onProductSelect(index, productId) {
                    if (!productId) return;
                    var product = productsMap[productId];
                    if (!product) return;
                    this.items[index].description = product.description || product.name || '';
                    this.items[index].unit_price = parseFloat(product.unit_price) || 0;
                    this.items[index].tax_rate = parseFloat(product.tax_rate) || 0;
                },

                lineTotal(index) {
                    var item = this.items[index];
                    return (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                },

                get subtotal() {
                    return this.items.reduce(function (sum, item) {
                        return sum + (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                    }, 0);
                },
                get taxAmount() { return this.subtotal * (parseFloat(this.tax_rate) || 0) / 100; },
                get discountAmount() {
                    if (this.discount_type === 'percent') return this.subtotal * (parseFloat(this.discount_value) || 0) / 100;
                    if (this.discount_type === 'fixed') return parseFloat(this.discount_value) || 0;
                    return 0;
                },
                get total() { return this.subtotal + this.taxAmount - this.discountAmount; },
                formatCurrency(value) {
                    return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value || 0);
                }
            };
        }
    </script>
</x-tabler.layout>

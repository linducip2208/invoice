<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Edit Invoice') }} <span class="text-2xl">✏️</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ $invoice->invoice_no }}</p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Back') }}</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <form method="POST" action="{{ route('invoices.update', $invoice) }}" x-data="invoiceForm({{ Js::from($invoice->items->toArray()) }}, {{ Js::from($products->keyBy('id')->toArray()) }})" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Client & Dates -->
            <div class="card-unicorn">
                <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
                <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Client & Dates') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <x-input-label :value="__('Client *')" />
                        <select name="client_id" required class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">
                            <option value="">{{ __('Select client…') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $invoice->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}{{ $client->company_name ? ' (' . $client->company_name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Currency *')" />
                        <select name="currency" x-model="currency" class="mt-1 block w-full input-unicorn rounded-xl px-4 py-2">
                            @foreach(['IDR'=>'IDR — Indonesian Rupiah','USD'=>'USD — US Dollar','EUR'=>'EUR — Euro','GBP'=>'GBP — British Pound','SGD'=>'SGD — Singapore Dollar','AUD'=>'AUD — Australian Dollar','JPY'=>'JPY — Japanese Yen'] as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Issue Date *')" />
                        <x-text-input name="issue_date" type="date" :value="old('issue_date', $invoice->issue_date?->format('Y-m-d'))" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Due Date *')" />
                        <x-text-input name="due_date" type="date" :value="old('due_date', $invoice->due_date?->format('Y-m-d'))" required class="mt-1 block w-full" />
                    </div>
                </div>
            </div>

            <!-- Template & Colors -->
            <div class="card-unicorn">
                <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                    <span class="text-purple-400">✦</span> {{ __('Template & Colors') }}
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mb-5">
                    @php $templates = ['classic', 'modern', 'minimal', 'bold', 'elegant']; $tplGradients = ['classic'=>'from-purple-100 to-blue-100','modern'=>'from-pink-100 to-purple-100','minimal'=>'from-gray-100 to-purple-50','bold'=>'from-pink-200 to-yellow-100','elegant'=>'from-purple-200 to-pink-100']; @endphp
                    @foreach ($templates as $tpl)
                        <label class="relative rounded-xl p-3 cursor-pointer border-2 border-purple-100 hover:border-pink-300 has-[:checked]:border-pink-400 has-[:checked]:shadow-unicorn-pink transition">
                            <input type="radio" name="template" value="{{ $tpl }}" {{ old('template', $invoice->template) === $tpl ? 'checked' : '' }} class="sr-only">
                            <div class="text-center">
                                <div class="w-full h-16 rounded-lg bg-gradient-to-br {{ $tplGradients[$tpl] }} mb-2 flex items-center justify-center text-xs uppercase font-bold text-purple-700">{{ $tpl }}</div>
                                <span class="text-sm font-semibold text-purple-700 capitalize">{{ $tpl }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label :value="__('Primary Color')" />
                        <div class="mt-1 flex items-center gap-2">
                            <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $invoice->primary_color ?: '#A78BFA') }}" class="h-10 w-16 rounded-xl border-2 border-purple-200 cursor-pointer">
                            <input type="text" value="{{ old('primary_color', $invoice->primary_color ?: '#A78BFA') }}" id="primary_color_text" class="input-unicorn rounded-xl px-3 py-2 text-sm w-32" oninput="document.getElementById('primary_color').value = this.value">
                        </div>
                    </div>
                    <div>
                        <x-input-label :value="__('Accent Color')" />
                        <div class="mt-1 flex items-center gap-2">
                            <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', $invoice->accent_color ?: '#FF7AC6') }}" class="h-10 w-16 rounded-xl border-2 border-purple-200 cursor-pointer">
                            <input type="text" value="{{ old('accent_color', $invoice->accent_color ?: '#FF7AC6') }}" id="accent_color_text" class="input-unicorn rounded-xl px-3 py-2 text-sm w-32" oninput="document.getElementById('accent_color').value = this.value">
                        </div>
                    </div>
                </div>

                @if(isset($layouts) && $layouts->isNotEmpty())
                    <div class="mt-5 pt-5 border-t border-purple-100">
                        <x-input-label :value="__('Custom Print Layout')" />
                        <select name="invoice_layout_id" class="input-premium mt-1 w-full">
                            <option value="">— {{ __('Pakai template di atas (default)') }} —</option>
                            @foreach($layouts as $l)
                                <option value="{{ $l->id }}" {{ old('invoice_layout_id', $invoice->invoice_layout_id) == $l->id ? 'selected' : '' }}>
                                    🎨 {{ $l->name }}{{ $l->is_default ? ' (default)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Items -->
            <div class="card-unicorn">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display font-bold text-lg text-purple-900 flex items-center gap-2">
                        <span class="text-sky-400">✦</span> {{ __('Items') }}
                    </h3>
                    <button type="button" @click="addItem()" class="btn-unicorn">+ {{ __('Add Row') }}</button>
                </div>
                <div class="overflow-x-auto -mx-2">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                                <th class="px-2 py-2 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider w-6">⋮⋮</th>
                                <th class="px-2 py-2 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider w-6">#</th>
                                <th class="px-2 py-2 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">{{ __('Description *') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-24">{{ __('Qty *') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-32">{{ __('Unit *') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-20">{{ __('Tax %') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-28">{{ __('Total') }}</th>
                                <th class="px-2 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItemsBody">
                            <template x-for="(item, index) in items" :key="item._uid">
                                <tr class="border-t border-purple-100 invoice-item-row" :data-uid="item._uid">
                                    <td class="px-2 py-2 text-purple-300 cursor-move drag-handle text-center select-none" title="Drag untuk reorder">⋮⋮</td>
                                    <td class="px-2 py-2 text-sm text-purple-400" x-text="index + 1"></td>
                                    <td class="px-2 py-2">
                                        <select class="w-full input-unicorn rounded-lg px-2 py-1 text-xs"
                                                :name="'items[' + index + '][product_id]'"
                                                x-model="item.product_id"
                                                @change="onProductSelect(index, $event.target.value)">
                                            <option value="">— {{ __('Pick product') }} —</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" required :name="'items[' + index + '][description]'" x-model="item.description" placeholder="{{ __('Description') }}" class="mt-1 w-full input-unicorn rounded-lg px-2 py-1 text-sm">
                                    </td>
                                    <td class="px-2 py-2"><input type="number" step="0.01" min="0.01" required :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" class="w-full input-unicorn rounded-lg px-2 py-1 text-sm text-right"></td>
                                    <td class="px-2 py-2"><input type="number" step="0.01" min="0" required :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" class="w-full input-unicorn rounded-lg px-2 py-1 text-sm text-right"></td>
                                    <td class="px-2 py-2"><input type="number" step="0.01" min="0" max="100" :name="'items[' + index + '][tax_rate]'" x-model.number="item.tax_rate" class="w-full input-unicorn rounded-lg px-2 py-1 text-sm text-right"></td>
                                    <td class="px-2 py-2 text-right text-sm font-bold text-purple-900" x-text="formatCurrency(lineTotal(index))"></td>
                                    <td class="px-2 py-2 text-center"><button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-rose-400 hover:text-rose-600 text-xl leading-none">×</button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary -->
            <div class="card-unicorn">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ {{ __('Tax & Discount') }}</h3>
                        <div class="space-y-4">
                            <div>
                                <x-input-label :value="__('Tax Rate (%)')" />
                                <input type="number" step="0.01" min="0" max="100" name="tax_rate" x-model.number="tax_rate" value="{{ old('tax_rate', (float) $invoice->tax_rate) }}" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            </div>
                            <div>
                                <x-input-label :value="__('Discount Type')" />
                                <select name="discount_type" x-model="discount_type" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                                    <option value="none">{{ __('None') }}</option>
                                    <option value="fixed">{{ __('Fixed Amount') }}</option>
                                    <option value="percent">{{ __('Percentage (%)') }}</option>
                                </select>
                            </div>
                            <div x-show="discount_type !== 'none'">
                                <x-input-label :value="__('Discount Value')" />
                                <input type="number" step="0.01" min="0" name="discount_value" x-model.number="discount_value" value="{{ old('discount_value', (float) $invoice->discount_value) }}" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ {{ __('Summary') }}</h3>
                        <div class="space-y-2 p-5 rounded-2xl" style="background: linear-gradient(135deg, rgba(255,228,241,0.6), rgba(237,233,254,0.6), rgba(224,242,254,0.5));">
                            <div class="flex justify-between text-sm"><span class="text-purple-500">{{ __('Subtotal') }}</span><span class="text-purple-900 font-semibold" x-text="formatCurrency(subtotal)"></span></div>
                            <div class="flex justify-between text-sm" x-show="taxAmount > 0"><span class="text-purple-500">{{ __('Tax') }} <span x-text="'(' + tax_rate + '%)'"></span></span><span class="text-purple-900" x-text="formatCurrency(taxAmount)"></span></div>
                            <div class="flex justify-between text-sm" x-show="discountAmount > 0"><span class="text-purple-500">{{ __('Discount') }}</span><span class="text-rose-600 font-semibold" x-text="'-' + formatCurrency(discountAmount)"></span></div>
                            <div class="flex justify-between text-base font-display font-bold border-t border-purple-200 pt-2.5">
                                <span class="text-purple-900">{{ __('Total') }}</span>
                                <span class="text-rainbow" x-text="formatCurrency(total)"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">
                    <div>
                        <x-input-label :value="__('Notes')" />
                        <textarea name="notes" rows="4" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                    <div>
                        <x-input-label :value="__('Terms')" />
                        <textarea name="terms" rows="4" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">{{ old('terms', $invoice->terms) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice) }}" class="btn-unicorn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-unicorn px-8 py-3">✦ {{ __('Update Invoice') }} ✦</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js" defer></script>
    <script>
        const __uid = () => Math.random().toString(36).slice(2, 10);
        function invoiceForm(initialItems, productsMap) {
            const items = initialItems.length > 0 ? initialItems.map(function (item) {
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
                items: items,
                tax_rate: {{ old('tax_rate', (float) $invoice->tax_rate) }},
                discount_type: '{{ old('discount_type', $invoice->discount_type) }}',
                discount_value: {{ old('discount_value', (float) $invoice->discount_value) }},
                currency: '{{ old('currency', $invoice->currency) }}',
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
                        accentText.addEventListener('input', function () { accentEl.value = this.value; });
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
</x-app-layout>

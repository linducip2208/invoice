<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('New Recurring') }} <span class="text-2xl">🔄</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Schedule sparkles on auto-pilot') }}</p>
            </div>
            <a href="{{ route('recurring.index') }}" class="text-sm font-semibold text-purple-500 hover:text-pink-500">← {{ __('Back') }}</a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <form method="POST" action="{{ route('recurring.store') }}" x-data="recurringForm()" class="space-y-6">
            @csrf

            <div class="card-unicorn">
                <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
                <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ {{ __('Schedule') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <x-input-label :value="__('Client *')" />
                        <select name="client_id" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full" required>
                            <option value="">{{ __('Select client…') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }}@if($client->company_name) ({{ $client->company_name }})@endif</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label :value="__('Template')" />
                        <select name="template" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            @foreach ($templates as $tpl)
                                <option value="{{ $tpl->slug }}" @selected(old('template', workspace()?->default_template) == $tpl->slug)>{{ $tpl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Frequency')" />
                        <select name="frequency" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full" required>
                            <option value="daily"   @selected(old('frequency') === 'daily')>{{ __('Daily') }}</option>
                            <option value="weekly"  @selected(old('frequency') === 'weekly')>{{ __('Weekly') }}</option>
                            <option value="monthly" @selected(old('frequency', 'monthly') === 'monthly')>{{ __('Monthly') }}</option>
                            <option value="yearly"  @selected(old('frequency') === 'yearly')>{{ __('Yearly') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Interval *')" />
                        <x-text-input name="interval_value" type="number" :value="old('interval_value', 1)" min="1" max="365" required class="mt-1 block w-full" />
                        <p class="text-xs text-purple-400 mt-1">✦ {{ __('e.g. Every 2 weeks, Every 3 months') }}</p>
                    </div>
                    <div>
                        <x-input-label :value="__('Start Date *')" />
                        <x-text-input name="next_run_date" type="date" :value="old('next_run_date', now()->toDateString())" required class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('End Date (optional)')" />
                        <x-text-input name="end_date" type="date" :value="old('end_date')" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Max Occurrences (optional)')" />
                        <x-text-input name="max_occurrences" type="number" :value="old('max_occurrences')" min="1" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Currency')" />
                        <x-text-input name="currency" type="text" :value="old('currency', workspace()?->currency ?? 'IDR')" class="mt-1 block w-full" />
                    </div>
                </div>
            </div>

            <div class="card-unicorn">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display font-bold text-lg text-purple-900">✦ {{ __('Items') }}</h3>
                    <button type="button" @click="addItem" class="btn-unicorn">+ {{ __('Add Item') }}</button>
                </div>
                <div class="overflow-x-auto -mx-2">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                                <th class="px-2 py-2 text-left text-[11px] font-bold text-purple-700 uppercase tracking-wider">{{ __('Description') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-24">{{ __('Qty') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-32">{{ __('Unit') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-20">{{ __('Tax %') }}</th>
                                <th class="px-2 py-2 text-right text-[11px] font-bold text-purple-700 uppercase tracking-wider w-28">{{ __('Total') }}</th>
                                <th class="px-2 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="border-t border-purple-100">
                                    <td class="px-2 py-2"><input type="text" x-model="item.description" :name="'items[' + index + '][description]'" class="w-full input-unicorn rounded-lg px-2 py-1 text-sm" required placeholder="{{ __('Item description') }}"></td>
                                    <td class="px-2 py-2"><input type="number" x-model="item.quantity" :name="'items[' + index + '][quantity]'" class="w-full input-unicorn rounded-lg px-2 py-1 text-sm text-right" min="0.01" step="0.01" required></td>
                                    <td class="px-2 py-2"><input type="number" x-model="item.unit_price" :name="'items[' + index + '][unit_price]'" class="w-full input-unicorn rounded-lg px-2 py-1 text-sm text-right" min="0" step="0.01" required></td>
                                    <td class="px-2 py-2"><input type="number" x-model="item.tax_rate" :name="'items[' + index + '][tax_rate]'" class="w-full input-unicorn rounded-lg px-2 py-1 text-sm text-right" min="0" max="100" step="0.01"></td>
                                    <td class="px-2 py-2 text-right text-sm font-bold text-purple-900" x-text="lineTotal(item)"></td>
                                    <td class="px-2 py-2 text-center"><button type="button" @click="removeItem(index)" class="text-rose-400 hover:text-rose-600 text-xl">×</button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-unicorn">
                <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ {{ __('Totals & Notes') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <x-input-label :value="__('Tax Rate (%)')" />
                        <x-text-input name="tax_rate" type="number" :value="old('tax_rate', 0)" min="0" max="100" step="0.01" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Discount Type')" />
                        <select name="discount_type" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="none"    @selected(old('discount_type') === 'none')>{{ __('None') }}</option>
                            <option value="fixed"   @selected(old('discount_type') === 'fixed')>{{ __('Fixed') }}</option>
                            <option value="percent" @selected(old('discount_type') === 'percent')>{{ __('Percent') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Discount Value')" />
                        <x-text-input name="discount_value" type="number" :value="old('discount_value', 0)" min="0" step="0.01" class="mt-1 block w-full" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <x-input-label :value="__('Notes')" />
                        <textarea name="notes" rows="3" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">{{ old('notes') }}</textarea>
                    </div>
                    <div>
                        <x-input-label :value="__('Terms')" />
                        <textarea name="terms" rows="3" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">{{ old('terms') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('recurring.index') }}" class="btn-unicorn-secondary">{{ __('Cancel') }}</a>
                <x-primary-button>✦ {{ __('Create Recurring') }}</x-primary-button>
            </div>
        </form>
    </div>

    @php
        $initialItems = old('items', [['description' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => 0]]);
    @endphp
    <script>
        function recurringForm() {
            return {
                items: {!! json_encode($initialItems) !!},
                addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0, tax_rate: 0 }); },
                removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); },
                lineTotal(item) {
                    const qty = parseFloat(item.quantity) || 0;
                    const price = parseFloat(item.unit_price) || 0;
                    const tax = parseFloat(item.tax_rate) || 0;
                    const subtotal = qty * price;
                    return (subtotal + (subtotal * tax / 100)).toFixed(2);
                }
            };
        }
    </script>
</x-app-layout>

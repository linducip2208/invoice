<x-tabler.layout :title="__('New Recurring')" :pretitle="__('Payments & Finance')">
    <x-slot name="actions">
        <a href="{{ route('recurring.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
    </x-slot>

    <form method="POST" action="{{ route('recurring.store') }}" data-prevent-double-submit x-data="recurringForm()">
        @csrf

        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ __('Schedule') }}</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Client *')" name="client_id">
                            <select id="client_id" name="client_id" required class="form-select @error('client_id') is-invalid @enderror">
                                <option value="">{{ __('Select client…') }}</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }}@if($client->company_name) ({{ $client->company_name }})@endif</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Template')" name="template">
                            <select id="template" name="template" class="form-select">
                                @foreach ($templates as $tpl)
                                    <option value="{{ $tpl->slug }}" @selected(old('template', workspace()?->default_template) == $tpl->slug)>{{ $tpl->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Frequency')" name="frequency">
                            <select id="frequency" name="frequency" required class="form-select">
                                <option value="daily"   @selected(old('frequency') === 'daily')>{{ __('Daily') }}</option>
                                <option value="weekly"  @selected(old('frequency') === 'weekly')>{{ __('Weekly') }}</option>
                                <option value="monthly" @selected(old('frequency', 'monthly') === 'monthly')>{{ __('Monthly') }}</option>
                                <option value="yearly"  @selected(old('frequency') === 'yearly')>{{ __('Yearly') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Interval *')" name="interval_value" :help="__('e.g. Every 2 weeks, Every 3 months')" type="number" :value="old('interval_value', 1)" attrs="min=1 max=365 required" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Start Date *')" name="next_run_date" type="date" :value="old('next_run_date', now()->toDateString())" attrs="required" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('End Date (optional)')" name="end_date" type="date" :value="old('end_date')" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Max Occurrences (optional)')" name="max_occurrences" type="number" :value="old('max_occurrences')" attrs="min=1" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Currency')" name="currency" type="text" :value="old('currency', workspace()?->currency ?? 'IDR')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Items') }}</h3>
                <div class="card-actions">
                    <button type="button" @click="addItem" class="btn btn-sm btn-primary"><x-ti name="plus" /> {{ __('Add Item') }}</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <th class="text-end" style="width: 6rem;">{{ __('Qty') }}</th>
                            <th class="text-end" style="width: 8rem;">{{ __('Unit') }}</th>
                            <th class="text-end" style="width: 5rem;">{{ __('Tax %') }}</th>
                            <th class="text-end" style="width: 7rem;">{{ __('Total') }}</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td><input type="text" x-model="item.description" :name="'items[' + index + '][description]'" class="form-control form-control-sm" required placeholder="{{ __('Item description') }}"></td>
                                <td><input type="number" x-model="item.quantity" :name="'items[' + index + '][quantity]'" class="form-control form-control-sm text-end" min="0.01" step="0.01" required></td>
                                <td><input type="number" x-model="item.unit_price" :name="'items[' + index + '][unit_price]'" class="form-control form-control-sm text-end" min="0" step="0.01" required></td>
                                <td><input type="number" x-model="item.tax_rate" :name="'items[' + index + '][tax_rate]'" class="form-control form-control-sm text-end" min="0" max="100" step="0.01"></td>
                                <td class="text-end fw-bold small" x-text="lineTotal(item)"></td>
                                <td class="text-center"><button type="button" @click="removeItem(index)" class="btn btn-sm btn-link text-danger p-0"><x-ti name="x" /></button></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ __('Totals & Notes') }}</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Tax Rate (%)')" name="tax_rate" type="number" :value="old('tax_rate', 0)" attrs="min=0 max=100 step=0.01" />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Discount Type')" name="discount_type">
                            <select id="discount_type" name="discount_type" class="form-select">
                                <option value="none"    @selected(old('discount_type') === 'none')>{{ __('None') }}</option>
                                <option value="fixed"   @selected(old('discount_type') === 'fixed')>{{ __('Fixed') }}</option>
                                <option value="percent" @selected(old('discount_type') === 'percent')>{{ __('Percent') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Discount Value')" name="discount_value" type="number" :value="old('discount_value', 0)" attrs="min=0 step=0.01" />
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Notes')" name="notes">
                            <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Terms')" name="terms">
                            <textarea id="terms" name="terms" rows="3" class="form-control">{{ old('terms') }}</textarea>
                        </x-tabler.field>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2">
            <a href="{{ route('recurring.index') }}" class="btn">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Create Recurring') }}</button>
        </div>
    </form>

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
</x-tabler.layout>

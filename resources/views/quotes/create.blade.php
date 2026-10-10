<x-tabler.layout :title="__('Quote Baru')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('quotes.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('quotes.store') }}" data-prevent-double-submit x-data='{ items: [{description:"", quantity:1, unit_price:0, tax_rate:0}] }'>
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Klien *')" name="client_id">
                            <select id="client_id" name="client_id" required class="form-select @error('client_id') is-invalid @enderror">
                                <option value="">— {{ __('Pilih klien') }} —</option>
                                @foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Currency *')" name="currency">
                            <select id="currency" name="currency" class="form-select">
                                <option>IDR</option><option>USD</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Issue Date *')" name="issue_date" type="date" :value="now()->toDateString()" attrs="required" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Expiry Date')" name="expiry_date" type="date" :value="now()->addDays(30)->toDateString()" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Tax Rate (%)')" name="tax_rate">
                            <input type="number" step="0.01" id="tax_rate" name="tax_rate" value="0" class="form-control">
                        </x-tabler.field>
                    </div>
                </div>

                <h4 class="card-title mt-4 mb-3">{{ __('Items') }}</h4>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>{{ __('Description') }}</th>
                                <th style="width: 6rem;">{{ __('Qty') }}</th>
                                <th style="width: 9rem;">{{ __('Unit Price') }}</th>
                                <th style="width: 6rem;">{{ __('Tax%') }}</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(it, i) in items" :key="i">
                                <tr>
                                    <td><input type="text" :name="`items[${i}][description]`" x-model="it.description" required class="form-control form-control-sm"></td>
                                    <td><input type="number" step="0.01" :name="`items[${i}][quantity]`" x-model.number="it.quantity" required class="form-control form-control-sm text-end"></td>
                                    <td><input type="number" step="0.01" :name="`items[${i}][unit_price]`" x-model.number="it.unit_price" required class="form-control form-control-sm text-end"></td>
                                    <td><input type="number" step="0.01" :name="`items[${i}][tax_rate]`" x-model.number="it.tax_rate" class="form-control form-control-sm text-end"></td>
                                    <td class="text-center"><button type="button" @click="items.splice(i, 1)" x-show="items.length > 1" class="btn btn-sm btn-link text-danger p-0"><x-ti name="x" /></button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <button type="button" @click="items.push({description:'', quantity:1, unit_price:0, tax_rate:0})" class="btn btn-sm mt-2"><x-ti name="plus" /> {{ __('Add Row') }}</button>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Notes')" name="notes">
                            <textarea id="notes" name="notes" rows="3" class="form-control"></textarea>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Terms')" name="terms">
                            <textarea id="terms" name="terms" rows="3" class="form-control"></textarea>
                        </x-tabler.field>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ route('quotes.index') }}" class="btn">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Create Quote') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

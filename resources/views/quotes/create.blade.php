<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">📋 {{ __('Quote Baru') }}</h2></x-slot>
    <div class="max-w-4xl mx-auto card-unicorn">
        <form method="POST" action="{{ route('quotes.store') }}" x-data='{ items: [{description:"", quantity:1, unit_price:0, tax_rate:0}] }' class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="text-sm font-bold">Klien *</label>
                    <select name="client_id" required class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
                        <option value="">— Pilih klien —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div><label class="text-sm font-bold">Currency *</label>
                    <select name="currency" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full">
                        <option>IDR</option><option>USD</option>
                    </select>
                </div>
                <div><label class="text-sm font-bold">Issue Date *</label><input type="date" name="issue_date" value="{{ now()->toDateString() }}" required class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
                <div><label class="text-sm font-bold">Expiry Date</label><input type="date" name="expiry_date" value="{{ now()->addDays(30)->toDateString() }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
                <div><label class="text-sm font-bold">Tax Rate (%)</label><input type="number" step="0.01" name="tax_rate" value="0" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            </div>

            <h4 class="font-display font-bold mt-4">Items</h4>
            <table class="w-full text-sm">
                <thead><tr><th class="text-left">Description</th><th>Qty</th><th>Unit Price</th><th>Tax%</th><th></th></tr></thead>
                <tbody>
                    <template x-for="(it, i) in items" :key="i">
                        <tr>
                            <td><input type="text" :name="`items[${i}][description]`" x-model="it.description" required class="input-unicorn rounded px-2 py-1 w-full"></td>
                            <td><input type="number" step="0.01" :name="`items[${i}][quantity]`" x-model.number="it.quantity" required class="input-unicorn rounded px-2 py-1 w-20"></td>
                            <td><input type="number" step="0.01" :name="`items[${i}][unit_price]`" x-model.number="it.unit_price" required class="input-unicorn rounded px-2 py-1 w-32"></td>
                            <td><input type="number" step="0.01" :name="`items[${i}][tax_rate]`" x-model.number="it.tax_rate" class="input-unicorn rounded px-2 py-1 w-20"></td>
                            <td><button type="button" @click="items.splice(i, 1)" x-show="items.length > 1" class="text-rose-500">×</button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" @click="items.push({description:'', quantity:1, unit_price:0, tax_rate:0})" class="btn-unicorn-secondary">+ Add Row</button>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="text-sm font-bold">Notes</label><textarea name="notes" rows="3" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></textarea></div>
                <div><label class="text-sm font-bold">Terms</label><textarea name="terms" rows="3" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></textarea></div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('quotes.index') }}" class="btn-unicorn-secondary">Cancel</a>
                <button class="btn-unicorn">✦ Create Quote</button>
            </div>
        </form>
    </div>
</x-app-layout>

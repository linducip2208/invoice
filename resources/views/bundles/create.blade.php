<x-tabler.layout :title="__('Paket Baru')" :pretitle="__('Sales')" :breadcrumbs="[['label' => __('Paket'), 'url' => route('bundles.index')], ['label' => __('Baru')]]">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('bundles.store') }}" x-data="{ items: [{product_id: '', quantity: 1}] }">
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Nama Paket')" name="name" :required="true">
                            <input type="text" id="name" name="name" required value="{{ old('name') }}" class="form-control" placeholder="{{ __('Mis: Paket Wedding Basic') }}">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Harga Paket (opsional)')" name="price" :help="__('Kosongkan = jumlah harga komponen.')">
                            <input type="number" id="price" name="price" min="0" step="0.01" value="{{ old('price') }}" class="form-control" placeholder="0">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <label class="form-check mb-2">
                            <input type="checkbox" name="is_active" value="1" checked class="form-check-input">
                            <span class="form-check-label">{{ __('Aktif') }}</span>
                        </label>
                    </div>
                    <div class="col-12">
                        <x-tabler.field :label="__('Deskripsi')" name="description">
                            <textarea id="description" name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                        </x-tabler.field>
                    </div>
                </div>
                <h3 class="card-title">{{ __('Komponen') }}</h3>
                <template x-for="(row, i) in items" :key="i">
                    <div class="row g-2 mb-2">
                        <div class="col-7">
                            <select :name="'items[' + i + '][product_id]'" x-model="row.product_id" required class="form-select">
                                <option value="">{{ __('— Pilih produk —') }}</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ number_format($p->unit_price, 0) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="number" :name="'items[' + i + '][quantity]'" x-model="row.quantity" min="0.01" step="0.01" required class="form-control" placeholder="Qty">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn w-100" @click="items.splice(i, 1)" x-show="items.length > 1"><x-ti name="x" /></button>
                        </div>
                    </div>
                </template>
                <div class="mb-3"><button type="button" class="btn btn-sm" @click="items.push({product_id: '', quantity: 1})"><x-ti name="plus" /> {{ __('Tambah komponen') }}</button></div>
                <button class="btn btn-primary">{{ __('Simpan Paket') }}</button>
            </form>
        </div>
    </div>
</x-tabler.layout>

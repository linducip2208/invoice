<x-tabler.layout :title="$bundle->name" :pretitle="__('Paket')" :breadcrumbs="[['label' => __('Paket'), 'url' => route('bundles.index')], ['label' => $bundle->name]]">
    <x-slot name="actions">
        <a href="{{ route('bundles.edit', $bundle) }}" class="btn btn-outline-warning"><x-ti name="pencil" /> {{ __('Edit') }}</a>
    </x-slot>

    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('Komponen') }} ({{ $bundle->items->count() }})</h3></div>
                <div class="table-responsive"><table class="table table-vcenter card-table">
                    <thead><tr><th>{{ __('Produk') }}</th><th class="text-end">{{ __('Qty') }}</th><th class="text-end">{{ __('Harga Satuan') }}</th><th class="text-end">{{ __('Subtotal') }}</th></tr></thead>
                    <tbody>
                        @foreach($bundle->items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->product?->name ?? '—' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end text-muted">{{ number_format($item->product?->unit_price ?? 0, 0) }}</td>
                                <td class="text-end fw-bold">{{ number_format($item->quantity * ($item->product?->unit_price ?? 0), 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
                <div class="card-body">
                    <dl class="row g-2 mb-0">
                        <dt class="col-6 text-muted">{{ __('Total komponen') }}</dt><dd class="col-6 text-end">{{ number_format($bundle->components_total, 0) }}</dd>
                        <dt class="col-6 text-muted">{{ __('Harga paket') }}</dt><dd class="col-6 text-end fw-bold">{{ $bundle->price !== null ? number_format($bundle->price, 0) : __('Ikut komponen') }}</dd>
                        @if($bundle->price !== null)
                            <dt class="col-6 text-muted">{{ __('Hemat') }}</dt><dd class="col-6 text-end text-success">{{ number_format(max(0, $bundle->components_total - $bundle->price), 0) }}</dd>
                        @endif
                    </dl>
                    @if($bundle->description)<p class="text-muted small mt-2 mb-0">{{ $bundle->description }}</p>@endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">{{ __('Buat Invoice dari Paket') }}</h3></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bundles.invoice', $bundle) }}">
                        @csrf
                        <x-tabler.field :label="__('Klien')" name="client_id" :required="true">
                            <select id="client_id" name="client_id" required class="form-select">
                                <option value="">{{ __('— Pilih klien —') }}</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}{{ $c->company_name ? ' · ' . $c->company_name : '' }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                        <button class="btn btn-primary w-100"><x-ti name="file-invoice" /> {{ __('Buat Draft Invoice') }}</button>
                        <p class="text-muted small mt-2 mb-0">{{ __('1 baris per komponen, atau 1 baris harga paket bila harga paket diisi.') }}</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

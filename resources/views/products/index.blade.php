<x-tabler.layout :title="__('Products')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Add Product') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <x-tabler.field :label="__('Search')" name="search">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Name, description, category…') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Category')" name="category">
                            <select id="category" name="category" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                @foreach (\App\Models\Product::whereNotNull('category')->distinct()->pluck('category') as $cat)
                                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select id="status" name="status" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                <option value="active" @selected(request('status') === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>{{ __('Inactive') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-12">
                        <div class="btn-list justify-content-end">
                            @if(request()->anyFilled(['search', 'category', 'status']))
                                <a href="{{ route('products.index') }}" class="btn">{{ __('Reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th class="text-end">{{ __('Price') }}</th>
                        <th class="text-end">{{ __('Tax') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-sm"><x-ti name="package" /></span>
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        @if($product->description)
                                            <div class="text-muted small text-truncate" style="max-width: 16rem;">{{ $product->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($product->unit_price, 2) }}</td>
                            <td class="text-end text-muted">{{ $product->tax_rate }}%</td>
                            <td>
                                @if($product->category)
                                    <span class="badge bg-blue-lt">{{ $product->category }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-green">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-red">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                <x-tabler.delete-button :action="route('products.destroy', $product)"
                                    :confirm="__('Delete this product?')" :title="__('Delete')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-tabler.empty icon="package" :title="__('No products yet!')"
                                    :body="__('Add your first sparkly item.')"
                                    :actionUrl="route('products.create')" :actionLabel="__('Add Product')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $products->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

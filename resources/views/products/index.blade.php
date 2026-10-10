<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Products') }} <span class="text-2xl">📦</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Your magical inventory') }}</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn-unicorn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('Add Product') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="card-unicorn mb-5">
            <form method="GET" action="{{ route('products.index') }}" class="grid sm:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2">
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" type="text" :value="request('search')" placeholder="{{ __('Name, description, category…') }}" />
                </div>
                <div>
                    <x-input-label for="category" :value="__('Category')" />
                    <select id="category" name="category" class="input-unicorn rounded-xl px-4 py-2 w-full">
                        <option value="">{{ __('All') }}</option>
                        @foreach (\App\Models\Product::whereNotNull('category')->distinct()->pluck('category') as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="input-unicorn rounded-xl px-4 py-2 w-full">
                        <option value="">{{ __('All') }}</option>
                        <option value="active"   @selected(request('status')==='active')>{{ __('Active') }}</option>
                        <option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="sm:col-span-4 flex justify-end gap-2">
                    @if(request()->anyFilled(['search','category','status']))
                        <a href="{{ route('products.index') }}" class="btn-unicorn-secondary">{{ __('Reset') }}</a>
                    @endif
                    <button type="submit" class="btn-unicorn">✦ {{ __('Filter') }}</button>
                </div>
            </form>
        </div>

        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="overflow-x-auto">
                <table class="table-unicorn">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th class="text-right">{{ __('Price') }}</th>
                            <th class="text-right">{{ __('Tax') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-yellow-100 to-pink-100 flex items-center justify-center text-lg shrink-0">📦</div>
                                        <div>
                                            <div class="font-bold text-purple-900">{{ $product->name }}</div>
                                            @if($product->description)
                                                <div class="text-xs text-purple-400 truncate max-w-xs">{{ $product->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right font-bold text-purple-900">{{ number_format($product->unit_price, 2) }}</td>
                                <td class="text-right text-purple-500">{{ $product->tax_rate }}%</td>
                                <td>@if($product->category)<span class="badge-blue">{{ $product->category }}</span>@else <span class="text-purple-300">—</span>@endif</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge-green">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge-red">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('products.edit', $product) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-sm">{{ __('Edit') }}</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this product?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center sparkles-bg">
                                    <span class="text-6xl block animate-unicorn-float">📦</span>
                                    <p class="mt-3 text-purple-500 font-semibold">{{ __('No products yet!') }}</p>
                                    <p class="text-sm text-purple-400">{{ __('Add your first sparkly item.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Discount Coupons') }} <span class="text-2xl">🎟️</span></h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif

        <details class="card-unicorn !p-6">
            <summary class="cursor-pointer font-display font-bold text-purple-900 flex items-center gap-2">
                <span class="text-pink-400">+</span> {{ __('Buat Coupon Baru') }}
            </summary>
            <form method="POST" action="{{ route('coupons.store') }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label :value="__('Kode (kosong = auto)')" />
                        <x-text-input name="code" class="mt-1 w-full font-mono uppercase" placeholder="HEMAT10" />
                    </div>
                    <div>
                        <x-input-label :value="__('Berlaku untuk')" />
                        <select name="applies_to" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="invoice">Invoice</option>
                            <option value="subscription">Subscription</option>
                            <option value="both">Keduanya</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Tipe Diskon')" />
                        <select name="discount_type" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="percent">% Persen</option>
                            <option value="fixed">Rp Tetap</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label :value="__('Nilai Diskon *')" />
                        <x-text-input name="discount_value" type="number" step="0.01" min="0.01" required class="mt-1 w-full" placeholder="10" />
                    </div>
                    <div>
                        <x-input-label :value="__('Min. Total Invoice')" />
                        <x-text-input name="min_amount" type="number" step="0.01" min="0" class="mt-1 w-full" placeholder="100000" />
                    </div>
                    <div>
                        <x-input-label :value="__('Max Pemakaian')" />
                        <x-text-input name="max_uses" type="number" min="1" class="mt-1 w-full" placeholder="100" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Berlaku sampai')" />
                        <x-text-input name="expires_at" type="datetime-local" class="mt-1 w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Deskripsi')" />
                        <x-text-input name="description" class="mt-1 w-full" placeholder="Promo Lebaran 2026" />
                    </div>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="allow_stack" value="1" class="rounded border-purple-300 text-pink-500 focus:ring-pink-300">
                    <span class="text-sm text-purple-700">Boleh dikombinasi dengan diskon lain</span>
                </label>

                <button type="submit" class="btn-unicorn">+ {{ __('Buat Coupon') }}</button>
            </form>
        </details>

        @if($coupons->isEmpty())
            <div class="card-unicorn !p-12 text-center">
                <p class="text-5xl mb-3">🎟️</p>
                <p class="font-display font-bold text-lg text-purple-700">{{ __('Belum ada coupon') }}</p>
            </div>
        @else
            <div class="card-unicorn !p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-sky-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Kode</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Diskon</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Berlaku</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Pemakaian</th>
                                <th class="px-4 py-3 text-left text-[11px] font-bold text-purple-700 uppercase">Expires</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupons as $c)
                                <tr class="border-t border-purple-100 hover:bg-purple-50/40">
                                    <td class="px-4 py-3">
                                        <code class="font-mono text-pink-600 font-bold bg-pink-50 px-2 py-0.5 rounded">{{ $c->code }}</code>
                                        @if($c->description)<p class="text-xs text-purple-500 mt-1">{{ $c->description }}</p>@endif
                                    </td>
                                    <td class="px-4 py-3 font-bold text-purple-900">
                                        @if($c->discount_type === 'percent') {{ number_format($c->discount_value, 0) }}%
                                        @else {{ number_format($c->discount_value, 0) }} {{ $c->currency ?? 'IDR' }} @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-purple-600">{{ ucfirst($c->applies_to) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $c->uses_count }}@if($c->max_uses) / {{ $c->max_uses }}@endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-purple-500">{{ $c->expires_at?->format('d M Y') ?? '— No expiry' }}</td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <form method="POST" action="{{ route('coupons.toggle', $c) }}" class="inline">@csrf
                                            <button type="submit" class="text-xs font-semibold {{ $c->is_active ? 'text-emerald-600' : 'text-gray-400' }} hover:underline">
                                                {{ $c->is_active ? '● Active' : '○ Inactive' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('coupons.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-rose-500 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

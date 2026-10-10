<x-tabler.layout :title="__('Discount Coupons')" :pretitle="__('Workspace')">
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="plus" /> {{ __('Buat Coupon Baru') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('coupons.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Kode (kosong = auto)')" name="code" type="text" :value="old('code')" attrs='placeholder="HEMAT10" class="font-monospace text-uppercase"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Berlaku untuk')" name="applies_to">
                            <select id="applies_to" name="applies_to" required class="form-select">
                                <option value="invoice">{{ __('Invoice') }}</option>
                                <option value="subscription">{{ __('Subscription') }}</option>
                                <option value="both">{{ __('Keduanya') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Tipe Diskon')" name="discount_type">
                            <select id="discount_type" name="discount_type" required class="form-select">
                                <option value="percent">{{ __('% Persen') }}</option>
                                <option value="fixed">{{ __('Rp Tetap') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Nilai Diskon *')" name="discount_value" type="number" :value="old('discount_value')" attrs='step="0.01" min="0.01" required placeholder="10"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Min. Total Invoice')" name="min_amount" type="number" :value="old('min_amount')" attrs='step="0.01" min="0" placeholder="100000"' />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Max Pemakaian')" name="max_uses" type="number" :value="old('max_uses')" attrs='min="1" placeholder="100"' />
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Berlaku sampai')" name="expires_at">
                            <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" class="form-control @error('expires_at') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Deskripsi')" name="description" type="text" :value="old('description')" attrs='placeholder="Promo Lebaran 2026"' />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="allow_stack" value="1" class="form-check-input">
                        <span class="form-check-label">{{ __('Boleh dikombinasi dengan diskon lain') }}</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Buat Coupon') }}</button>
            </form>
        </div>
    </div>

    <div class="card">
        @if($coupons->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="percentage" :title="__('Belum ada coupon')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Kode') }}</th>
                            <th>{{ __('Diskon') }}</th>
                            <th>{{ __('Berlaku') }}</th>
                            <th>{{ __('Pemakaian') }}</th>
                            <th>{{ __('Expires') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $c)
                            <tr>
                                <td>
                                    <code>{{ $c->code }}</code>
                                    @if($c->description)
                                        <div class="text-muted small">{{ $c->description }}</div>
                                    @endif
                                </td>
                                <td class="fw-bold">
                                    @if($c->discount_type === 'percent')
                                        {{ number_format($c->discount_value, 0) }}%
                                    @else
                                        {{ number_format($c->discount_value, 0) }} {{ $c->currency ?? 'IDR' }}
                                    @endif
                                </td>
                                <td class="text-muted">{{ ucfirst($c->applies_to) }}</td>
                                <td>{{ $c->uses_count }}@if($c->max_uses) / {{ $c->max_uses }}@endif</td>
                                <td class="text-muted small">{{ $c->expires_at?->format('d M Y') ?? '— ' . __('No expiry') }}</td>
                                <td class="text-end text-nowrap">
                                    <form method="POST" action="{{ route('coupons.toggle', $c) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $c->is_active ? 'btn-outline-success' : '' }}">{{ $c->is_active ? __('Active') : __('Inactive') }}</button>
                                    </form>
                                    <x-tabler.delete-button :action="route('coupons.destroy', $c)" :confirm="__('Hapus?')" :label="__('Hapus')" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-tabler.layout>

<x-tabler.admin-layout :title="__('Coupons')" :pretitle="__('Superadmin')">
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('New Coupon') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.coupons.store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Code')" name="code" required>
                            <input type="text" id="code" name="code" required placeholder="LAUNCH50"
                                class="form-control text-uppercase @error('code') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Discount type')" name="discount_type" required>
                            <select id="discount_type" name="discount_type" required class="form-select @error('discount_type') is-invalid @enderror">
                                <option value="percent">{{ __('Percent (%)') }}</option>
                                <option value="fixed">{{ __('Fixed') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Value')" name="discount_value" required>
                            <input type="number" step="0.01" id="discount_value" name="discount_value" required placeholder="{{ __('Value') }}"
                                class="form-control @error('discount_value') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Plan')" name="plan_id">
                            <select id="plan_id" name="plan_id" class="form-select @error('plan_id') is-invalid @enderror">
                                <option value="">{{ __('All plans') }}</option>
                                @foreach($plans as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Max uses')" name="max_uses">
                            <input type="number" id="max_uses" name="max_uses" placeholder="{{ __('Max uses') }}"
                                class="form-control @error('max_uses') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field :label="__('Expires at')" name="expires_at">
                            <input type="datetime-local" id="expires_at" name="expires_at"
                                class="form-control @error('expires_at') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Description')" name="description">
                            <input type="text" id="description" name="description" placeholder="{{ __('Description') }}"
                                class="form-control @error('description') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <label class="form-check form-switch mt-4">
                            <input type="checkbox" name="is_active" value="1" checked class="form-check-input">
                            <span class="form-check-label">{{ __('Active') }}</span>
                        </label>
                    </div>
                    <div class="col-md-3">
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <x-ti name="plus" /> {{ __('Create') }}
                            </button>
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
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('Plan') }}</th>
                        <th class="text-center">{{ __('Uses') }}</th>
                        <th>{{ __('Expires') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $c)
                        <tr>
                            <td><code class="fw-bold">{{ $c->code }}</code></td>
                            <td class="text-muted">{{ $c->discount_type === 'percent' ? $c->discount_value . '%' : 'Rp' . number_format($c->discount_value, 0, ',', '.') }}</td>
                            <td class="text-muted">{{ $c->plan?->name ?? __('All') }}</td>
                            <td class="text-center"><span class="badge bg-blue-lt">{{ $c->uses_count }}/{{ $c->max_uses ?? '∞' }}</span></td>
                            <td class="text-muted">{{ $c->expires_at?->format('d M Y') ?? __('Never') }}</td>
                            <td>
                                @if($c->is_active)
                                    <span class="badge bg-green-lt">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-yellow-lt">{{ __('Disabled') }}</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $c) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm">{{ $c->is_active ? __('Disable') : __('Enable') }}</button>
                                </form>
                                <x-tabler.delete-button :action="route('admin.coupons.destroy', $c)"
                                    :confirm="__('Delete this coupon?')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-tabler.empty icon="percentage" :title="__('No coupons yet')"
                                    :body="__('Create your first coupon above.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $coupons->links() }}</div>
        @endif
    </div>
</x-tabler.admin-layout>

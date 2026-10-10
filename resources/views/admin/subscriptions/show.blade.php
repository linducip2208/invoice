<x-tabler.layout :title="__('Subscription') . ' #' . $subscription->id" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <a href="{{ route('admin.subscriptions.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Subscriptions') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title">{{ $subscription->workspace?->name }} — {{ $subscription->plan?->name }}</h3>
            <div class="d-flex gap-2 flex-wrap align-items-center mt-2">
                <x-tabler.status-badge :status="$subscription->status" />
                <span class="badge bg-blue-lt">{{ ucfirst($subscription->billing_period) }}</span>
                <span class="badge bg-secondary-lt">{{ $subscription->currency }}</span>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('Override Subscription') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}">
                @csrf @method('PUT')
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Plan')" name="plan_id">
                            <select id="plan_id" name="plan_id" class="form-select @error('plan_id') is-invalid @enderror">
                                @foreach(\App\Models\Plan::orderBy('sort_order')->get() as $p)
                                    <option value="{{ $p->id }}" @selected($subscription->plan_id == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach(['trial','active','past_due','cancelled','expired'] as $s)
                                    <option value="{{ $s }}" @selected($subscription->status === $s)>{{ __(ucfirst($s)) }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Period')" name="billing_period">
                            <select id="billing_period" name="billing_period" class="form-select @error('billing_period') is-invalid @enderror">
                                @foreach(['free','monthly','yearly'] as $p)
                                    <option value="{{ $p }}" @selected($subscription->billing_period === $p)>{{ __(ucfirst($p)) }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Currency')" name="currency">
                            <select id="currency" name="currency" class="form-select @error('currency') is-invalid @enderror">
                                <option value="IDR" @selected($subscription->currency === 'IDR')>IDR</option>
                                <option value="USD" @selected($subscription->currency === 'USD')>USD</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Period ends at')" name="current_period_ends_at">
                            <input type="date" id="current_period_ends_at" name="current_period_ends_at" value="{{ $subscription->current_period_ends_at?->format('Y-m-d') }}"
                                class="form-control @error('current_period_ends_at') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('Save Override') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($subscription->invoices->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Billing History') }}</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th class="text-end">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscription->invoices as $inv)
                            <tr>
                                <td class="text-muted">{{ $inv->paid_at?->format('d M Y') ?? $inv->created_at->format('d M Y') }}</td>
                                <td><code>{{ $inv->reference_no }}</code></td>
                                <td class="text-end fw-semibold">{{ $inv->currency }} {{ number_format($inv->amount, 0) }}</td>
                                <td class="text-end"><x-tabler.status-badge :status="$inv->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-tabler.layout>

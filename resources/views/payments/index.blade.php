<x-tabler.layout :title="__('Payments')" :pretitle="__('Payments & Finance')">

    <!-- Summary -->
    <div class="row g-3 mb-3">
        <x-tabler.stat :label="__('Total Collected')" :value="(workspace()?->currency ?? 'IDR') . ' ' . number_format($totalPaid, 2)" icon="cash" />
        <x-tabler.stat :label="__('Payments')" :value="$count" icon="credit-card" />
        <x-tabler.stat :label="__('Avg Payment')" :value="$count ? number_format($totalPaid / $count, 2) : '0.00'" icon="chart-bar" />
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('payments.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Search')" name="search">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Reference / invoice / client') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('Metode')" name="method">
                            <select id="method" name="method" class="form-select">
                                <option value="">{{ __('Semua') }}</option>
                                @foreach($methods as $key => $label)
                                    <option value="{{ $key }}" @selected(request('method')===$key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('From')" name="date_from">
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field :label="__('To')" name="date_to">
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-12">
                        <div class="btn-list justify-content-end">
                            @if(request()->anyFilled(['search','method','date_from','date_to']))
                                <a href="{{ route('payments.index') }}" class="btn">{{ __('Reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Invoice') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Method') }}</th>
                        <th>{{ __('Reference') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="text-nowrap fw-medium">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="text-nowrap">
                                @if($payment->invoice)
                                    <a href="{{ route('invoices.show', $payment->invoice) }}" class="fw-semibold">{{ $payment->invoice->invoice_no }}</a>
                                @else — @endif
                            </td>
                            <td class="text-muted">{{ $payment->invoice?->client?->name ?? '—' }}</td>
                            <td class="text-nowrap fw-bold text-success">{{ number_format($payment->amount, 2) }}</td>
                            <td class="text-nowrap">
                                <span class="badge bg-purple-lt">
                                    @if($payment->paymentGateway)
                                        {{ $payment->paymentGateway->getChannelLabel($payment->channel) }}
                                    @else
                                        {{ ucwords(str_replace(['_','-'], ' ', $payment->payment_method)) }}
                                    @endif
                                </span>
                            </td>
                            <td class="text-muted">{{ $payment->reference_no ?: '—' }}</td>
                            <td class="text-end text-nowrap">
                                <x-tabler.delete-button :action="route('payments.destroy', $payment)"
                                    :confirm="__('Delete this payment?')" :title="__('Delete')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-tabler.empty icon="credit-card" :title="__('No payments recorded yet.')"
                                    :body="__('Payments appear here once you record them on individual invoices.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $payments->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

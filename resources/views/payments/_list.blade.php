@props(['invoice'])

<div class="table-responsive">
    <table class="table table-vcenter card-table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Reference') }}</th>
                <th>{{ __('Notes') }}</th>
                <th class="text-end">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoice->payments as $payment)
                <tr>
                    <td class="text-nowrap">{{ $payment->payment_date->format('M d, Y') }}</td>
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
                    <td class="text-nowrap text-muted">{{ $payment->reference_no ?: '—' }}</td>
                    <td class="text-muted text-truncate" style="max-width: 12rem;">{{ $payment->notes ?: '—' }}</td>
                    <td class="text-nowrap text-end">
                        <x-tabler.delete-button :action="route('payments.destroy', $payment)"
                            :confirm="__('Delete this payment?')" :title="__('Delete')" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-tabler.empty icon="credit-card" :title="__('No payments recorded yet.')" />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

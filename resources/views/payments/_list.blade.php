@props(['invoice'])

<div class="overflow-x-auto rounded-xl">
    <table class="table-unicorn">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Reference') }}</th>
                <th>{{ __('Notes') }}</th>
                <th class="text-right"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoice->payments as $payment)
                <tr>
                    <td class="whitespace-nowrap text-purple-700">{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td class="whitespace-nowrap font-bold text-green-600">{{ number_format($payment->amount, 2) }}</td>
                    <td class="whitespace-nowrap">
                        <span class="badge-purple">
                            @if($payment->paymentGateway)
                                ⚡ {{ $payment->paymentGateway->getChannelLabel($payment->channel) }}
                            @else
                                {{ ucwords(str_replace(['_','-'], ' ', $payment->payment_method)) }}
                            @endif
                        </span>
                    </td>
                    <td class="whitespace-nowrap text-purple-500">{{ $payment->reference_no ?: '—' }}</td>
                    <td class="text-purple-500 max-w-xs truncate">{{ $payment->notes ?: '—' }}</td>
                    <td class="whitespace-nowrap text-right">
                        <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('{{ __('Delete this payment?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-sm">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <span class="text-3xl">💸</span>
                        <p class="mt-2 text-sm text-purple-400">{{ __('No payments recorded yet.') }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

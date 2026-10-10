<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Payments') }} <span class="text-2xl">💖</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Every sparkle of incoming gold') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="card-unicorn">
                <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">{{ __('Total Collected') }}</p>
                <p class="font-display text-2xl font-bold text-green-600 mt-1">
                    <span class="text-sm text-purple-400 font-medium mr-1">{{ workspace()?->currency ?? 'IDR' }}</span>
                    {{ number_format($totalPaid, 2) }}
                </p>
            </div>
            <div class="card-unicorn">
                <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">{{ __('Payments') }}</p>
                <p class="font-display text-2xl font-bold text-purple-900 mt-1">{{ $count }}</p>
            </div>
            <div class="card-unicorn">
                <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider">{{ __('Avg Payment') }}</p>
                <p class="font-display text-2xl font-bold text-pink-500 mt-1">
                    {{ $count ? number_format($totalPaid / $count, 2) : '0.00' }}
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card-unicorn mb-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" type="text" :value="request('search')" placeholder="{{ __('Reference / invoice / client') }}" />
                </div>
                <div>
                    <x-input-label for="method" :value="__('Metode')" />
                    <select id="method" name="method" class="input-unicorn rounded-xl px-4 py-2 w-full">
                        <option value="">{{ __('Semua') }}</option>
                        @foreach($methods as $key => $label)
                            <option value="{{ $key }}" @selected(request('method')===$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="date_from" :value="__('From')" />
                    <x-text-input id="date_from" name="date_from" type="date" :value="request('date_from')" />
                </div>
                <div>
                    <x-input-label for="date_to" :value="__('To')" />
                    <x-text-input id="date_to" name="date_to" type="date" :value="request('date_to')" />
                </div>
                <div class="col-span-full flex justify-end gap-2">
                    <a href="{{ route('payments.index') }}" class="btn-unicorn-secondary">{{ __('Reset') }}</a>
                    <button type="submit" class="btn-unicorn">✦ {{ __('Filter') }}</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="overflow-x-auto">
                <table class="table-unicorn">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice') }}</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="whitespace-nowrap text-purple-700 font-medium">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="whitespace-nowrap">
                                    @if($payment->invoice)
                                        <a href="{{ route('invoices.show', $payment->invoice) }}" class="font-bold text-purple-700 hover:text-pink-500">{{ $payment->invoice->invoice_no }}</a>
                                    @else — @endif
                                </td>
                                <td class="text-purple-700">{{ $payment->invoice?->client?->name ?? '—' }}</td>
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
                                <td class="text-purple-500">{{ $payment->reference_no ?: '—' }}</td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('{{ __('Delete this payment?') }}')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center sparkles-bg">
                                    <span class="text-5xl block animate-unicorn-float">💸</span>
                                    <p class="mt-3 text-purple-500 font-semibold">{{ __('No payments recorded yet.') }}</p>
                                    <p class="text-xs text-purple-400 mt-1">{{ __('Payments appear here once you record them on individual invoices.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </div>
</x-app-layout>

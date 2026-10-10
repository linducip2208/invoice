<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Invoices') }} <span class="text-2xl">📜</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Every magical bill, in one place') }}</p>
            </div>
            <a href="{{ route('invoices.create') }}" class="btn-unicorn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('Create Invoice') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="card-unicorn mb-5">
            <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <x-input-label :value="__('Status')" />
                    <select name="status" class="input-unicorn rounded-xl px-4 py-2 w-full">
                        <option value="">{{ __('All') }}</option>
                        @foreach(['draft','sent','paid','partial','overdue','cancelled'] as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-input-label :value="__('Search')" />
                    <x-text-input name="search" type="text" :value="request('search')" placeholder="{{ __('Invoice # or client…') }}" />
                </div>
                <div>
                    <x-input-label :value="__('From')" />
                    <x-text-input name="date_from" type="date" :value="request('date_from')" />
                </div>
                <div>
                    <x-input-label :value="__('To')" />
                    <x-text-input name="date_to" type="date" :value="request('date_to')" />
                </div>
                <div class="col-span-full flex justify-end gap-2">
                    @if(request()->anyFilled(['status','search','date_from','date_to']))
                        <a href="{{ route('invoices.index') }}" class="btn-unicorn-secondary">{{ __('Reset') }}</a>
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
                            <th>{{ __('Invoice #') }}</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Issue') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th class="text-right">{{ __('Total') }}</th>
                            <th class="text-right">{{ __('Balance') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td><a href="{{ route('invoices.show', $invoice) }}" class="font-bold text-purple-700 hover:text-pink-500">{{ $invoice->invoice_no }}</a></td>
                                <td class="text-purple-700">{{ $invoice->client?->name ?? '—' }}</td>
                                <td class="text-purple-500">{{ $invoice->issue_date?->format('M d, Y') }}</td>
                                <td class="text-purple-500">{{ $invoice->due_date?->format('M d, Y') }}</td>
                                <td class="text-right font-bold text-purple-900">{{ number_format($invoice->total, 2) }}</td>
                                <td class="text-right font-bold {{ $invoice->balance_due > 0 ? 'text-pink-600' : 'text-green-600' }}">{{ number_format($invoice->balance_due, 2) }}</td>
                                <td class="text-center"><span class="badge-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span></td>
                                <td class="text-right space-x-2.5 whitespace-nowrap">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-purple-600 hover:text-pink-500 font-semibold text-sm">{{ __('View') }}</a>
                                    @if($invoice->status === 'draft')
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-sm">{{ __('Edit') }}</a>
                                    @endif
                                    <a href="{{ route('invoices.pdf', $invoice) }}" class="text-purple-500 hover:text-purple-700 font-semibold text-sm">{{ __('PDF') }}</a>
                                    @if($invoice->status === 'draft')
                                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this invoice?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm">{{ __('Delete') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center sparkles-bg">
                                    <span class="text-6xl block animate-unicorn-float">📜</span>
                                    <p class="mt-3 text-purple-500 font-semibold">{{ __('No invoices found.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $invoices->links() }}</div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Recurring Invoices') }} <span class="text-2xl">🔄</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Magic that bills itself, on schedule') }}</p>
            </div>
            <a href="{{ route('recurring.create') }}" class="btn-unicorn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                {{ __('New Recurring') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="card-unicorn mb-5">
            <form method="GET" action="{{ route('recurring.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <x-input-label :value="__('Status')" />
                    <select name="status" class="input-unicorn rounded-xl px-4 py-2">
                        <option value="">{{ __('All') }}</option>
                        <option value="active"    @selected(request('status')==='active')>{{ __('Active') }}</option>
                        <option value="paused"    @selected(request('status')==='paused')>{{ __('Paused') }}</option>
                        <option value="completed" @selected(request('status')==='completed')>{{ __('Completed') }}</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    @if(request('status'))
                        <a href="{{ route('recurring.index') }}" class="btn-unicorn-secondary">{{ __('Reset') }}</a>
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
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Frequency') }}</th>
                            <th>{{ __('Next Run') }}</th>
                            <th class="text-center">{{ __('Occurrences') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recurringInvoices as $ri)
                            <tr>
                                <td class="font-bold text-purple-900">{{ $ri->client?->name ?? '—' }}</td>
                                <td class="text-purple-700">{{ __('Every') }} {{ $ri->interval_value }} {{ __($ri->frequency) }}{{ $ri->interval_value > 1 ? 's' : '' }}</td>
                                <td class="text-purple-500">{{ $ri->next_run_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="text-center text-purple-700">{{ $ri->current_occurrence }}@if($ri->max_occurrences) / {{ $ri->max_occurrences }}@endif</td>
                                <td class="text-center">
                                    @php $sc = match($ri->status) { 'active'=>'green','paused'=>'yellow', default=>'gray' }; @endphp
                                    <span class="badge-{{ $sc }}">{{ ucfirst($ri->status) }}</span>
                                </td>
                                <td class="text-right space-x-3 whitespace-nowrap">
                                    <form action="{{ route('recurring.toggle', $ri) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-{{ $ri->status === 'active' ? 'amber' : 'green' }}-600 hover:text-{{ $ri->status === 'active' ? 'amber' : 'green' }}-700 font-semibold text-sm">
                                            {{ $ri->status === 'active' ? '⏸ '.__('Pause') : '▶ '.__('Resume') }}
                                        </button>
                                    </form>
                                    <a href="{{ route('recurring.edit', $ri) }}" class="text-purple-600 hover:text-pink-500 font-semibold text-sm">{{ __('Edit') }}</a>
                                    <form action="{{ route('recurring.destroy', $ri) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Delete this recurring invoice?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center sparkles-bg">
                                    <span class="text-6xl block animate-unicorn-float">🔄</span>
                                    <p class="mt-3 text-purple-500 font-semibold">{{ __('No recurring invoices yet.') }}</p>
                                    <p class="text-sm text-purple-400">{{ __('Set one up to bill clients automatically.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $recurringInvoices->links() }}</div>
    </div>
</x-app-layout>

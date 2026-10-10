<x-tabler.layout :title="__('Recurring Invoices')" :pretitle="__('Payments & Finance')">
    <x-slot name="actions">
        <a href="{{ route('recurring.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('New Recurring') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('recurring.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select id="status" name="status" class="form-select">
                                <option value="">{{ __('All') }}</option>
                                <option value="active"    @selected(request('status')==='active')>{{ __('Active') }}</option>
                                <option value="paused"    @selected(request('status')==='paused')>{{ __('Paused') }}</option>
                                <option value="completed" @selected(request('status')==='completed')>{{ __('Completed') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-list justify-content-end">
                            @if(request('status'))
                                <a href="{{ route('recurring.index') }}" class="btn">{{ __('Reset') }}</a>
                            @endif
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
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
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Frequency') }}</th>
                        <th>{{ __('Next Run') }}</th>
                        <th class="text-center">{{ __('Occurrences') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recurringInvoices as $ri)
                        <tr>
                            <td class="fw-bold">{{ $ri->client?->name ?? '—' }}</td>
                            <td class="text-muted">{{ __('Every') }} {{ $ri->interval_value }} {{ __($ri->frequency) }}{{ $ri->interval_value > 1 ? 's' : '' }}</td>
                            <td class="text-muted">{{ $ri->next_run_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="text-center">{{ $ri->current_occurrence }}@if($ri->max_occurrences) / {{ $ri->max_occurrences }}@endif</td>
                            <td class="text-center">
                                <x-tabler.status-badge :status="$ri->status" />
                            </td>
                            <td class="text-end text-nowrap">
                                <form action="{{ route('recurring.toggle', $ri) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm {{ $ri->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        {{ $ri->status === 'active' ? __('Pause') : __('Resume') }}
                                    </button>
                                </form>
                                <a href="{{ route('recurring.edit', $ri) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                <x-tabler.delete-button :action="route('recurring.destroy', $ri)"
                                    :confirm="__('Delete this recurring invoice?')" :title="__('Delete')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-tabler.empty icon="repeat" :title="__('No recurring invoices yet.')"
                                    :body="__('Set one up to bill clients automatically.')"
                                    :actionUrl="route('recurring.create')" :actionLabel="__('New Recurring')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($recurringInvoices->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $recurringInvoices->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

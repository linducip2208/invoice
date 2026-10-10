<x-tabler.layout :title="__('Quotes / Estimates')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('quotes.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Quote Baru') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('No') }}</th>
                        <th>{{ __('Klien') }}</th>
                        <th>{{ __('Issue') }}</th>
                        <th>{{ __('Expiry') }}</th>
                        <th class="text-end">{{ __('Total') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotes as $q)
                        <tr>
                            <td class="fw-bold"><a href="{{ route('quotes.show', $q) }}">{{ $q->quote_no }}</a></td>
                            <td class="text-muted">{{ $q->client?->name }}</td>
                            <td class="text-muted">{{ $q->issue_date?->format('d M Y') }}</td>
                            <td class="text-muted">{{ $q->expiry_date?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end fw-bold">{{ $q->currency }} {{ number_format($q->total, 2) }}</td>
                            <td class="text-center"><x-tabler.status-badge :status="$q->status" /></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('quotes.show', $q) }}" class="btn btn-sm">{{ __('View') }}</a>
                                @if($q->status !== 'converted')
                                    <form method="POST" action="{{ route('quotes.convert', $q) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">{{ __('Invoice') }}</button></form>
                                @endif
                                <x-tabler.delete-button :action="route('quotes.destroy', $q)"
                                    :confirm="__('Hapus?')" :title="__('Delete')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-tabler.empty icon="file-text" :title="__('Belum ada quote.')"
                                    :actionUrl="route('quotes.create')" :actionLabel="__('Bikin yang pertama')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($quotes->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $quotes->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

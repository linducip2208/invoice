<x-tabler.layout :title="__('Accounting Exports')" :pretitle="__('Finance')">
    <x-slot name="actions">
        <a href="{{ route('accounting-exports.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Generate Export') }}
        </a>
    </x-slot>

    <div class="card">
        @if($exports->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="file-export" :title="__('Belum ada export')" :actionUrl="route('accounting-exports.create')" :actionLabel="__('Generate Export')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Format') }}</th>
                            <th>{{ __('Periode') }}</th>
                            <th class="text-end">{{ __('Records') }}</th>
                            <th>{{ __('Generated') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exports as $e)
                            <tr>
                                <td><span class="badge bg-blue-lt">{{ ucfirst($e->format) }}</span></td>
                                <td class="text-muted">{{ $e->period_start->format('d M Y') }} - {{ $e->period_end->format('d M Y') }}</td>
                                <td class="text-end fw-bold">{{ $e->record_count }}</td>
                                <td class="text-muted small">{{ $e->created_at->diffForHumans() }} · {{ $e->generator?->name }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('accounting-exports.download', $e) }}" class="btn btn-sm"><x-ti name="download" /> {{ __('Download') }}</a>
                                    <x-tabler.delete-button :action="route('accounting-exports.destroy', $e)" :confirm="__('Hapus?')" :label="__('Hapus')" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($exports->hasPages())
                <div class="card-footer d-flex justify-content-center">{{ $exports->links() }}</div>
            @endif
        @endif
    </div>
    <p class="text-muted small mt-2">{{ __('Export ke Jurnal/QuickBooks/Xero/CSV generic') }}</p>
</x-tabler.layout>

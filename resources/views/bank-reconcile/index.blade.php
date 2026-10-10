<x-tabler.layout :title="__('Bank Reconcile')" :pretitle="__('Finance')">
    <x-slot name="actions">
        <a href="{{ route('bank-reconcile.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('Upload CSV') }}
        </a>
    </x-slot>

    <p class="text-muted small mb-3">{{ __('Upload mutasi bank → auto-match invoice') }}</p>

    <div class="card">
        @if($batches->isEmpty())
            <div class="card-body">
                <x-tabler.empty icon="building-bank" :title="__('Belum ada batch reconcile')" :actionUrl="route('bank-reconcile.create')" :actionLabel="__('Upload CSV')" />
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('Bank') }}</th>
                            <th>{{ __('Akun') }}</th>
                            <th>{{ __('Tgl') }}</th>
                            <th class="text-end">{{ __('Lines') }}</th>
                            <th class="text-end">{{ __('Matched') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $b)
                            <tr>
                                <td class="fw-bold">{{ $b->bank_name }}</td>
                                <td class="text-muted font-monospace">{{ $b->account_no }}</td>
                                <td class="text-muted small">{{ $b->created_at->format('d M Y H:i') }}</td>
                                <td class="text-end fw-bold">{{ $b->total_lines }}</td>
                                <td class="text-end text-success fw-bold">{{ $b->matched_count }} / {{ $b->total_lines }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $b->status === 'completed' ? 'success' : ($b->status === 'review' ? 'warning' : 'secondary') }}">{{ ucfirst($b->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('bank-reconcile.show', $b) }}" class="btn btn-sm">{{ __('Detail') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($batches->hasPages())
                <div class="card-footer d-flex justify-content-center">{{ $batches->links() }}</div>
            @endif
        @endif
    </div>
</x-tabler.layout>

<x-tabler.layout :title="__('Paket / Bundel')" :pretitle="__('Sales')">
    <x-slot name="actions">
        <a href="{{ route('bundles.create') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Paket Baru') }}</a>
    </x-slot>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>{{ __('Nama') }}</th><th>{{ __('Komponen') }}</th><th class="text-end">{{ __('Harga Paket') }}</th><th class="text-end">{{ __('Total Komponen') }}</th><th class="text-center">{{ __('Status') }}</th><th class="text-end">{{ __('Actions') }}</th></tr></thead>
                <tbody>
                    @forelse($bundles as $b)
                        <tr>
                            <td><a href="{{ route('bundles.show', $b) }}" class="fw-semibold">{{ $b->name }}</a><div class="text-muted small">{{ $b->description }}</div></td>
                            <td class="text-muted">{{ $b->items->count() }} item</td>
                            <td class="text-end fw-bold">{{ $b->price !== null ? number_format($b->price, 0) : '—' }}</td>
                            <td class="text-end text-muted">{{ number_format($b->components_total, 0) }}</td>
                            <td class="text-center">@if($b->is_active)<span class="badge bg-success-lt">{{ __('Aktif') }}</span>@else<span class="badge bg-secondary-lt">{{ __('Nonaktif') }}</span>@endif</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('bundles.show', $b) }}" class="btn btn-sm">{{ __('View') }}</a>
                                <a href="{{ route('bundles.edit', $b) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                <x-tabler.delete-button :action="route('bundles.destroy', $b)" :confirm="__('Hapus paket ini?')" :title="__('Delete')" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-tabler.empty icon="package" :title="__('Belum ada paket.')" :body="__('Gabungkan beberapa produk jadi satu paket jual.')" :actionUrl="route('bundles.create')" :actionLabel="__('Buat Paket')" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bundles->hasPages())<div class="card-footer d-flex justify-content-center">{{ $bundles->links() }}</div>@endif
    </div>
</x-tabler.layout>

<x-tabler.layout :title="__('Expenses')" :pretitle="__('Finance')">
    <x-slot name="actions">
        <a href="{{ route('expenses.ocr.upload') }}" class="btn btn-primary">
            <x-ti name="scan" /> {{ __('OCR Receipt') }}
        </a>
    </x-slot>

    <div class="row row-decks row-cards mb-3">
        <x-tabler.stat :label="__('Total Expenses')" :value="number_format($totalAmount, 2)" icon="receipt" />
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="plus" /> {{ __('Catat Expense') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-2">
                    <div class="col-md-2">
                        <x-tabler.field name="expense_date" type="date" :value="now()->toDateString()" attrs="required" />
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field name="category" type="text" attrs='placeholder="Kategori"' />
                    </div>
                    <div class="col-md-3">
                        <x-tabler.field name="description" type="text" attrs='required placeholder="Deskripsi"' />
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field name="amount" type="number" attrs='step="0.01" min="0" required placeholder="Jumlah"' />
                    </div>
                    <div class="col-md-1">
                        <x-tabler.field name="currency">
                            <select id="currency" name="currency" class="form-select"><option>IDR</option><option>USD</option></select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <x-tabler.field name="client_id">
                            <select id="client_id" name="client_id" class="form-select">
                                <option value="">— {{ __('Klien (opsional, untuk billable)') }} —</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <x-tabler.field name="receipt">
                            <input type="file" id="receipt" name="receipt" accept="image/*" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <label class="form-check">
                            <input type="checkbox" name="billable" value="1" class="form-check-input">
                            <span class="form-check-label">{{ __('Billable') }}</span>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-list justify-content-end">
                            <button class="btn btn-primary">{{ __('Simpan') }}</button>
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
                        <th>{{ __('Tanggal') }}</th>
                        <th>{{ __('Kategori') }}</th>
                        <th>{{ __('Deskripsi') }}</th>
                        <th>{{ __('Klien') }}</th>
                        <th class="text-end">{{ __('Jumlah') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                        <tr>
                            <td class="text-muted">{{ $e->expense_date?->format('d M Y') }}</td>
                            <td><span class="badge bg-blue-lt">{{ $e->category ?? '—' }}</span></td>
                            <td>{{ $e->description }}</td>
                            <td class="text-muted">{{ $e->client?->name ?? '—' }}</td>
                            <td class="text-end fw-bold">{{ $e->currency }} {{ number_format($e->amount, 2) }}</td>
                            <td class="text-end">
                                <x-tabler.delete-button :action="route('expenses.destroy', $e)" :confirm="__('Hapus?')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-tabler.empty icon="receipt" :title="__('Belum ada expense.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $expenses->links() }}</div>
        @endif
    </div>
</x-tabler.layout>

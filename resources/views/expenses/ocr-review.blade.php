<x-tabler.layout :title="__('Review Hasil OCR')" :pretitle="__('Finance')">
    <x-slot name="actions">
        <a href="{{ route('expenses.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
    </x-slot>

    <div class="row row-decks row-cards">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><x-ti name="photo" /> {{ __('Receipt') }}</h3>
                </div>
                <div class="card-body">
                    @if($expense->receipt_path)
                        <img src="{{ asset('storage/' . $expense->receipt_path) }}" alt="Receipt" class="img-fluid rounded">
                    @endif
                    @if(! empty($expense->ocr_extracted['items']))
                        <div class="mt-3">
                            <div class="subheader mb-2">{{ __('Items extracted') }}</div>
                            <div class="list-group list-group-flush">
                                @foreach($expense->ocr_extracted['items'] as $item)
                                    <div class="list-group-item">
                                        <div class="row">
                                            <div class="col text-muted small">{{ $item['description'] ?? '—' }}</div>
                                            <div class="col-auto fw-bold">{{ number_format($item['total'] ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><x-ti name="pencil" /> {{ __('Edit & Confirm') }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.ocr.confirm', $expense) }}">
                        @csrf
                        <x-tabler.field :label="__('Vendor')" name="vendor_name" type="text" :value="old('vendor_name', $expense->vendor_name)" />
                        <x-tabler.field :label="__('Deskripsi *')" name="description" type="text" :value="old('description', $expense->description)" attrs="required" />
                        <div class="row g-2">
                            <div class="col-6">
                                <x-tabler.field :label="__('Total *')" name="amount" type="number" :value="old('amount', $expense->amount)" attrs='step="0.01" min="0" required' />
                            </div>
                            <div class="col-6">
                                <x-tabler.field :label="__('Currency *')" name="currency">
                                    <select id="currency" name="currency" required class="form-select">
                                        @foreach(['IDR', 'USD', 'EUR', 'SGD'] as $c)
                                            <option value="{{ $c }}" {{ $expense->currency === $c ? 'selected' : '' }}>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </x-tabler.field>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <x-tabler.field :label="__('Tanggal *')" name="expense_date">
                                    <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d')) }}" required class="form-control @error('expense_date') is-invalid @enderror">
                                </x-tabler.field>
                            </div>
                            <div class="col-6">
                                <x-tabler.field :label="__('Kategori')" name="category">
                                    <select id="category" name="category" class="form-select">
                                        @foreach(['Office Supplies', 'Travel', 'Meals', 'Software', 'Marketing', 'Equipment', 'Other'] as $cat)
                                            <option value="{{ $cat }}" {{ $expense->category === $cat ? 'selected' : '' }}>{{ $cat }}@if($expense->ai_category_suggested === $cat) (AI suggested) @endif</option>
                                        @endforeach
                                    </select>
                                </x-tabler.field>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" name="billable" value="1" {{ $expense->billable ? 'checked' : '' }} class="form-check-input">
                                <span class="form-check-label">{{ __('Billable ke klien') }}</span>
                            </label>
                        </div>
                        <div class="btn-list justify-content-end">
                            <a href="{{ route('expenses.index') }}" class="btn">{{ __('Batal') }}</a>
                            <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Simpan Expense') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

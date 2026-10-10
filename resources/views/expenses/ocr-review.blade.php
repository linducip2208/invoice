<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Review Hasil OCR') }} <span class="text-2xl">✨</span></h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800 mb-4">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Receipt preview --}}
            <div class="card-unicorn !p-4">
                <p class="text-xs uppercase tracking-wider text-purple-400 mb-2">📸 Receipt</p>
                @if($expense->receipt_path)
                    <img src="{{ asset('storage/' . $expense->receipt_path) }}" alt="Receipt" class="rounded-xl w-full">
                @endif
                @if(! empty($expense->ocr_extracted['items']))
                    <div class="mt-4">
                        <p class="text-xs uppercase tracking-wider text-purple-400 mb-2">Items extracted</p>
                        <div class="space-y-1 text-xs">
                            @foreach($expense->ocr_extracted['items'] as $item)
                                <div class="flex justify-between bg-purple-50/40 px-3 py-2 rounded-lg">
                                    <span class="text-purple-700">{{ $item['description'] ?? '—' }}</span>
                                    <span class="text-purple-900 font-bold">{{ number_format(($item['total'] ?? 0), 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Edit form --}}
            <form method="POST" action="{{ route('expenses.ocr.confirm', $expense) }}" class="card-unicorn !p-6 space-y-4">
                @csrf
                <p class="text-xs uppercase tracking-wider text-purple-400 mb-2">✏️ Edit & Confirm</p>

                <div>
                    <x-input-label :value="__('Vendor')" />
                    <x-text-input name="vendor_name" :value="old('vendor_name', $expense->vendor_name)" class="mt-1 w-full" />
                </div>

                <div>
                    <x-input-label :value="__('Deskripsi *')" />
                    <x-text-input name="description" :value="old('description', $expense->description)" required class="mt-1 w-full" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label :value="__('Total *')" />
                        <x-text-input name="amount" type="number" step="0.01" min="0" :value="old('amount', $expense->amount)" required class="mt-1 w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Currency *')" />
                        <select name="currency" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            @foreach(['IDR','USD','EUR','SGD'] as $c)
                                <option value="{{ $c }}" {{ ($expense->currency === $c) ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label :value="__('Tanggal *')" />
                        <x-text-input name="expense_date" type="date" :value="old('expense_date', $expense->expense_date?->format('Y-m-d'))" required class="mt-1 w-full" />
                    </div>
                    <div>
                        <x-input-label :value="__('Kategori')" />
                        <select name="category" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            @foreach(['Office Supplies','Travel','Meals','Software','Marketing','Equipment','Other'] as $cat)
                                <option value="{{ $cat }}" {{ ($expense->category === $cat) ? 'selected' : '' }}>
                                    {{ $cat }}
                                    @if($expense->ai_category_suggested === $cat) (AI suggested) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="billable" value="1" {{ $expense->billable ? 'checked' : '' }} class="rounded border-purple-300 text-pink-500">
                    <span class="text-sm text-purple-700">Billable ke klien</span>
                </label>

                <div class="flex justify-end gap-3 pt-3 border-t border-purple-100">
                    <a href="{{ route('expenses.index') }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                    <button type="submit" class="btn-unicorn">✓ {{ __('Simpan Expense') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

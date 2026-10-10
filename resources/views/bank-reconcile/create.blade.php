<x-tabler.layout :title="__('Upload Bank Statement')" :pretitle="__('Finance')">
    <x-slot name="actions">
        <a href="{{ route('bank-reconcile.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('bank-reconcile.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Bank *')" name="bank_name">
                            <select id="bank_name" name="bank_name" required class="form-select @error('bank_name') is-invalid @enderror">
                                @foreach(['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB', 'Permata', 'Danamon', 'Maybank', 'Other'] as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Nomor Rekening *')" name="account_no" type="text" :value="old('account_no')" attrs='required placeholder="1234567890" class="font-monospace"' />
                    </div>
                </div>
                <x-tabler.field :label="__('File CSV Mutasi *')" name="file" :help="__('Format: CSV dari internet banking (BCA m-banking, Mandiri Web, dll). Max 5MB.')">
                    <input type="file" id="file" name="file" accept=".csv,.txt" required class="form-control @error('file') is-invalid @enderror">
                </x-tabler.field>
                <div class="alert alert-info" role="alert">
                    <div class="d-flex">
                        <div><x-ti name="info-circle" /></div>
                        <div>
                            {{ __('Sistem akan auto-match transaksi credit dengan invoice yang belum lunas:') }}
                            <ul class="mb-0 mt-1">
                                <li>{{ __('High confidence: invoice_no ada di description') }}</li>
                                <li>{{ __('High: nominal exact + tanggal ≤7 hari dari issue') }}</li>
                                <li>{{ __('Medium: nominal exact + tanggal ≤30 hari') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100"><x-ti name="upload" /> {{ __('Upload & Auto-Match') }}</button>
            </form>
        </div>
    </div>
</x-tabler.layout>

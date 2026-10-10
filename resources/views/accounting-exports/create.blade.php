<x-tabler.layout :title="__('Generate Accounting Export')" :pretitle="__('Finance')">
    <x-slot name="actions">
        <a href="{{ route('accounting-exports.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('accounting-exports.store') }}">
                @csrf
                <x-tabler.field :label="__('Format Target *')" name="format" :help="__('Format CSV bisa di-import langsung ke aplikasi accounting target.')">
                    <select id="format" name="format" required class="form-select @error('format') is-invalid @enderror">
                        <option value="jurnal">{{ __('Mekari Jurnal (Indonesia)') }}</option>
                        <option value="mekari">{{ __('Mekari Klikpajak') }}</option>
                        <option value="quickbooks">{{ __('QuickBooks (CSV)') }}</option>
                        <option value="xero">{{ __('Xero (CSV)') }}</option>
                        <option value="csv_generic">{{ __('CSV Generic (universal)') }}</option>
                    </select>
                </x-tabler.field>
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Periode Awal *')" name="period_start" type="date" :value="now()->startOfMonth()->format('Y-m-d')" attrs="required" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Periode Akhir *')" name="period_end" type="date" :value="now()->endOfMonth()->format('Y-m-d')" attrs="required" />
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100"><x-ti name="file-export" /> {{ __('Generate Export') }}</button>
            </form>
        </div>
    </div>
</x-tabler.layout>

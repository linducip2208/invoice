<x-tabler.layout :title="__('Import Clients dari CSV')">
    <x-slot name="actions">
        <a href="{{ route('clients.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Clients') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Import Clients dari CSV') }}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Upload file CSV. Header wajib di baris pertama: <code>name, company_name, email, phone, address, city, state, country, postal_code, tax_id</code> (cuma <code>name</code> yang required).</p>

            <form method="POST" action="{{ route('clients.import') }}" enctype="multipart/form-data">
                @csrf
                <x-tabler.field :label="__('CSV File')" name="file" type="file" attrs='accept=".csv,text/csv" required' />
                <button type="submit" class="btn btn-primary">
                    <x-ti name="upload" /> {{ __('Upload & Import') }}
                </button>
            </form>

            <div class="alert alert-info mt-3 mb-0">
                <p class="fw-semibold mb-2">Contoh CSV:</p>
<pre class="mb-0">name,company_name,email,phone,city
John Doe,Acme Inc,john@acme.com,+62-812-1234,Jakarta
Jane Smith,Globex,jane@globex.com,+62-813-5678,Surabaya</pre>
            </div>
        </div>
    </div>
</x-tabler.layout>

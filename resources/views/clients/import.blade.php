<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">📥 {{ __('Import Clients dari CSV') }}</h2></x-slot>
    <div class="max-w-2xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">Upload file CSV. Header wajib di baris pertama: <code class="font-mono">name, company_name, email, phone, address, city, state, country, postal_code, tax_id</code> (cuma <code>name</code> yang required).</p>

        <form method="POST" action="{{ route('clients.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="file" name="file" accept=".csv,text/csv" required class="block w-full text-sm">
            <button class="btn-unicorn">Upload & Import</button>
        </form>

        <div class="mt-6 p-3 bg-purple-50 rounded-lg text-xs">
            <p class="font-bold text-purple-700 mb-2">Contoh CSV:</p>
<pre class="font-mono text-purple-700">name,company_name,email,phone,city
John Doe,Acme Inc,john@acme.com,+62-812-1234,Jakarta
Jane Smith,Globex,jane@globex.com,+62-813-5678,Surabaya</pre>
        </div>
    </div>
</x-app-layout>

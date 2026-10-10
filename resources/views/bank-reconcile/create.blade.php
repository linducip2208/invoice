<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Upload Bank Statement') }} <span class="text-2xl">📥</span></h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        @if(session('error'))<div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800 mb-4">⚠️ {{ session('error') }}</div>@endif
        <form method="POST" action="{{ route('bank-reconcile.store') }}" enctype="multipart/form-data" class="card-unicorn !p-8 space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Bank *')" />
                    <select name="bank_name" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        @foreach(['BCA','Mandiri','BNI','BRI','CIMB','Permata','Danamon','Maybank','Other'] as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Nomor Rekening *')" />
                    <x-text-input name="account_no" required class="mt-1 w-full font-mono" placeholder="1234567890" />
                </div>
            </div>
            <div>
                <x-input-label :value="__('File CSV Mutasi *')" />
                <input type="file" name="file" accept=".csv,.txt" required
                       class="block w-full mt-1 text-sm text-purple-700 file:mr-4 file:py-2 file:px-5 file:rounded-xl file:border-0 file:font-bold file:bg-gradient-to-br file:from-pink-200 file:to-purple-200 file:text-purple-700 cursor-pointer">
                <p class="text-xs text-purple-400 mt-2">📄 Format: CSV dari internet banking (BCA m-banking, Mandiri Web, dll). Max 5MB.</p>
            </div>
            <div class="rounded-xl bg-blue-50 border border-blue-200 p-3 text-xs text-blue-800">
                💡 Sistem akan auto-match transaksi credit dengan invoice yang belum lunas:
                <ul class="mt-1 space-y-0.5 ml-4 list-disc">
                    <li>High confidence: invoice_no ada di description</li>
                    <li>High: nominal exact + tanggal ≤7 hari dari issue</li>
                    <li>Medium: nominal exact + tanggal ≤30 hari</li>
                </ul>
            </div>
            <button type="submit" class="btn-unicorn w-full py-3">📥 {{ __('Upload & Auto-Match') }}</button>
        </form>
    </div>
</x-app-layout>

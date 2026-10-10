<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Kirim via WhatsApp') }} <span class="text-2xl">💬</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ Invoice <strong>{{ $invoice->invoice_no }}</strong></p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}" class="btn-unicorn-secondary text-sm">← {{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-5">
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('invoices.whatsapp.send', $invoice) }}" class="card-unicorn !p-8 space-y-5">
            @csrf
            <div>
                <x-input-label :value="__('Nomor WhatsApp')" />
                <x-text-input name="to_phone" :value="$invoice->client?->phone" required class="mt-1 w-full font-mono" placeholder="08123456789 atau +6281234..." />
                <p class="text-xs text-purple-400 mt-1">Format Indonesia: 08xxx atau +62. Akan otomatis di-normalize.</p>
            </div>

            <div>
                <x-input-label :value="__('Isi Pesan')" />
                <textarea name="message" rows="10" required maxlength="4000"
                          class="input-unicorn rounded-xl px-4 py-3 mt-1 block w-full font-mono text-sm">{{ $defaultMessage }}</textarea>
                <p class="text-xs text-purple-400 mt-1">💡 WhatsApp formatting: <code>*tebal*</code>, <code>_miring_</code>, <code>~coret~</code></p>
            </div>

            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs text-emerald-800">
                <p class="font-bold mb-1">✓ Akan terkirim ke:</p>
                <p>{{ $invoice->client?->name }} ({{ $invoice->client?->company_name ?: '—' }})</p>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice) }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                <button type="submit" class="btn-unicorn px-8">💬 {{ __('Kirim WhatsApp') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>

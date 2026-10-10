<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">✉️ {{ __('Email Templates') }}</h2></x-slot>
    <div class="max-w-4xl mx-auto space-y-4">
        @foreach([
            'invoice_sent' => ['title' => 'Invoice Dikirim', 'subject' => 'Invoice {invoice_no} dari {company_name}', 'body' => "Halo {client_name},\n\nMohon temukan invoice {invoice_no} sebesar {currency} {total} terlampir.\n\nDue date: {due_date}\n\nTerima kasih!\n\n{company_name}"],
            'reminder'     => ['title' => 'Reminder Pembayaran', 'subject' => 'Reminder: Invoice {invoice_no}', 'body' => "Halo {client_name},\n\nSekedar reminder bahwa invoice {invoice_no} jatuh tempo {due_date}.\n\nTotal: {currency} {total}\n\nLink pembayaran: {pay_link}"],
            'paid'         => ['title' => 'Pembayaran Diterima', 'subject' => 'Terima kasih — Pembayaran diterima', 'body' => "Halo {client_name},\n\nKami sudah menerima pembayaran sebesar {currency} {amount_paid} untuk invoice {invoice_no}.\n\nTerima kasih!"],
        ] as $key => $tpl)
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg mb-3">{{ $tpl['title'] }}</h3>
            <div><label class="text-sm font-bold">Subject</label><input type="text" value="{{ $tpl['subject'] }}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div class="mt-3"><label class="text-sm font-bold">Body</label><textarea rows="6" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full font-mono text-xs">{{ $tpl['body'] }}</textarea></div>
            <button type="button" disabled class="btn-unicorn opacity-50 mt-3">💾 Save (scaffold)</button>
        </div>
        @endforeach

        <div class="card-unicorn text-sm text-purple-600">
            <p class="font-bold mb-2">Variabel yang bisa dipakai:</p>
            <code class="font-mono text-xs">{client_name} {company_name} {invoice_no} {total} {currency} {due_date} {amount_paid} {pay_link}</code>
        </div>
    </div>
</x-app-layout>

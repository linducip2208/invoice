<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">📨 {{ __('Auto Reminder (Dunning)') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">{{ __('Atur otomasi pengingat email untuk invoice overdue.') }}</p>
        <form class="space-y-4">
            <div>
                <label class="text-sm font-bold">{{ __('Reminder Schedule') }}</label>
                <div class="space-y-2 mt-2 text-sm">
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox" checked> 3 hari sebelum due date</label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox" checked> Pada hari due date</label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox" checked> 3 hari setelah overdue</label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox"> 7 hari setelah overdue</label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-purple-50"><input type="checkbox"> 14 hari setelah overdue</label>
                </div>
            </div>
            <div><label class="text-sm font-bold">{{ __('Subject Template') }}</label><input type="text" value="Reminder: Invoice {invoice_no} — Due {due_date}" class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"></div>
            <div><label class="text-sm font-bold">{{ __('Stop on Payment Received') }}</label>
                <select class="input-unicorn rounded-xl px-3 py-2 mt-1 w-full"><option>Yes (recommended)</option><option>No</option></select>
            </div>
            <button type="button" disabled class="btn-unicorn opacity-50">💾 {{ __('Save (UI scaffold)') }}</button>
        </form>
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            ⏳ {{ __('Scheduled command `invoices:send-dunning-reminders` akan jalan harian saat di-deploy.') }}
        </div>
    </div>
</x-app-layout>

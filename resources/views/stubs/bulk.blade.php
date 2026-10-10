<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">📦 {{ __('Bulk Actions') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <p class="text-sm text-purple-600 mb-4">{{ __('Aksi massal pada multiple invoice / klien.') }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('invoices.index') }}" class="p-4 rounded-xl border-2 border-purple-100 hover:border-pink-300 transition">
                <div class="text-3xl mb-2">📨</div>
                <p class="font-bold">{{ __('Bulk Send Email') }}</p>
                <p class="text-xs text-purple-400">{{ __('Pilih invoice di /invoices, klik bulk-send.') }}</p>
            </a>
            <a href="{{ route('invoices.index') }}" class="p-4 rounded-xl border-2 border-purple-100 hover:border-pink-300 transition">
                <div class="text-3xl mb-2">📥</div>
                <p class="font-bold">{{ __('Bulk Download PDF (zip)') }}</p>
                <p class="text-xs text-purple-400">{{ __('Pilih multiple → download as zip.') }}</p>
            </a>
            <a href="{{ route('invoices.index') }}" class="p-4 rounded-xl border-2 border-purple-100 hover:border-pink-300 transition">
                <div class="text-3xl mb-2">✓</div>
                <p class="font-bold">{{ __('Bulk Mark as Sent / Paid') }}</p>
                <p class="text-xs text-purple-400">{{ __('Update status banyak invoice sekaligus.') }}</p>
            </a>
            <a href="{{ route('exports.index') }}" class="p-4 rounded-xl border-2 border-purple-100 hover:border-pink-300 transition">
                <div class="text-3xl mb-2">📊</div>
                <p class="font-bold">{{ __('Bulk Export CSV') }}</p>
                <p class="text-xs text-purple-400">{{ __('Sudah live di Export CSV.') }}</p>
            </a>
        </div>
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            ⏳ {{ __('Checkbox UI di /invoices belum di-wire. Sementara pakai actions per-invoice di list page.') }}
        </div>
    </div>
</x-app-layout>

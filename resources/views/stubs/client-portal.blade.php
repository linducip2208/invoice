<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🪟 {{ __('Client Portal') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <p class="text-purple-700 mb-3">{{ __('Klien kamu otomatis dapat link publik per invoice — tanpa perlu login.') }}</p>
        <p class="text-purple-700 mb-3">{{ __('Setiap invoice yang di-publish punya URL unik (token-based) yang bisa dibagikan via email atau WhatsApp.') }}</p>
        <div class="bg-purple-50 rounded-lg p-4 my-4">
            <p class="text-sm font-bold text-purple-700">{{ __('Format link:') }}</p>
            <code class="text-xs font-mono text-purple-600">{{ url('/i/{public_token}') }}</code>
        </div>
        <p class="text-purple-700 mb-3">{{ __('Klien bisa: lihat invoice, download PDF, bayar via gateway (jika aktif), tanpa register.') }}</p>
        <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200 text-sm text-amber-800">
            ⏳ <strong>{{ __('Magic-link login multi-invoice') }}</strong> — {{ __('Coming soon: 1 link untuk semua invoice klien dalam waktu') }}.
        </div>
        <a href="{{ route('invoices.index') }}" class="btn-unicorn mt-4">→ {{ __('Lihat invoice & link publik') }}</a>
    </div>
</x-app-layout>

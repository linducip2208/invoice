<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">🎓 {{ __('Onboarding Wizard') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto card-unicorn">
        <h3 class="font-display font-bold text-xl mb-4">{{ __('Setup Wizard — 5 Steps') }}</h3>
        <ol class="space-y-3">
            <li class="flex items-center gap-3 p-3 rounded-lg bg-purple-50">
                <span class="badge-unicorn">1</span>
                <a href="{{ route('workspace.settings') }}" class="font-semibold text-purple-700 hover:text-pink-500">{{ __('Lengkapi Company Info & Logo') }} →</a>
            </li>
            <li class="flex items-center gap-3 p-3 rounded-lg bg-purple-50">
                <span class="badge-unicorn">2</span>
                <a href="{{ route('clients.create') }}" class="font-semibold text-purple-700 hover:text-pink-500">{{ __('Tambah Klien Pertama') }} →</a>
            </li>
            <li class="flex items-center gap-3 p-3 rounded-lg bg-purple-50">
                <span class="badge-unicorn">3</span>
                <a href="{{ route('products.create') }}" class="font-semibold text-purple-700 hover:text-pink-500">{{ __('Tambah Produk / Layanan') }} →</a>
            </li>
            <li class="flex items-center gap-3 p-3 rounded-lg bg-purple-50">
                <span class="badge-unicorn">4</span>
                <a href="{{ route('invoices.create') }}" class="font-semibold text-purple-700 hover:text-pink-500">{{ __('Bikin Invoice Pertama') }} →</a>
            </li>
            <li class="flex items-center gap-3 p-3 rounded-lg bg-purple-50">
                <span class="badge-unicorn">5</span>
                <a href="{{ route('gateways.index') }}" class="font-semibold text-purple-700 hover:text-pink-500">{{ __('Setup Payment Gateway') }} →</a>
            </li>
        </ol>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header"><h2 class="heading-unicorn">📊 {{ __('Export Data') }}</h2></x-slot>
    <div class="max-w-3xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('exports.invoices') }}" class="card-unicorn text-center hover:shadow-unicorn-glow group">
            <span class="text-5xl block mb-3 group-hover:animate-unicorn-bounce">📄</span>
            <p class="font-display font-bold text-purple-900">Invoices</p>
            <p class="text-xs text-purple-500 mt-1">CSV semua invoice</p>
        </a>
        <a href="{{ route('exports.payments') }}" class="card-unicorn text-center hover:shadow-unicorn-glow group">
            <span class="text-5xl block mb-3 group-hover:animate-unicorn-bounce">💰</span>
            <p class="font-display font-bold text-purple-900">Payments</p>
            <p class="text-xs text-purple-500 mt-1">CSV semua payment</p>
        </a>
        <a href="{{ route('exports.clients') }}" class="card-unicorn text-center hover:shadow-unicorn-glow group">
            <span class="text-5xl block mb-3 group-hover:animate-unicorn-bounce">👥</span>
            <p class="font-display font-bold text-purple-900">Clients</p>
            <p class="text-xs text-purple-500 mt-1">CSV semua klien</p>
        </a>
    </div>
</x-app-layout>

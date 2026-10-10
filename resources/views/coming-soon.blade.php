<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ $info['name'] }} <span class="text-2xl">{{ $info['icon'] }}</span></h2>
        <p class="text-sm text-purple-500 mt-1">✦ {{ $info['desc'] }}</p>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="card-unicorn text-center py-16">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            <div class="text-7xl mb-4 inline-block animate-unicorn-float">{{ $info['icon'] }}</div>
            <h3 class="font-display text-2xl font-bold text-purple-900">{{ $info['name'] }}</h3>
            <p class="mt-2 text-sm text-purple-600 max-w-md mx-auto">{{ $info['desc'] }}</p>
            <div class="mt-3">
                <span class="badge-unicorn">{{ __('Plan:') }} {{ $info['plan'] }}</span>
            </div>

            <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-50 border border-amber-200 text-sm text-amber-800">
                ⚠️ {{ __('Modul ini akan tersedia di update berikutnya. Sudah ke-scaffold di sidebar untuk preview.') }}
            </div>

            <div class="mt-8">
                <a href="{{ route('dashboard') }}" class="btn-unicorn-secondary">← {{ __('Kembali ke Dashboard') }}</a>
                <a href="{{ route('billing.show') }}" class="btn-unicorn">✦ {{ __('Lihat Plan') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>

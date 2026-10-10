<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Template Marketplace') }} <span class="text-2xl">🛒</span></h2>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="card-unicorn-glow !p-12 text-center sparkles-bg">
            <p class="text-6xl mb-4 animate-unicorn-float">🛒</p>
            <h3 class="font-display text-2xl font-bold text-rainbow mb-3">Marketplace Coming Soon</h3>
            <p class="text-purple-600 max-w-2xl mx-auto mb-6">
                Browse template invoice premium dari komunitas, install ke workspace dengan 1-klik. Author bisa publish & monetize karya mereka.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-3xl mx-auto mt-8">
                @foreach([
                    ['📜', 'Premium Templates', 'Design profesional dari designer komunitas'],
                    ['📨', 'Email Templates', 'Sequence reminder yang terbukti convert'],
                    ['🤝', 'Workflow Templates', 'Best practice dari user lain'],
                ] as [$icon, $title, $desc])
                    <div class="card-unicorn !p-5 text-left">
                        <p class="text-4xl mb-2">{{ $icon }}</p>
                        <p class="font-display font-bold text-purple-900">{{ $title }}</p>
                        <p class="text-xs text-purple-500 mt-1">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            <p class="text-xs text-purple-400 mt-8">📅 Target launch: Q3 2026</p>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('AI Providers') }} <span class="text-2xl">🤖</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ {{ __('Bring Your Own Key — pakai LLM apapun') }}</p>
            </div>
            <a href="{{ route('ai-providers.create') }}" class="btn-unicorn">+ {{ __('Tambah Provider') }}</a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-2xl bg-rose-50 border-2 border-rose-200 p-4 text-rose-800">⚠️ {{ session('error') }}</div>
        @endif

        {{-- Usage stats --}}
        <div class="card-unicorn-glow !p-6">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-pink-400">✦</span> {{ __('Pemakaian Bulan Ini') }}
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wider text-purple-400">Total Cost (estimasi)</p>
                    <p class="font-display text-2xl font-bold text-rainbow">${{ number_format($totalCost, 4) }}</p>
                </div>
                @foreach($usageThisMonth as $u)
                    <div class="bg-purple-50/50 rounded-xl p-3">
                        <p class="text-xs font-bold text-purple-700">{{ str_replace('_',' ', ucfirst($u->feature)) }}</p>
                        <p class="text-sm text-purple-900 mt-1">{{ number_format($u->calls) }} call · {{ number_format($u->tokens) }} token</p>
                        <p class="text-xs text-purple-500 mt-0.5">${{ number_format($u->cost, 4) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Providers list --}}
        @if($providers->isEmpty())
            <div class="card-unicorn !p-12 text-center">
                <p class="text-5xl mb-3">🤖</p>
                <p class="font-display font-bold text-lg text-purple-700">{{ __('Belum ada AI provider') }}</p>
                <p class="text-sm text-purple-400 mt-1 mb-4">Tambah provider untuk pakai fitur AI (smart description, OCR receipt, predict, dll).</p>
                <a href="{{ route('ai-providers.create') }}" class="btn-unicorn">+ {{ __('Tambah Provider Pertama') }}</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($providers as $p)
                    <div class="card-unicorn !p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-display font-bold text-lg text-purple-900 truncate">{{ $p->name }}</h3>
                                    @if($p->is_active)
                                        <span class="badge-mint text-[10px]">Active</span>
                                    @else
                                        <span class="badge-gray text-[10px]">Disabled</span>
                                    @endif
                                </div>
                                <p class="text-xs text-purple-400 mt-1 font-mono">{{ str_replace('_',' ', $p->api_format) }} · {{ parse_url($p->base_url, PHP_URL_HOST) }}</p>
                                @if($p->default_model)
                                    <p class="text-xs text-purple-500 mt-1">Default: <code class="bg-purple-50 px-1.5 py-0.5 rounded">{{ $p->default_model }}</code></p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-4 pt-4 border-t border-purple-100">
                            <a href="{{ route('ai-providers.edit', $p) }}" class="btn-unicorn-secondary text-xs flex-1 text-center">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('ai-providers.test', $p) }}">@csrf
                                <button type="submit" class="btn-unicorn text-xs">🔌 Test</button>
                            </form>
                            <form method="POST" action="{{ route('ai-providers.destroy', $p) }}" onsubmit="return confirm('Hapus provider {{ $p->name }}?')">@csrf @method('DELETE')
                                <button type="submit" class="text-xs text-rose-500 px-2 hover:underline font-semibold">{{ __('Hapus') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

@extends('layouts.seo', ['metaTitle' => $metaTitle, 'metaDescription' => $metaDescription])

@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $metaTitle,
        'description' => $metaDescription,
        'datePublished' => '2024-01-01',
        'dateModified' => date('Y-m-d'),
        'author' => ['@type' => 'Organization', 'name' => 'Invocely'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Invocely'],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => url()->current()],
    ];
@endphp

@section('jsonld'){!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES) !!}@endsection

@section('content')
    <section class="hero-aurora pt-16 pb-12 px-4 sm:px-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <span class="absolute top-[20%] left-[10%] text-2xl animate-unicorn-float">✦</span>
            <span class="absolute bottom-[20%] right-[8%] text-2xl animate-unicorn-twinkle text-yellow-400">✧</span>
        </div>
        <div class="relative max-w-4xl mx-auto text-center">
            <span class="badge-unicorn">⚔️ {{ __('Head-to-Head') }}</span>
            <h1 class="mt-4 font-display text-3xl sm:text-5xl font-bold text-purple-900 leading-tight">
                {{ $toolA }} <span class="text-rainbow">vs</span> {{ $toolB }}
            </h1>
            <p class="mt-4 text-lg text-purple-600 max-w-2xl mx-auto">{{ $metaDescription }}</p>
        </div>
    </section>

    <article class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-8">
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="p-6">
                <h2 class="font-display text-2xl font-bold text-purple-900 mb-4 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Feature Comparison') }}
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="background: linear-gradient(90deg, #FFE4F1, #EDE9FE, #E0F2FE);">
                                <th class="py-3 px-4 text-left text-xs font-bold text-purple-700 uppercase tracking-wider">{{ __('Feature') }}</th>
                                <th class="py-3 px-4 text-center text-xs font-bold text-purple-700 uppercase tracking-wider">{{ $toolA }} @if($a==='invocely')🦄@endif</th>
                                <th class="py-3 px-4 text-center text-xs font-bold text-purple-700 uppercase tracking-wider">{{ $toolB }} @if($b==='invocely')🦄@endif</th>
                                <th class="py-3 px-4 text-center text-xs font-bold text-purple-700 uppercase tracking-wider">{{ __('Winner') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comparison as $feature)
                                <tr class="border-b border-purple-100 hover:bg-pink-50/40 transition">
                                    <td class="py-4 px-4 text-sm font-bold text-purple-900">{{ $feature['label'] }}</td>
                                    <td class="py-4 px-4 text-sm text-purple-700 text-center">{{ $feature['a'] }}</td>
                                    <td class="py-4 px-4 text-sm text-purple-700 text-center">{{ $feature['b'] }}</td>
                                    <td class="py-4 px-4 text-center">
                                        @if ($feature['winner'] === $a)
                                            <span class="badge-green">✦ {{ $toolA }}</span>
                                        @elseif ($feature['winner'] === $b)
                                            <span class="badge-green">✦ {{ $toolB }}</span>
                                        @else
                                            <span class="badge-gray">{{ __('Tie') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-unicorn">
            <h2 class="font-display text-2xl font-bold text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-purple-400">✦</span> {{ __('Detailed Analysis') }}
            </h2>
            <div class="space-y-5">
                <div>
                    <h3 class="font-display text-lg font-bold text-purple-900 flex items-center gap-2">💰 {{ __('Pricing & Value') }}</h3>
                    <p class="mt-1 text-purple-700 leading-relaxed">{{ __('Both') }} {{ $toolA }} {{ __('and') }} {{ $toolB }} {{ __('offer competitive pricing.') }} {{ $toolA }} {{ __('focuses on core features at an accessible price; ') }} {{ $toolB }} {{ __('may bundle accounting features that justify a higher tier.') }}</p>
                </div>
                <div>
                    <h3 class="font-display text-lg font-bold text-purple-900 flex items-center gap-2">🎨 {{ __('User Experience') }}</h3>
                    <p class="mt-1 text-purple-700 leading-relaxed">{{ $toolA }} {{ __('is known for a clean, intuitive interface.') }} {{ $toolB }} {{ __('offers a more feature-rich environment that needs setup time but rewards with flexibility.') }}</p>
                </div>
                <div>
                    <h3 class="font-display text-lg font-bold text-purple-900 flex items-center gap-2">🔌 {{ __('Integrations') }}</h3>
                    <p class="mt-1 text-purple-700 leading-relaxed">{{ $toolB }} {{ __('typically offers more third-party integrations.') }} {{ $toolA }} {{ __('focuses on the integrations that matter most for small businesses and freelancers.') }}</p>
                </div>
                <div>
                    <h3 class="font-display text-lg font-bold text-purple-900 flex items-center gap-2">🤝 {{ __('Support & Community') }}</h3>
                    <p class="mt-1 text-purple-700 leading-relaxed">{{ $toolA }} {{ __('typically provides responsive support across multiple channels.') }} {{ $toolB }} {{ __('complements support with extensive docs and community forums.') }}</p>
                </div>
            </div>
        </div>

        <div class="card-unicorn">
            <h2 class="font-display text-2xl font-bold text-purple-900 mb-5 flex items-center gap-2">
                <span class="text-sky-400">✦</span> {{ __('Which one should you choose?') }}
            </h2>
            <div class="grid md:grid-cols-2 gap-5">
                <div class="rounded-2xl p-5" style="background: linear-gradient(135deg, #FFE4F1, #EDE9FE); border: 1px solid rgba(255,122,198,0.3);">
                    <h3 class="font-display font-bold text-purple-900 mb-3 flex items-center gap-2">🦄 {{ __('Choose') }} {{ $toolA }} {{ __('if…') }}</h3>
                    <ul class="space-y-2 text-sm text-purple-800">
                        @foreach([
                            __('You need an affordable, straightforward invoicing tool'),
                            __('You prefer a modern, intuitive interface'),
                            __('Recurring billing and automation are priorities'),
                            __('You\'re a freelancer or small business owner'),
                            __('You want quick setup without extensive training'),
                        ] as $reason)
                            <li class="flex gap-2"><span class="text-pink-500">✦</span><span>{{ $reason }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl p-5" style="background: linear-gradient(135deg, #E0F2FE, #EDE9FE); border: 1px solid rgba(125,211,252,0.4);">
                    <h3 class="font-display font-bold text-purple-900 mb-3 flex items-center gap-2">🏢 {{ __('Choose') }} {{ $toolB }} {{ __('if…') }}</h3>
                    <ul class="space-y-2 text-sm text-purple-800">
                        @foreach([
                            __('You need comprehensive accounting alongside invoicing'),
                            __('You require many third-party integrations'),
                            __('Your business has complex billing workflows'),
                            __('You need advanced reporting and analytics'),
                            __('You have a dedicated finance team'),
                        ] as $reason)
                            <li class="flex gap-2"><span class="text-sky-500">✦</span><span>{{ $reason }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        @if($a !== 'invocely' && $b !== 'invocely')
            <div class="card-unicorn-glow text-center sparkles-bg p-10">
                <span class="text-5xl block animate-unicorn-float">🦄</span>
                <h2 class="mt-4 font-display text-2xl font-bold text-purple-900">{{ __('Or try a magical third option') }}</h2>
                <p class="mt-2 text-purple-600">{{ __('Invocely combines the best of both — modern UX, recurring billing, sparkly templates, all free to start.') }}</p>
                <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn-unicorn mt-5">✦ {{ __('Try Invocely') }}</a>
            </div>
        @endif
    </article>
@endsection

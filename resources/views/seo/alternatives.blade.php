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
            <span class="absolute bottom-[20%] right-[8%] text-2xl animate-unicorn-twinkle text-pink-400">✧</span>
        </div>
        <div class="relative max-w-4xl mx-auto text-center">
            <span class="badge-unicorn">🔄 {{ __('Alternatives') }}</span>
            <h1 class="mt-4 font-display text-3xl sm:text-5xl font-bold text-purple-900 leading-tight">{{ __('Best') }} {{ $toolName }} {{ __('Alternatives') }}</h1>
            <p class="mt-4 text-lg text-purple-600 max-w-2xl mx-auto">{{ $metaDescription }}</p>
        </div>
    </section>

    <article class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-8">
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="p-6">
                <h2 class="font-display text-2xl font-bold text-purple-900 mb-4 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ __('Top') }} {{ $toolName }} {{ __('Competitors') }}
                </h2>
                <p class="text-purple-700 mb-6 leading-relaxed">
                    {{ __('While') }} {{ $toolName }} {{ __('is popular, several alternatives may better fit your specific needs. We\'ve analyzed pricing, features, and user reviews to bring you the best options today.') }}
                </p>
                <div class="space-y-4">
                    @foreach ($alternatives as $rank => $alt)
                        <div class="flex gap-4 p-4 rounded-2xl border-2 border-purple-100 hover:border-pink-300 hover:shadow-unicorn-card transition">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center font-display font-bold text-white text-lg shadow-unicorn-pink" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                                {{ $rank + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <h3 class="font-display text-lg font-bold text-purple-900">{{ $alt['name'] }} @if($alt['slug']==='invocely')<span class="text-pink-400 animate-unicorn-twinkle">🦄</span>@endif</h3>
                                    <span class="badge-gold">{{ $alt['price'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-sm">
                                    <span class="text-yellow-500 text-base">{{ str_repeat('★', floor($alt['rating'])) }}{{ $alt['rating'] - floor($alt['rating']) >= 0.5 ? '½' : '' }}</span>
                                    <span class="text-purple-500 font-semibold">{{ $alt['rating'] }}/5</span>
                                </div>
                                <p class="mt-2 text-purple-600 text-sm leading-relaxed">{{ $alt['description'] }}</p>
                                <p class="mt-2 text-sm text-green-700 font-semibold flex items-start gap-1">
                                    <span>✦</span>
                                    <span>{{ __('Why over') }} {{ $toolName }}: {{ $alt['advantage'] }}</span>
                                </p>
                                <a href="{{ route('seo.compare', ['a' => $slug, 'b' => $alt['slug']]) }}" class="inline-block mt-2 text-sm text-pink-500 hover:text-pink-700 font-semibold">{{ __('Compare') }} {{ $toolName }} vs {{ $alt['name'] }} →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-unicorn">
            <h2 class="font-display text-2xl font-bold text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-purple-400">✦</span> {{ __('Why Consider Alternatives') }}
            </h2>
            <p class="text-purple-700 mb-4 leading-relaxed">{{ __('Every business has unique requirements. While') }} {{ $toolName }} {{ __('serves many well, here\'s why you might explore alternatives:') }}</p>
            <ul class="space-y-3 text-purple-700">
                @foreach([
                    ['💰', __('Pricing'),    __('Some alternatives offer more competitive pricing or generous free tiers.')],
                    ['🎨', __('Feature Set'), __('Different tools excel in different areas — automation, design, reporting.')],
                    ['🔌', __('Integrations'), __('Your existing stack might work better with a specific tool\'s API.')],
                    ['🎯', __('User Experience'), __('Interface preferences are subjective — try before you commit.')],
                    ['🤝', __('Support'),     __('Response times and channels vary widely between providers.')],
                    ['📈', __('Scalability'), __('You need a tool that grows with you, not against your wallet.')],
                ] as [$icon, $title, $desc])
                    <li class="flex gap-3 items-start">
                        <span class="text-2xl shrink-0">{{ $icon }}</span>
                        <div><strong class="text-purple-900">{{ $title }}:</strong> <span class="text-purple-600">{{ $desc }}</span></div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card-unicorn">
            <h2 class="font-display text-2xl font-bold text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-sky-400">✦</span> {{ __('FAQ') }}
            </h2>
            <div class="space-y-4">
                @foreach([
                    [__('What is the best alternative to').' '.$toolName.'?', __('It depends on your needs. Invocely offers modern features and recurring billing magic, Zoho Invoice is completely free, and QuickBooks provides full accounting.')],
                    [__('Can I switch from').' '.$toolName.' '.__('easily?'), __('Most invoicing tools support CSV import for clients, products, and invoices. Migration usually takes a few hours.')],
                    [__('Do alternatives offer data migration?'), __('Many provide free migration assistance or detailed guides. Premium plans often include white-glove migration.')],
                ] as [$q, $a])
                    <details class="group rounded-xl bg-purple-50/40 p-4 border border-purple-100 hover:border-pink-200 transition">
                        <summary class="font-display font-bold text-purple-900 cursor-pointer flex items-center justify-between gap-2">
                            <span>{{ $q }}</span>
                            <span class="text-pink-400 group-open:rotate-180 transition">✦</span>
                        </summary>
                        <p class="mt-2 text-sm text-purple-700 leading-relaxed">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>

        <div class="card-unicorn-glow text-center sparkles-bg p-10">
            <span class="text-5xl block animate-unicorn-float">🦄</span>
            <h2 class="mt-4 font-display text-2xl font-bold text-purple-900">{{ __('Try the magical alternative') }}</h2>
            <p class="mt-2 text-purple-600">{{ __('Invocely is free to start, and ridiculously sparkly.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn-unicorn mt-5">✦ {{ __('Sign Up Free') }}</a>
        </div>
    </article>
@endsection

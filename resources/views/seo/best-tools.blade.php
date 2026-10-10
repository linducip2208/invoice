@extends('layouts.seo', ['metaTitle' => $metaTitle, 'metaDescription' => $metaDescription])

@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $metaTitle,
        'description' => $metaDescription,
        'datePublished' => $year . '-01-01',
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
            <span class="absolute top-[30%] right-[15%] text-xl animate-unicorn-twinkle text-yellow-400">✧</span>
            <span class="absolute bottom-[20%] right-[8%] text-2xl animate-unicorn-float text-purple-400" style="animation-delay:1s;">✦</span>
        </div>
        <div class="relative max-w-4xl mx-auto text-center">
            <span class="badge-unicorn">📜 {{ __('Top') }} {{ $year }}</span>
            <h1 class="mt-4 font-display text-3xl sm:text-5xl font-bold text-purple-900 leading-tight">{{ $metaTitle }}</h1>
            <p class="mt-4 text-lg text-purple-600 max-w-2xl mx-auto">{{ $metaDescription }}</p>
        </div>
    </section>

    <article class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-8">
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="p-6">
                <h2 class="font-display text-2xl font-bold text-purple-900 mb-6 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> Top 10 Invoicing Tools for {{ $year }}
                </h2>
                <div class="space-y-4">
                    @foreach ($tools as $rank => $tool)
                        <div class="flex gap-4 p-4 rounded-2xl border-2 border-purple-100 hover:border-pink-300 hover:shadow-unicorn-card transition group">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center font-display font-bold text-white text-lg shadow-unicorn-pink" style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                                {{ $rank + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <h3 class="font-display text-lg font-bold text-purple-900">{{ $tool['name'] }} @if($tool['slug']==='invocely')<span class="text-pink-400 animate-unicorn-twinkle">🦄</span>@endif</h3>
                                    <span class="badge-gold">{{ $tool['price'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-sm">
                                    <span class="text-yellow-500 text-base">{{ str_repeat('★', floor($tool['rating'])) }}{{ $tool['rating'] - floor($tool['rating']) >= 0.5 ? '½' : '' }}</span>
                                    <span class="text-purple-500 font-semibold">{{ $tool['rating'] }}/5</span>
                                </div>
                                <p class="mt-2 text-purple-600 text-sm leading-relaxed">{{ $tool['description'] }}</p>
                                <a href="{{ route('seo.alternatives', ['slug' => $tool['slug']]) }}" class="inline-block mt-2 text-sm text-pink-500 hover:text-pink-700 font-semibold">{{ __('View alternatives') }} →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-unicorn">
            <h2 class="font-display text-2xl font-bold text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-purple-400">✦</span> {{ __('How to Choose') }}
            </h2>
            <p class="text-purple-700 mb-4 leading-relaxed">Choosing the right invoicing software depends on your business size, industry, and needs. Here's what to look for in {{ $year }}:</p>
            <ul class="space-y-3 text-purple-700 ml-1">
                @foreach([
                    ['💰', 'Pricing Model', 'Free plans suffice for many. Premium plans unlock automation, multi-user, integrations.'],
                    ['🔄', 'Automation', 'Recurring invoices, payment reminders, late fee calculations save hours weekly.'],
                    ['🔌', 'Integrations', 'Should plug into your accounting software, payment gateways, and CRM.'],
                    ['🎨', 'Customization', 'Professional templates and brand colors keep your identity intact.'],
                    ['🌍', 'Multi-Currency', 'Essential if you bill international clients across currencies.'],
                    ['📊', 'Reporting', 'Revenue, outstanding, top clients, payment trends — all in one place.'],
                    ['📱', 'Mobile Access', 'Bill on the go. Critical for service businesses and freelancers.'],
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
                    ['What is the best free invoicing tool in '.$year.'?', 'Zoho Invoice and Wave offer fully free invoicing. Invocely also has a generous free tier with recurring invoices, beautiful templates, and public invoice sharing.'],
                    ['Can small businesses use invoicing software?', 'Absolutely — most tools are designed for small businesses and freelancers. Features include automated billing, expense tracking, and client management at affordable prices.'],
                    ['How do recurring invoices work?', 'Recurring invoices auto-generate on a schedule (daily, weekly, monthly, yearly). Configure the frequency, amount, and client once — the system handles the rest.'],
                    ['What features matter most?', 'Professional templates, recurring billing, payment tracking, multi-currency, expense management, and integration with payment gateways like Stripe or PayPal.'],
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
            <h2 class="mt-4 font-display text-2xl font-bold text-purple-900">{{ __('Ready to try Invocely?') }}</h2>
            <p class="mt-2 text-purple-600">{{ __('Free to start. Magical from day one.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn-unicorn mt-5">✦ {{ __('Begin Free') }}</a>
        </div>
    </article>
@endsection

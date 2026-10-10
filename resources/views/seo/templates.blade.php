@php
    $items = [];
    $position = 1;
    foreach ($templateCategories as $cat) {
        foreach ($cat['templates'] as $tpl) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $tpl['name'] . ' Invoice Template',
                'description' => $tpl['description'],
            ];
        }
    }
    $tplGradients = [
        'classic' => 'from-purple-200 to-blue-200',
        'modern'  => 'from-pink-200 to-purple-200',
        'minimal' => 'from-gray-100 to-purple-100',
        'bold'    => 'from-pink-300 to-yellow-200',
        'elegant' => 'from-purple-300 to-pink-200',
    ];
@endphp

@extends('layouts.seo', ['metaTitle' => $metaTitle, 'metaDescription' => $metaDescription])

@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => 'Invoice Templates Gallery',
        'description' => $metaDescription,
        'itemListElement' => $items,
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
            <span class="badge-unicorn">📜 {{ __('Templates Gallery') }}</span>
            <h1 class="mt-4 font-display text-3xl sm:text-5xl font-bold text-purple-900 leading-tight">
                {{ __('Invoice Templates') }} <span class="text-rainbow">{{ __('Gallery') }}</span>
            </h1>
            <p class="mt-4 text-lg text-purple-600 max-w-2xl mx-auto">{{ $metaDescription }}</p>
        </div>
    </section>

    <article class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-8">
        @foreach ($templateCategories as $category)
            <div class="card-unicorn">
                <h2 class="font-display text-2xl font-bold text-purple-900 flex items-center gap-2">
                    <span class="text-pink-400">✦</span> {{ $category['category'] }} {{ __('Templates') }}
                </h2>
                <p class="text-purple-600 mt-1 mb-5">{{ $category['description'] }}</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($category['templates'] as $template)
                        <div class="rounded-2xl p-5 border-2 border-purple-100 hover:border-pink-300 hover:shadow-unicorn-glow transition group">
                            <div class="w-full h-36 rounded-xl mb-4 flex items-center justify-center bg-gradient-to-br {{ $tplGradients[$template['slug']] ?? 'from-purple-100 to-pink-100' }} group-hover:animate-unicorn-pulse">
                                <span class="font-display font-bold text-purple-900 text-xl tracking-wide drop-shadow-sm">{{ $template['name'] }}</span>
                            </div>
                            <h3 class="font-display font-bold text-purple-900 mb-1 flex items-center gap-1">{{ $template['name'] }} <span class="text-yellow-400">✦</span></h3>
                            <p class="text-sm text-purple-600 leading-relaxed">{{ $template['description'] }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="badge-mint">{{ __('Free') }}</span>
                                <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="text-xs font-semibold text-pink-500 hover:text-pink-700">{{ __('Use template') }} →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="card-unicorn">
            <h2 class="font-display text-2xl font-bold text-purple-900 mb-4 flex items-center gap-2">
                <span class="text-purple-400">✦</span> {{ __('How to Choose Your Template') }}
            </h2>
            <p class="text-purple-700 mb-4 leading-relaxed">{{ __('Your invoice template is more than a document — it\'s a reflection of your brand. Here\'s how to pick the perfect one:') }}</p>
            <ul class="space-y-3 text-purple-700">
                @foreach([
                    ['🎨', __('Match your brand'), __('Use colors and typography that align with your guidelines for trust and consistency.')],
                    ['🏢', __('Consider your industry'), __('Corporate clients want formal. Creative agencies can be bold. Freelancers benefit from minimal.')],
                    ['👀', __('Prioritize readability'), __('Make invoice number, due date, total, and payment instructions easy to find.')],
                    ['🖨️', __('Digital vs. print'), __('Some templates shine on screen but lose clarity in print. Test both if needed.')],
                    ['🧾', __('Essential elements'), __('Logo, client details, itemized services, tax, payment terms, contact info.')],
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
                    [__('What makes a good invoice template?'), __('Balance of professional design and clear hierarchy. Brand prominent, financials easy to find, payment instructions obvious.')],
                    [__('Can I customize these templates?'), __('Yes — all are fully customizable. Change colors, fonts, add your logo, adjust layout to match your brand.')],
                    [__('Are templates free to use?'), __('All templates work with a free Invocely account. Unlimited invoices, no cost on the free tier.')],
                    [__('What file format do invoices export as?'), __('PDF — universally compatible across devices. Plus a public link for instant viewing in any browser.')],
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
            <h2 class="mt-4 font-display text-2xl font-bold text-purple-900">{{ __('Pick a sparkly template & start invoicing') }}</h2>
            <p class="mt-2 text-purple-600">{{ __('Free forever on the basic plan. No credit card needed.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn-unicorn mt-5">✦ {{ __('Start Free') }}</a>
        </div>
    </article>
@endsection

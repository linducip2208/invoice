<x-tabler.public :title="$metaTitle" :meta-description="$metaDescription">
    @push('head')
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
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col text-center">
                <div class="page-pretitle justify-content-center"><span class="badge bg-blue-lt"><x-ti name="repeat" /> {{ __('Alternatives') }}</span></div>
                <h1 class="page-title">{{ __('Best') }} {{ $toolName }} {{ __('Alternatives') }}</h1>
                <div class="text-muted mt-1">{{ $metaDescription }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><x-ti name="star" /> {{ __('Top') }} {{ $toolName }} {{ __('Competitors') }}</h3>
        </div>
        <div class="card-body">
            <p class="text-muted">{{ __('While') }} {{ $toolName }} {{ __('is popular, several alternatives may better fit your specific needs. We\'ve analyzed pricing, features, and user reviews to bring you the best options today.') }}</p>
            <div class="list-group list-group-flush">
                @foreach ($alternatives as $rank => $alt)
                    <div class="list-group-item">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto"><span class="avatar">{{ $rank + 1 }}</span></div>
                            <div class="col">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <strong>{{ $alt['name'] }}</strong>
                                    <span class="badge bg-yellow-lt">{{ $alt['price'] }}</span>
                                    <span class="text-muted"><x-ti name="star" /> {{ $alt['rating'] }}/5</span>
                                </div>
                                <p class="text-muted mb-1">{{ $alt['description'] }}</p>
                                <p class="mb-1"><span class="badge bg-green-lt"><x-ti name="check" /> {{ __('Why over') }} {{ $toolName }}: {{ $alt['advantage'] }}</span></p>
                                <a href="{{ route('seo.compare', ['a' => $slug, 'b' => $alt['slug']]) }}" class="btn btn-sm mt-1">{{ __('Compare') }} {{ $toolName }} vs {{ $alt['name'] }} &rarr;</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="info-circle" /> {{ __('Why Consider Alternatives') }}</h3></div>
        <div class="card-body">
            <p class="text-muted">{{ __('Every business has unique requirements. While') }} {{ $toolName }} {{ __('serves many well, here\'s why you might explore alternatives:') }}</p>
            <ul class="list-unstyled mb-0">
                @foreach([
                    [__('Pricing'), __('Some alternatives offer more competitive pricing or generous free tiers.')],
                    [__('Feature Set'), __('Different tools excel in different areas — automation, design, reporting.')],
                    [__('Integrations'), __('Your existing stack might work better with a specific tool\'s API.')],
                    [__('User Experience'), __('Interface preferences are subjective — try before you commit.')],
                    [__('Support'), __('Response times and channels vary widely between providers.')],
                    [__('Scalability'), __('You need a tool that grows with you, not against your wallet.')],
                ] as [$title, $desc])
                    <li class="d-flex gap-2 mb-2"><span class="text-primary"><x-ti name="check" /></span><span><strong>{{ $title }}:</strong> <span class="text-muted">{{ $desc }}</span></span></li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="help" /> {{ __('FAQ') }}</h3></div>
        <div class="card-body">
            <div class="accordion" id="faq-alternatives">
                @foreach([
                    [__('What is the best alternative to').' '.$toolName.'?', __('It depends on your needs. Invocely offers modern features and recurring billing, Zoho Invoice is completely free, and QuickBooks provides full accounting.')],
                    [__('Can I switch from').' '.$toolName.' '.__('easily?'), __('Most invoicing tools support CSV import for clients, products, and invoices. Migration usually takes a few hours.')],
                    [__('Do alternatives offer data migration?'), __('Many provide free migration assistance or detailed guides. Premium plans often include white-glove migration.')],
                ] as $i => [$q, $a])
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-alt-{{ $i }}">{{ $q }}</button>
                        </h2>
                        <div id="faq-alt-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faq-alternatives">
                            <div class="accordion-body text-muted">{{ $a }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card text-center">
        <div class="card-body">
            <h2 class="card-title justify-content-center">{{ __('Try a clean alternative') }}</h2>
            <p class="text-muted">{{ __('Invocely is free to start.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Sign Up Free') }}</a>
        </div>
    </div>
</x-tabler.public>

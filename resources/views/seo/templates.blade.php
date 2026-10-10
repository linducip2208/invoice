<x-tabler.public :title="$metaTitle" :meta-description="$metaDescription">
    @push('head')
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
            $jsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => 'Invoice Templates Gallery',
                'description' => $metaDescription,
                'itemListElement' => $items,
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col text-center">
                <div class="page-pretitle justify-content-center"><span class="badge bg-blue-lt"><x-ti name="template" /> {{ __('Templates Gallery') }}</span></div>
                <h1 class="page-title">{{ __('Invoice Templates Gallery') }}</h1>
                <div class="text-muted mt-1">{{ $metaDescription }}</div>
            </div>
        </div>
    </div>

    @foreach ($templateCategories as $category)
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title"><x-ti name="template" /> {{ $category['category'] }} {{ __('Templates') }}</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $category['description'] }}</p>
                <div class="row row-cards">
                    @foreach ($category['templates'] as $template)
                        <div class="col-sm-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-2 text-primary"><x-ti name="file-text" class="icon icon-lg" /></div>
                                    <h4 class="card-title justify-content-center">{{ $template['name'] }}</h4>
                                    <p class="text-muted">{{ $template['description'] }}</p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="badge bg-green-lt">{{ __('Free') }}</span>
                                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-sm">{{ __('Use template') }} &rarr;</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="info-circle" /> {{ __('How to Choose Your Template') }}</h3></div>
        <div class="card-body">
            <p class="text-muted">{{ __('Your invoice template is more than a document — it\'s a reflection of your brand. Here\'s how to pick the perfect one:') }}</p>
            <ul class="list-unstyled mb-0">
                @foreach([
                    [__('Match your brand'), __('Use colors and typography that align with your guidelines for trust and consistency.')],
                    [__('Consider your industry'), __('Corporate clients want formal. Creative agencies can be bold. Freelancers benefit from minimal.')],
                    [__('Prioritize readability'), __('Make invoice number, due date, total, and payment instructions easy to find.')],
                    [__('Digital vs. print'), __('Some templates shine on screen but lose clarity in print. Test both if needed.')],
                    [__('Essential elements'), __('Logo, client details, itemized services, tax, payment terms, contact info.')],
                ] as [$title, $desc])
                    <li class="d-flex gap-2 mb-2"><span class="text-primary"><x-ti name="check" /></span><span><strong>{{ $title }}:</strong> <span class="text-muted">{{ $desc }}</span></span></li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="help" /> {{ __('FAQ') }}</h3></div>
        <div class="card-body">
            <div class="accordion" id="faq-templates">
                @foreach([
                    [__('What makes a good invoice template?'), __('Balance of professional design and clear hierarchy. Brand prominent, financials easy to find, payment instructions obvious.')],
                    [__('Can I customize these templates?'), __('Yes — all are fully customizable. Change colors, fonts, add your logo, adjust layout to match your brand.')],
                    [__('Are templates free to use?'), __('All templates work with a free Invocely account. Unlimited invoices, no cost on the free tier.')],
                    [__('What file format do invoices export as?'), __('PDF — universally compatible across devices. Plus a public link for instant viewing in any browser.')],
                ] as $i => [$q, $a])
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-tpl-{{ $i }}">{{ $q }}</button>
                        </h2>
                        <div id="faq-tpl-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faq-templates">
                            <div class="accordion-body text-muted">{{ $a }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card text-center">
        <div class="card-body">
            <h2 class="card-title justify-content-center">{{ __('Pick a template & start invoicing') }}</h2>
            <p class="text-muted">{{ __('Free on the basic plan. No credit card needed.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Start Free') }}</a>
        </div>
    </div>
</x-tabler.public>

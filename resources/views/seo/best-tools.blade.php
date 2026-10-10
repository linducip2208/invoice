<x-tabler.public :title="$metaTitle" :meta-description="$metaDescription">
    @push('head')
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
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col text-center">
                <div class="page-pretitle justify-content-center"><span class="badge bg-blue-lt"><x-ti name="trophy" /> {{ __('Top') }} {{ $year }}</span></div>
                <h1 class="page-title">{{ $metaTitle }}</h1>
                <div class="text-muted mt-1">{{ $metaDescription }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="trophy" /> {{ __('Top 10 Invoicing Tools for') }} {{ $year }}</h3></div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach ($tools as $rank => $tool)
                    <div class="list-group-item">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto"><span class="avatar">{{ $rank + 1 }}</span></div>
                            <div class="col">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <strong>{{ $tool['name'] }}</strong>
                                    <span class="badge bg-yellow-lt">{{ $tool['price'] }}</span>
                                    <span class="text-muted"><x-ti name="star" /> {{ $tool['rating'] }}/5</span>
                                </div>
                                <p class="text-muted mb-1">{{ $tool['description'] }}</p>
                                <a href="{{ route('seo.alternatives', ['slug' => $tool['slug']]) }}" class="btn btn-sm mt-1">{{ __('View alternatives') }} &rarr;</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="info-circle" /> {{ __('How to Choose') }}</h3></div>
        <div class="card-body">
            <p class="text-muted">{{ __('Choosing the right invoicing software depends on your business size, industry, and needs. Here\'s what to look for in :year:', ['year' => $year]) }}</p>
            <ul class="list-unstyled mb-0">
                @foreach([
                    [__('Pricing Model'), __('Free plans suffice for many. Premium plans unlock automation, multi-user, integrations.')],
                    [__('Automation'), __('Recurring invoices, payment reminders, and late fee calculations save hours weekly.')],
                    [__('Integrations'), __('Should plug into your accounting software, payment gateways, and CRM.')],
                    [__('Customization'), __('Professional templates and brand colors keep your identity intact.')],
                    [__('Multi-Currency'), __('Essential if you bill international clients across currencies.')],
                    [__('Reporting'), __('Revenue, outstanding, top clients, and payment trends — all in one place.')],
                    [__('Mobile Access'), __('Bill on the go. Critical for service businesses and freelancers.')],
                ] as [$title, $desc])
                    <li class="d-flex gap-2 mb-2"><span class="text-primary"><x-ti name="check" /></span><span><strong>{{ $title }}:</strong> <span class="text-muted">{{ $desc }}</span></span></li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="help" /> {{ __('FAQ') }}</h3></div>
        <div class="card-body">
            <div class="accordion" id="faq-tools">
                @foreach([
                    [__('What is the best free invoicing tool in').' '.$year.'?', __('Zoho Invoice and Wave offer fully free invoicing. Invocely also has a free tier with recurring invoices, templates, and public invoice sharing.')],
                    [__('Can small businesses use invoicing software?'), __('Yes — most tools are designed for small businesses and freelancers, with automated billing, expense tracking, and client management.')],
                    [__('How do recurring invoices work?'), __('Recurring invoices auto-generate on a schedule (daily, weekly, monthly, yearly). Configure the frequency, amount, and client once.')],
                    [__('What features matter most?'), __('Professional templates, recurring billing, payment tracking, multi-currency, expense management, and payment gateway integrations.')],
                ] as $i => [$q, $a])
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-tool-{{ $i }}">{{ $q }}</button>
                        </h2>
                        <div id="faq-tool-{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faq-tools">
                            <div class="accordion-body text-muted">{{ $a }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card text-center">
        <div class="card-body">
            <h2 class="card-title justify-content-center">{{ __('Ready to try Invocely?') }}</h2>
            <p class="text-muted">{{ __('Free to start.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Begin Free') }}</a>
        </div>
    </div>
</x-tabler.public>

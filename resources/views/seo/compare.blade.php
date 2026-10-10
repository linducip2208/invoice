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
                <div class="page-pretitle justify-content-center"><span class="badge bg-blue-lt"><x-ti name="scale" /> {{ __('Head-to-Head') }}</span></div>
                <h1 class="page-title">{{ $toolA }} {{ __('vs') }} {{ $toolB }}</h1>
                <div class="text-muted mt-1">{{ $metaDescription }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="table" /> {{ __('Feature Comparison') }}</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Feature') }}</th>
                        <th class="text-center">{{ $toolA }}</th>
                        <th class="text-center">{{ $toolB }}</th>
                        <th class="text-center">{{ __('Winner') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comparison as $feature)
                        <tr>
                            <td class="fw-bold">{{ $feature['label'] }}</td>
                            <td class="text-center text-muted">{{ $feature['a'] }}</td>
                            <td class="text-center text-muted">{{ $feature['b'] }}</td>
                            <td class="text-center">
                                @if ($feature['winner'] === $a)
                                    <span class="badge bg-green-lt"><x-ti name="check" /> {{ $toolA }}</span>
                                @elseif ($feature['winner'] === $b)
                                    <span class="badge bg-green-lt"><x-ti name="check" /> {{ $toolB }}</span>
                                @else
                                    <span class="badge bg-yellow-lt">{{ __('Tie') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="file-text" /> {{ __('Detailed Analysis') }}</h3></div>
        <div class="card-body">
            <h4>{{ __('Pricing & Value') }}</h4>
            <p class="text-muted">{{ __('Both') }} {{ $toolA }} {{ __('and') }} {{ $toolB }} {{ __('offer competitive pricing.') }} {{ $toolA }} {{ __('focuses on core features at an accessible price; ') }} {{ $toolB }} {{ __('may bundle accounting features that justify a higher tier.') }}</p>
            <h4>{{ __('User Experience') }}</h4>
            <p class="text-muted">{{ $toolA }} {{ __('is known for a clean, intuitive interface.') }} {{ $toolB }} {{ __('offers a more feature-rich environment that needs setup time but rewards with flexibility.') }}</p>
            <h4>{{ __('Integrations') }}</h4>
            <p class="text-muted">{{ $toolB }} {{ __('typically offers more third-party integrations.') }} {{ $toolA }} {{ __('focuses on the integrations that matter most for small businesses and freelancers.') }}</p>
            <h4>{{ __('Support & Community') }}</h4>
            <p class="text-muted mb-0">{{ $toolA }} {{ __('typically provides responsive support across multiple channels.') }} {{ $toolB }} {{ __('complements support with extensive docs and community forums.') }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title"><x-ti name="help" /> {{ __('Which one should you choose?') }}</h3></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ __('Choose') }} {{ $toolA }} {{ __('if…') }}</h4>
                            <ul class="list-unstyled mb-0">
                                @foreach([
                                    __('You need an affordable, straightforward invoicing tool'),
                                    __('You prefer a modern, intuitive interface'),
                                    __('Recurring billing and automation are priorities'),
                                    __('You\'re a freelancer or small business owner'),
                                    __('You want quick setup without extensive training'),
                                ] as $reason)
                                    <li class="d-flex gap-2 mb-1"><span class="text-primary"><x-ti name="check" /></span><span>{{ $reason }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ __('Choose') }} {{ $toolB }} {{ __('if…') }}</h4>
                            <ul class="list-unstyled mb-0">
                                @foreach([
                                    __('You need comprehensive accounting alongside invoicing'),
                                    __('You require many third-party integrations'),
                                    __('Your business has complex billing workflows'),
                                    __('You need advanced reporting and analytics'),
                                    __('You have a dedicated finance team'),
                                ] as $reason)
                                    <li class="d-flex gap-2 mb-1"><span class="text-primary"><x-ti name="check" /></span><span>{{ $reason }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($a !== 'invocely' && $b !== 'invocely')
        <div class="card text-center">
            <div class="card-body">
                <h2 class="card-title justify-content-center">{{ __('Or try a third option') }}</h2>
                <p class="text-muted">{{ __('Invocely combines modern UX, recurring billing, and clean templates, free to start.') }}</p>
                <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Try Invocely') }}</a>
            </div>
        </div>
    @endif
</x-tabler.public>

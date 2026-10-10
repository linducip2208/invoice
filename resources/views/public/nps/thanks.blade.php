<x-tabler.public :title="__('Thank you!')" :meta-description="__('Thank you for your feedback.')">
    @push('head')
        <meta name="robots" content="noindex, nofollow">
    @endpush

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-2 text-success"><x-ti name="check" class="icon icon-lg" /></div>
                    <h1 class="card-title justify-content-center">{{ __('Thank you!') }}</h1>
                    <p class="text-muted">{{ __('Your feedback means a lot to our team. We read everything and keep improving our service quality.') }}</p>
                    @if($nps->score >= 9)
                        <div class="alert alert-success mt-3 mb-0">{{ __('You are one of our favorite clients!') }}</div>
                    @endif
                </div>
            </div>
            <p class="text-center text-muted mt-3">{{ __('Powered by') }} <a href="/" class="fw-bold">Invocely</a></p>
        </div>
    </div>
</x-tabler.public>

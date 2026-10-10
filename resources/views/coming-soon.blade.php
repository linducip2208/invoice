<x-tabler.layout :title="$info['name']" :pretitle="__('Coming soon')" :subtitle="$info['desc']">
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="mb-2 text-primary"><x-ti name="clock" class="icon icon-lg" /></div>
            <h3 class="card-title justify-content-center">{{ $info['name'] }}</h3>
            <p class="text-muted">{{ $info['desc'] }}</p>
            <div class="mt-2"><span class="badge bg-blue-lt">{{ __('Plan:') }} {{ $info['plan'] }}</span></div>
            <div class="alert alert-warning mt-4 mb-0">
                <div class="d-flex justify-content-center">
                    <div><x-ti name="info-circle" /></div>
                    <div>{{ __('This module will be available in a future update. It is scaffolded in the sidebar for preview.') }}</div>
                </div>
            </div>
            <div class="btn-list justify-content-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn">&larr; {{ __('Back to Dashboard') }}</a>
                <a href="{{ route('billing.show') }}" class="btn btn-primary"><x-ti name="wallet" /> {{ __('View Plans') }}</a>
            </div>
        </div>
    </div>
</x-tabler.layout>

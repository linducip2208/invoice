@section('title', '500')
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body text-center py-5">
            <div class="empty">
                <div class="empty-icon"><x-ti name="alert-circle" class="icon icon-lg" /></div>
                <p class="empty-title h1">500</p>
                <p class="empty-subtitle text-muted">{{ __('Something went wrong on our side. The error has been logged.') }}</p>
                <div class="empty-action">
                    <a href="/" class="btn">{{ __('Home') }}</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">{{ __('Dashboard') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-tabler.guest>

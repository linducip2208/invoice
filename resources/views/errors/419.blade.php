@section('title', '419')
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body text-center py-5">
            <div class="empty">
                <div class="empty-icon"><x-ti name="clock" class="icon icon-lg" /></div>
                <p class="empty-title h1">419</p>
                <p class="empty-subtitle text-muted">{{ __('Your session has expired. Please refresh and try again.') }}</p>
                <div class="empty-action">
                    <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Log in again') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-tabler.guest>

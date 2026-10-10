@section('title', '429')
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body text-center py-5">
            <div class="empty">
                <div class="empty-icon"><x-ti name="hourglass" class="icon icon-lg" /></div>
                <p class="empty-title h1">429</p>
                <p class="empty-subtitle text-muted">{{ __('Too many requests. Please slow down and try again in a moment.') }}</p>
                <div class="empty-action">
                    <a href="/" class="btn btn-primary">{{ __('Home') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-tabler.guest>

@section('title', '503')
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body text-center py-5">
            <div class="empty">
                <div class="empty-icon"><x-ti name="settings" class="icon icon-lg" /></div>
                <p class="empty-title h1">503</p>
                <p class="empty-subtitle text-muted">{{ __('We are performing scheduled maintenance. Please check back soon.') }}</p>
                <div class="empty-action">
                    <a href="/" class="btn btn-primary">{{ __('Home') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-tabler.guest>

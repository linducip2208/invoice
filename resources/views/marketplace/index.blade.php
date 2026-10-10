<x-tabler.layout :title="__('Template Marketplace')" :pretitle="__('Extensions')">
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="mb-2 text-primary"><x-ti name="shopping-cart" class="icon icon-lg" /></div>
            <h3 class="card-title justify-content-center">{{ __('Marketplace Coming Soon') }}</h3>
            <p class="text-muted">{{ __('Browse premium invoice templates from the community and install them into your workspace with one click.') }}</p>
            <div class="row row-cards mt-4 text-start">
                @foreach([
                    ['file-text', __('Premium Templates'), __('Professional designs from community designers')],
                    ['send', __('Email Templates'), __('Reminder sequences that convert')],
                    ['briefcase', __('Workflow Templates'), __('Best practices shared by other users')],
                ] as [$icon, $title, $desc])
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="mb-2 text-primary"><x-ti :name="$icon" class="icon icon-lg" /></div>
                                <p class="fw-bold mb-1">{{ $title }}</p>
                                <p class="text-muted mb-0">{{ $desc }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-muted mt-4 mb-0"><x-ti name="calendar" /> {{ __('Target launch: Q3 2026') }}</p>
        </div>
    </div>
</x-tabler.layout>

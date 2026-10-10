<x-tabler.layout :title="__('Export Data')" :pretitle="__('Workspace')">
    <div class="row row-decks row-cards">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted mb-2"><x-ti name="file-text" class="icon icon-lg" /></div>
                    <h3 class="card-title">{{ __('Invoices') }}</h3>
                    <p class="text-muted small">{{ __('CSV semua invoice') }}</p>
                    <a href="{{ route('exports.invoices') }}" class="btn btn-primary"><x-ti name="download" /> {{ __('Invoices') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted mb-2"><x-ti name="wallet" class="icon icon-lg" /></div>
                    <h3 class="card-title">{{ __('Payments') }}</h3>
                    <p class="text-muted small">{{ __('CSV semua payment') }}</p>
                    <a href="{{ route('exports.payments') }}" class="btn btn-primary"><x-ti name="download" /> {{ __('Payments') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-muted mb-2"><x-ti name="users" class="icon icon-lg" /></div>
                    <h3 class="card-title">{{ __('Clients') }}</h3>
                    <p class="text-muted small">{{ __('CSV semua klien') }}</p>
                    <a href="{{ route('exports.clients') }}" class="btn btn-primary"><x-ti name="download" /> {{ __('Clients') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

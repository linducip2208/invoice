@section('title', __('Activation Successful'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-status-top bg-success"></div>
        <div class="card-body text-center">
            <div class="mb-2 text-success"><x-ti name="check" class="icon icon-lg" /></div>
            <h2 class="h2 mb-1">{{ __('Activation Successful') }}</h2>
            <p class="text-muted">{{ __('The app is ready to use.') }}</p>
        </div>
        <div class="card-body border-top">
            <div class="datagrid">
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Product') }}</div>
                    <div class="datagrid-content">
                        <span class="fw-bold">{{ $data['product']['name'] ?? __('Application') }}</span>
                        <span class="text-muted">v{{ $data['product']['version'] ?? '1.0.0' }}</span>
                    </div>
                </div>
                <div class="datagrid-item">
                    <div class="datagrid-title">{{ __('Locked Domain') }}</div>
                    <div class="datagrid-content"><code>{{ $data['domain'] ?? '-' }}</code></div>
                </div>
                @if(!empty($data['license']['support_until']))
                    <div class="datagrid-item">
                        <div class="datagrid-title">{{ __('Support Active Until') }}</div>
                        <div class="datagrid-content">{{ \Illuminate\Support\Carbon::parse($data['license']['support_until'])->format('d M Y') }}</div>
                    </div>
                @endif
            </div>
            <div class="alert alert-success mt-3 mb-0">
                <div class="d-flex">
                    <div><x-ti name="shield-check" /></div>
                    <div><strong>{{ __('Confirm:') }}</strong> {{ __('The product name above must match what you purchased. If not, revoke it in the marketplace and re-pair with the correct key.') }}</div>
                </div>
            </div>
            <div class="form-footer mt-3">
                <a href="/" class="btn btn-dark w-100">{{ __('Enter Application') }} &rarr;</a>
            </div>
            <p class="text-center text-muted mt-2 mb-0">{{ __('Auto-redirect in 4 seconds...') }}</p>
        </div>
    </div>
    @push('scripts')
        <script>
            setTimeout(function() { window.location.href = '/'; }, 4000);
        </script>
    @endpush
</x-tabler.guest>

@section('title', __('Activate Application'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <div class="text-center mb-3">
                <span class="avatar avatar-lg mb-2"><x-ti name="lock" class="icon" /></span>
                <h2 class="h2 mb-1">{{ __('Activate Application') }}</h2>
                <p class="text-muted mb-0">{{ __('This app needs to be activated before use.') }}</p>
            </div>
            <form method="POST" action="/__pair" autocomplete="off" novalidate>
                @csrf
                @if($error)
                    <div class="alert alert-danger" role="alert">
                        <div class="d-flex">
                            <div><x-ti name="alert-circle" /></div>
                            <div>{{ $error }}</div>
                        </div>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label">{{ __('Detected Domain') }}</label>
                    <div class="d-flex align-items-center gap-2 p-2 border rounded bg-muted-lt">
                        <x-ti name="globe" />
                        <code>{{ $domain }}</code>
                        <span class="ms-auto text-muted">{{ __('auto') }}</span>
                    </div>
                    <small class="form-hint">{{ __('Domain is detected automatically from your browser — it cannot be changed manually.') }}</small>
                </div>
                <x-tabler.field :label="__('Activation Key')" name="activation_key" :help="__('Format: 4 groups of 5 characters separated by hyphens.')" :required="true">
                    <input type="text" id="activation_key" name="activation_key" value="{{ $old_key }}" placeholder="XXXXX-XXXXX-XXXXX-XXXXX" autocomplete="off" autofocus oninput="this.value = this.value.toUpperCase()" class="form-control font-monospace text-center @error('activation_key') is-invalid @enderror">
                </x-tabler.field>
                <div class="form-footer">
                    <button type="submit" id="submitBtn" class="btn btn-primary w-100"><x-ti name="arrow-right" /> <span id="submitText">{{ __('Activate') }}</span></button>
                </div>
            </form>
        </div>
        <div class="hr-text">{{ __('Need a key?') }}</div>
        <div class="card-body text-center text-muted">
            {{ __('Open the marketplace, log in, then copy the key from your license card.') }}
            <a href="{{ $marketplace_url }}/user/licenses" target="_blank" rel="noopener">{{ __('Open marketplace') }} &rarr;</a>
        </div>
    </div>
    <div class="text-center text-muted mt-3">
        {{ __('After activation, a .license.lock file is created automatically.') }}
    </div>
    @push('scripts')
        <script>
            document.querySelector('form')?.addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                const text = document.getElementById('submitText');
                btn.disabled = true;
                text.textContent = @json(__('Validating key...'));
            });
        </script>
    @endpush
</x-tabler.guest>

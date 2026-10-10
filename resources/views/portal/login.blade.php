@section('title', __('Customer Portal — Login'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-1"><x-ti name="layout-grid" /> {{ __('Customer Portal') }}</h2>
            <p class="text-muted text-center mb-4">{{ __('Access all your invoices and quotes') }}</p>
            <form method="POST" action="{{ route('portal.login.submit') }}" autocomplete="off" novalidate>
                @csrf
                <x-tabler.field :label="__('Email')" name="email" type="email" :value="old('email')" attrs="required autofocus autocomplete=email" />
                <div class="mb-3">
                    <label class="form-label" for="password">{{ __('Password') }}</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" value="1">
                        <span class="form-check-label">{{ __('Remember me') }}</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100"><x-ti name="login" /> {{ __('Log in') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center text-muted mt-3">
        {{ __('No account yet? Ask your provider for portal access.') }}
    </div>
    <div class="text-center text-muted mt-2">
        <a href="/">{{ __('Back to home') }}</a>
    </div>
</x-tabler.guest>

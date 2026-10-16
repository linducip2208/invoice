@section('title', __('Log in'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-1">{{ __('Log in to your account') }}</h2>
            <p class="text-muted text-center mb-4">
                {{ __("Don't have an account yet?") }}
                @if(Route::has('register'))
                    <a href="{{ route('register') }}">{{ __('Sign up') }}</a>
                @endif
            </p>
            <form method="POST" action="{{ route('login') }}" autocomplete="off" novalidate>
                @csrf
                <x-tabler.field :label="__('Email address')" name="email" type="email" :value="old('email')"
                    attrs="required autofocus autocomplete=email placeholder=email@example.com" />
                <div class="mb-2">
                    <label class="form-label" for="password">
                        {{ __('Password') }}
                        @if(Route::has('password.request'))
                            <span class="form-label-description">
                                <a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                            </span>
                        @endif
                    </label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        placeholder="••••••••" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember">
                        <span class="form-check-label">{{ __('Remember me') }}</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Log in') }}</button>
                </div>
            </form>
        </div>
    </div>
    @if(app()->isLocal())
        <div class="card card-md mt-3">
            <div class="card-body">
                <h3 class="card-title">{{ __('Demo Login') }}</h3>
                <p class="text-muted">{{ __('Local environment only — never shown in production.') }}</p>
                <dl class="row g-2 mb-0">
                    <dt class="col-12 text-muted small">{{ __('Superadmin') }}</dt>
                    <dd class="col-12"><code class="text-break">superadmin@invocely.test / superadmin123</code></dd>
                    <dt class="col-12 text-muted small">{{ __('Demo Admin') }}</dt>
                    <dd class="col-12"><code class="text-break">demo@invocely.test / password</code></dd>
                </dl>
            </div>
        </div>
    @endif
    <div class="text-center text-muted mt-3">
        <a href="{{ route('home') }}">{{ __('Back to home') }}</a>
    </div>
</x-tabler.guest>

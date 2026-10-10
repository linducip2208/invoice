@section('title', __('Forgot password'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">{{ __('Forgot password') }}</h2>
            <p class="text-muted mb-4">
                {{ __('Enter your email address and we will send you a link to reset your password.') }}
            </p>
            <form method="POST" action="{{ route('password.email') }}" autocomplete="off" novalidate>
                @csrf
                <x-tabler.field :label="__('Email address')" name="email" type="email" :value="old('email')"
                    attrs="required autofocus placeholder=email@example.com" />
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <x-ti name="mail" /> {{ __('Send reset link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center text-muted mt-3">
        <a href="{{ route('login') }}">{{ __('Back to log in') }}</a>
    </div>
</x-tabler.guest>

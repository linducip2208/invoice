@section('title', __('Sign up'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-1">{{ __('Create new account') }}</h2>
            <p class="text-muted text-center mb-4">
                {{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Log in') }}</a>
            </p>
            <form method="POST" action="{{ route('register') }}" autocomplete="off" novalidate>
                @csrf
                <x-tabler.field :label="__('Workspace / Company name')" name="company_name" type="text" :value="old('company_name')"
                    attrs="required autofocus autocomplete=organization" />
                <x-tabler.field :label="__('Your name')" name="name" type="text" :value="old('name')"
                    attrs="required autocomplete=name" />
                <x-tabler.field :label="__('Email address')" name="email" type="email" :value="old('email')"
                    attrs="required autocomplete=username" />
                <div class="row">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Password')" name="password" type="password" attrs="required autocomplete=new-password" />
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required" for="password_confirmation">{{ __('Confirm password') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                autocomplete="new-password" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Create account') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center text-muted mt-3">
        <a href="{{ route('home') }}">{{ __('Back to home') }}</a>
    </div>
</x-tabler.guest>

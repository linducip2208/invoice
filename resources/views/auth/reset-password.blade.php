@section('title', __('Reset password'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">{{ __('Reset password') }}</h2>
            <form method="POST" action="{{ route('password.store') }}" autocomplete="off" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <x-tabler.field :label="__('Email address')" name="email" type="email" :value="old('email', $request->email)"
                    attrs="required autofocus autocomplete=username" />
                <x-tabler.field :label="__('New password')" name="password" type="password" attrs="required autocomplete=new-password" />
                <div class="mb-3">
                    <label class="form-label required" for="password_confirmation">{{ __('Confirm new password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        autocomplete="new-password" class="form-control">
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Reset password') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center text-muted mt-3">
        <a href="{{ route('login') }}">{{ __('Back to log in') }}</a>
    </div>
</x-tabler.guest>

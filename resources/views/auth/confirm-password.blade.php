@section('title', __('Confirm password'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">{{ __('Confirm password') }}</h2>
            <p class="text-muted mb-4">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>
            <form method="POST" action="{{ route('password.confirm') }}" autocomplete="off" novalidate>
                @csrf
                <x-tabler.field :label="__('Password')" name="password" type="password" attrs="required autofocus autocomplete=current-password" />
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">{{ __('Confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-tabler.guest>

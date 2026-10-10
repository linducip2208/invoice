@section('title', __('Verify email'))
<x-tabler.guest>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">{{ __('Verify your email') }}</h2>
            <p class="text-muted mb-4">
                {{ __("Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn't receive the email, we will gladly send you another.") }}
            </p>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('verification.send') }}" class="flex-fill">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">{{ __('Resend verification email') }}</button>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>
    </div>
</x-tabler.guest>

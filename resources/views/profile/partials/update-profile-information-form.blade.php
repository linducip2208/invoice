<section>
    <p class="text-muted mb-3">{{ __("Update your account's name and email address.") }}</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf @method('patch')

        <x-tabler.field :label="__('Name')" name="name">
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                class="form-control @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </x-tabler.field>

        <x-tabler.field :label="__('Email')" name="email">
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                class="form-control @error('email') is-invalid @enderror">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </x-tabler.field>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning">
                <div>{{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="btn btn-sm ms-2">{{ __('Resend verification email →') }}</button>
                </div>
                @if (session('status') === 'verification-link-sent')
                    <div class="mt-2 text-success">{{ __('A new verification link has been sent.') }}</div>
                @endif
            </div>
        @endif

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <x-ti name="check" /> {{ __('Save') }}
            </button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-success mb-0">{{ __('Saved!') }}</p>
            @endif
        </div>
    </form>
</section>

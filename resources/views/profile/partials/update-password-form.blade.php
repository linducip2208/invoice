<section>
    <p class="text-muted mb-3">{{ __('Use a long, random password to keep your magic safe.') }}</p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf @method('put')

        <x-tabler.field :label="__('Current Password')" name="current_password">
            <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password"
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
            @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </x-tabler.field>
        <x-tabler.field :label="__('New Password')" name="password">
            <input type="password" id="update_password_password" name="password" autocomplete="new-password"
                class="form-control @error('password', 'updatePassword') is-invalid @enderror">
            @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </x-tabler.field>
        <x-tabler.field :label="__('Confirm Password')" name="password_confirmation">
            <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password"
                class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror">
            @error('password_confirmation', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </x-tabler.field>
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <x-ti name="check" /> {{ __('Save') }}
            </button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-success mb-0">{{ __('Saved!') }}</p>
            @endif
        </div>
    </form>
</section>

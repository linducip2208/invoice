<section>
    <p class="text-muted mb-3">
        <x-ti name="alert-triangle" class="icon text-danger" />
        {{ __('Once deleted, all your data vanishes forever. Please download anything you need first.') }}
    </p>

    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
        <x-ti name="trash" /> {{ __('Delete Account') }}
    </button>

    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf @method('delete')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Are you sure?') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">{{ __('This will permanently erase your account and all magic associated with it. Enter your password to confirm.') }}</p>
                        <x-tabler.field :label="__('Password')" name="password">
                            <input type="password" id="password" name="password" placeholder="{{ __('Password') }}"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror">
                            @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </x-tabler.field>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">
                            <x-ti name="trash" /> {{ __('Delete Forever') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->userDeletion->isNotEmpty())
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('confirmUserDeletion');
                if (el && window.bootstrap) { new bootstrap.Modal(el).show(); }
            });
        </script>
        @endpush
    @endif
</section>

<section class="space-y-5">
    <header>
        <h2 class="font-display text-lg font-bold text-rose-700 flex items-center gap-2">
            <span>⚠️</span> {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm text-rose-600">
            {{ __('Once deleted, all your data vanishes forever. Please download anything you need first.') }}
        </p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        🗑️ {{ __('Delete Account') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf @method('delete')

            <h2 class="font-display text-xl font-bold text-rose-700 flex items-center gap-2">
                <span>⚠️</span> {{ __('Are you sure?') }}
            </h2>
            <p class="mt-2 text-sm text-purple-700">
                {{ __('This will permanently erase your account and all magic associated with it. Enter your password to confirm.') }}
            </p>

            <div class="mt-5">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4" placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                <x-danger-button>🗑️ {{ __('Delete Forever') }}</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

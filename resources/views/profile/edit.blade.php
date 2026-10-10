<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="heading-unicorn flex items-center gap-2">{{ __('Profile') }} <span class="text-2xl">🦄</span></h2>
            <p class="text-sm text-purple-500 mt-1">✦ {{ __('Manage your sparkly identity') }}</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="card-unicorn">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card-unicorn">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card-unicorn" style="background: linear-gradient(135deg, rgba(254,226,226,0.5), rgba(252,165,165,0.3)); border-color: rgba(248,113,113,0.4);">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>

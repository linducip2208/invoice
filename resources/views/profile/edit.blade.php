<x-tabler.layout :title="__('Profile')" :pretitle="__('Account')">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">{{ __('Profile Information') }}</h3></div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">{{ __('Update Password') }}</h3></div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            <div class="card card-borderless bg-red-lt mb-3">
                <div class="card-header"><h3 class="card-title text-danger">{{ __('Delete Account') }}</h3></div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

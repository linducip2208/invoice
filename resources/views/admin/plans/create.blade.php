<x-tabler.admin-layout :title="__('New Plan')" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <a href="{{ route('admin.plans.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Plans') }}
        </a>
    </x-slot>

    @include('admin.plans._form')
</x-tabler.admin-layout>

<x-tabler.admin-layout :title="__('Edit Plan') . ': ' . $plan->name" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <a href="{{ route('admin.plans.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Plans') }}
        </a>
    </x-slot>

    @include('admin.plans._form', ['plan' => $plan])
</x-tabler.admin-layout>

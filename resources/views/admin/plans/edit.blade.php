<x-admin-layout title="Edit Plan: {{ $plan->name }}">
    @include('admin.plans._form', ['plan' => $plan])
</x-admin-layout>

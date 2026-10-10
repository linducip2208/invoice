<x-tabler.layout :title="__('Add Client')">
    <x-slot name="actions">
        <a href="{{ route('clients.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Clients') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add Client') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('clients.store') }}">
                @csrf
                @include('clients._form')
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('clients.index') }}" class="btn">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save Client') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

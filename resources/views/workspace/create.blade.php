<x-tabler.layout :title="__('Create Workspace')" :pretitle="__('Workspace')">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <form method="POST" action="{{ route('workspace.store') }}">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Workspace Details') }}</h3>
                        <div class="card-subtitle">{{ __('This information appears on every invoice you create.') }}</div>
                    </div>
                    <div class="card-body">
                        <x-tabler.field :label="__('Workspace / Company Name *')" name="name" :required="true">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="PT Maju Bersama" class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </x-tabler.field>
                        <x-tabler.field :label="__('Default Currency *')" name="currency" :required="true">
                            <select id="currency" name="currency" required class="form-select @error('currency') is-invalid @enderror">
                                @foreach (['IDR' => 'IDR — Indonesian Rupiah', 'USD' => 'USD — US Dollar', 'EUR' => 'EUR — Euro', 'GBP' => 'GBP — British Pound', 'SGD' => 'SGD — Singapore Dollar', 'AUD' => 'AUD — Australian Dollar', 'JPY' => 'JPY — Japanese Yen', 'CNY' => 'CNY — Chinese Yuan'] as $code => $label)
                                    <option value="{{ $code }}" @selected(old('currency', 'IDR') === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </x-tabler.field>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <x-ti name="plus" /> {{ __('Create Workspace') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

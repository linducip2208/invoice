<x-tabler.layout :title="__('Project Baru')">
    <x-slot name="actions">
        <a href="{{ route('projects.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Semua Project') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Project Baru') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Nama Project *')" name="name" type="text" :value="old('name')"
                            attrs="required" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Klien *')" name="client_id">
                            <select name="client_id" id="client_id" required class="form-select @error('client_id') is-invalid @enderror">
                                <option value="">{{ __('Pilih klien…') }}</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                </div>

                <x-tabler.field :label="__('Deskripsi')" name="description">
                    <textarea name="description" id="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                </x-tabler.field>

                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Status *')" name="status">
                            <select name="status" id="status" class="form-select">
                                <option value="active" @selected(old('status') === 'active')>Active</option>
                                <option value="on_hold" @selected(old('status') === 'on_hold')>On Hold</option>
                                <option value="completed" @selected(old('status') === 'completed')>Completed</option>
                                <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Budget (Rp)')" name="budget_amount" type="number" :value="old('budget_amount')"
                            attrs='step="0.01" min="0"' />
                    </div>
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Budget (Jam)')" name="budget_hours" type="number" :value="old('budget_hours')"
                            attrs='step="0.01" min="0"' />
                    </div>
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Tarif/Jam')" name="hourly_rate" type="number" :value="old('hourly_rate')"
                            attrs='step="0.01" min="0"' />
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4 col-6">
                        <x-tabler.field :label="__('Tgl Mulai')" name="start_date" type="date" :value="old('start_date')" />
                    </div>
                    <div class="col-md-4 col-6">
                        <x-tabler.field :label="__('Tgl Selesai')" name="end_date" type="date" :value="old('end_date')" />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Warna Tag')" name="color">
                            <input type="color" name="color" id="color" value="{{ old('color', '#A78BFA') }}"
                                class="form-control form-control-color w-100">
                        </x-tabler.field>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('projects.index') }}" class="btn">{{ __('Batal') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Buat Project') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

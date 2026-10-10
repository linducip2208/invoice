<x-tabler.layout :title="__('Edit Project')">
    <x-slot name="actions">
        <a href="{{ route('projects.show', $project) }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Batal') }}
        </a>
        <x-tabler.delete-button :action="route('projects.destroy', $project)"
            :confirm="__('Hapus project? Invoice & data lainnya tetap aman.')" :label="__('Hapus Project')" />
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Edit Project') }} — {{ $project->name }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Nama *')" name="name" type="text" :value="old('name', $project->name)"
                            attrs="required" />
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Klien *')" name="client_id">
                            <select name="client_id" id="client_id" required class="form-select @error('client_id') is-invalid @enderror">
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_id', $project->client_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                </div>

                <x-tabler.field :label="__('Deskripsi')" name="description">
                    <textarea name="description" id="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description) }}</textarea>
                </x-tabler.field>

                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Status')" name="status">
                            <select name="status" id="status" class="form-select">
                                @foreach(['active','on_hold','completed','cancelled'] as $s)
                                    <option value="{{ $s }}" @selected(old('status', $project->status) === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Budget (Rp)')" name="budget_amount" type="number" :value="old('budget_amount', $project->budget_amount)"
                            attrs='step="0.01" min="0"' />
                    </div>
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Budget (Jam)')" name="budget_hours" type="number" :value="old('budget_hours', $project->budget_hours)"
                            attrs='step="0.01" min="0"' />
                    </div>
                    <div class="col-md-3 col-6">
                        <x-tabler.field :label="__('Tarif/Jam')" name="hourly_rate" type="number" :value="old('hourly_rate', $project->hourly_rate)"
                            attrs='step="0.01" min="0"' />
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4 col-6">
                        <x-tabler.field :label="__('Tgl Mulai')" name="start_date" type="date" :value="old('start_date', $project->start_date?->format('Y-m-d'))" />
                    </div>
                    <div class="col-md-4 col-6">
                        <x-tabler.field :label="__('Tgl Selesai')" name="end_date" type="date" :value="old('end_date', $project->end_date?->format('Y-m-d'))" />
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Warna')" name="color">
                            <input type="color" name="color" id="color" value="{{ old('color', $project->color ?? '#A78BFA') }}"
                                class="form-control form-control-color w-100">
                        </x-tabler.field>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('projects.show', $project) }}" class="btn">{{ __('Batal') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>

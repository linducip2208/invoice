<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Edit Project') }} — {{ $project->name }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('projects.update', $project) }}" class="card-unicorn !p-8 space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Nama *')" />
                    <x-text-input name="name" :value="old('name', $project->name)" required class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Klien *')" />
                    <select name="client_id" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id', $project->client_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <x-input-label :value="__('Deskripsi')" />
                <textarea name="description" rows="3" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <x-input-label :value="__('Status')" />
                    <select name="status" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        @foreach(['active','on_hold','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $project->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Budget (Rp)')" />
                    <x-text-input name="budget_amount" type="number" step="0.01" min="0" :value="old('budget_amount', $project->budget_amount)" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Budget (Jam)')" />
                    <x-text-input name="budget_hours" type="number" step="0.01" min="0" :value="old('budget_hours', $project->budget_hours)" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Tarif/Jam')" />
                    <x-text-input name="hourly_rate" type="number" step="0.01" min="0" :value="old('hourly_rate', $project->hourly_rate)" class="mt-1 w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label :value="__('Tgl Mulai')" />
                    <x-text-input name="start_date" type="date" :value="old('start_date', $project->start_date?->format('Y-m-d'))" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Tgl Selesai')" />
                    <x-text-input name="end_date" type="date" :value="old('end_date', $project->end_date?->format('Y-m-d'))" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Warna')" />
                    <input type="color" name="color" value="{{ $project->color ?? '#A78BFA' }}" class="h-10 w-full rounded-xl border-2 border-purple-200 cursor-pointer mt-1">
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Hapus project? Invoice & data lainnya tetap aman.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-rose-500 font-semibold hover:underline">🗑️ {{ __('Hapus Project') }}</button>
                </form>
                <div class="flex gap-3">
                    <a href="{{ route('projects.show', $project) }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                    <button type="submit" class="btn-unicorn px-8">{{ __('Simpan') }}</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

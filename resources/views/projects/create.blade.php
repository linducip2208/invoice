<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Project Baru') }} <span class="text-2xl">📁</span></h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('projects.store') }}" class="card-unicorn !p-8 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Nama Project *')" />
                    <x-text-input name="name" :value="old('name')" required class="mt-1 w-full" />
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label :value="__('Klien *')" />
                    <select name="client_id" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        <option value="">{{ __('Pilih klien…') }}</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-input-label :value="__('Deskripsi')" />
                <textarea name="description" rows="3" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <x-input-label :value="__('Status *')" />
                    <select name="status" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        <option value="active">Active</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Budget (Rp)')" />
                    <x-text-input name="budget_amount" type="number" step="0.01" min="0" :value="old('budget_amount')" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Budget (Jam)')" />
                    <x-text-input name="budget_hours" type="number" step="0.01" min="0" :value="old('budget_hours')" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Tarif/Jam')" />
                    <x-text-input name="hourly_rate" type="number" step="0.01" min="0" :value="old('hourly_rate')" class="mt-1 w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label :value="__('Tgl Mulai')" />
                    <x-text-input name="start_date" type="date" :value="old('start_date')" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Tgl Selesai')" />
                    <x-text-input name="end_date" type="date" :value="old('end_date')" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Warna Tag')" />
                    <input type="color" name="color" value="{{ old('color', '#A78BFA') }}" class="h-10 w-full rounded-xl border-2 border-purple-200 cursor-pointer mt-1">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('projects.index') }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                <button type="submit" class="btn-unicorn px-8">✦ {{ __('Buat Project') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>

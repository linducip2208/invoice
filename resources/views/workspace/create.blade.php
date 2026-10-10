<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="heading-unicorn flex items-center gap-2">{{ __('Create Workspace') }} <span class="text-2xl">✨</span></h2>
            <p class="text-sm text-purple-500 mt-1">✦ {{ __('Set up your magical realm to get started') }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('workspace.store') }}" class="space-y-6">
            @csrf

            <div class="card-unicorn">
                <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
                <h3 class="font-display font-bold text-lg text-purple-900">✦ {{ __('Workspace Details') }}</h3>
                <p class="text-sm text-purple-500 mt-1 mb-5">{{ __('This information appears on every invoice you create.') }}</p>

                <div class="space-y-5">
                    <div>
                        <x-input-label :value="__('Workspace / Company Name *')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="PT Maju Bersama" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Default Currency *')" />
                        <select name="currency" class="input-unicorn rounded-xl px-4 py-2 w-full mt-1" required>
                            @foreach (['IDR' => 'IDR — Indonesian Rupiah', 'USD' => 'USD — US Dollar', 'EUR' => 'EUR — Euro', 'GBP' => 'GBP — British Pound', 'SGD' => 'SGD — Singapore Dollar', 'AUD' => 'AUD — Australian Dollar', 'JPY' => 'JPY — Japanese Yen', 'CNY' => 'CNY — Chinese Yuan'] as $code => $label)
                                <option value="{{ $code }}" @selected(old('currency', 'IDR') === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="submit" class="btn-unicorn">✦ {{ __('Create Workspace') }}</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

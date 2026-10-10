<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Edit Provider') }} — {{ $provider->name }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('ai-providers.update', $provider) }}" class="card-unicorn !p-8 space-y-5">
            @csrf @method('PUT')

            <div>
                <x-input-label :value="__('Nama *')" />
                <x-text-input name="name" :value="old('name', $provider->name)" required class="mt-1 w-full" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('API Format *')" />
                    <select name="api_format" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        @foreach(['openai_compatible','anthropic','gemini','image_generic','ocr_generic'] as $f)
                            <option value="{{ $f }}" {{ $provider->api_format === $f ? 'selected' : '' }}>{{ str_replace('_',' ', ucfirst($f)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Default Model')" />
                    <x-text-input name="default_model" :value="old('default_model', $provider->default_model)" class="mt-1 w-full" />
                </div>
            </div>

            <div>
                <x-input-label :value="__('Base URL *')" />
                <x-text-input name="base_url" type="url" :value="old('base_url', $provider->base_url)" required class="mt-1 w-full" />
            </div>

            <div>
                <x-input-label :value="__('API Key (kosongin kalau tidak ganti)')" />
                <x-text-input name="api_key" type="password" class="mt-1 w-full font-mono" placeholder="•••••••• tetap" autocomplete="new-password" />
                <p class="text-xs text-purple-400 mt-1">🔐 Existing key tersimpan ter-encrypt. Isi field ini hanya kalau mau ganti key.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Pricing Input ($/1k token)')" />
                    <x-text-input name="default_pricing_input" type="number" step="0.000001" min="0" :value="old('default_pricing_input', $provider->default_pricing_input)" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Pricing Output ($/1k token)')" />
                    <x-text-input name="default_pricing_output" type="number" step="0.000001" min="0" :value="old('default_pricing_output', $provider->default_pricing_output)" class="mt-1 w-full" />
                </div>
            </div>

            <div>
                <x-input-label :value="__('Extra Headers (JSON)')" />
                <textarea name="extra_headers" rows="2" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full font-mono text-xs">{{ old('extra_headers', $provider->extra_headers ? json_encode($provider->extra_headers) : '') }}</textarea>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $provider->is_active ? 'checked' : '' }} class="rounded border-purple-300 text-pink-500 focus:ring-pink-300">
                <span class="text-sm font-semibold text-purple-700">Aktif</span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('ai-providers.index') }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                <button type="submit" class="btn-unicorn px-8">{{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>

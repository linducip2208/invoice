<x-tabler.layout :title="__('Edit Provider')" :pretitle="__('Automation')" :subtitle="$provider->name">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('ai-providers.update', $provider) }}">
                        @csrf @method('PUT')

                        <x-tabler.field :label="__('Nama *')" name="name" :required="true">
                            <input type="text" id="name" name="name" value="{{ old('name', $provider->name) }}" required class="form-control">
                        </x-tabler.field>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-tabler.field :label="__('API Format *')" name="api_format" :required="true">
                                    <select id="api_format" name="api_format" required class="form-select">
                                        @foreach(['openai_compatible','anthropic','gemini','image_generic','ocr_generic'] as $f)
                                            <option value="{{ $f }}" {{ $provider->api_format === $f ? 'selected' : '' }}>{{ str_replace('_',' ', ucfirst($f)) }}</option>
                                        @endforeach
                                    </select>
                                </x-tabler.field>
                            </div>
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Default Model')" name="default_model">
                                    <input type="text" id="default_model" name="default_model" value="{{ old('default_model', $provider->default_model) }}" class="form-control">
                                </x-tabler.field>
                            </div>
                        </div>

                        <x-tabler.field :label="__('Base URL *')" name="base_url" :required="true">
                            <input type="url" id="base_url" name="base_url" value="{{ old('base_url', $provider->base_url) }}" required class="form-control">
                        </x-tabler.field>

                        <x-tabler.field :label="__('API Key (kosongin kalau tidak ganti)')" name="api_key" :help="__('Existing key tersimpan ter-encrypt. Isi field ini hanya kalau mau ganti key.')">
                            <input type="password" id="api_key" name="api_key" placeholder="••••••••" autocomplete="new-password" class="form-control font-monospace">
                        </x-tabler.field>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Pricing Input ($/1k token)')" name="default_pricing_input">
                                    <input type="number" id="default_pricing_input" name="default_pricing_input" step="0.000001" min="0" value="{{ old('default_pricing_input', $provider->default_pricing_input) }}" class="form-control">
                                </x-tabler.field>
                            </div>
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Pricing Output ($/1k token)')" name="default_pricing_output">
                                    <input type="number" id="default_pricing_output" name="default_pricing_output" step="0.000001" min="0" value="{{ old('default_pricing_output', $provider->default_pricing_output) }}" class="form-control">
                                </x-tabler.field>
                            </div>
                        </div>

                        <x-tabler.field :label="__('Extra Headers (JSON)')" name="extra_headers">
                            <textarea id="extra_headers" name="extra_headers" rows="2" class="form-control font-monospace small">{{ old('extra_headers', $provider->extra_headers ? json_encode($provider->extra_headers) : '') }}</textarea>
                        </x-tabler.field>

                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" {{ $provider->is_active ? 'checked' : '' }} class="form-check-input">
                                <span class="form-check-label">{{ __('Aktif') }}</span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('ai-providers.index') }}" class="btn">{{ __('Batal') }}</a>
                            <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Simpan') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

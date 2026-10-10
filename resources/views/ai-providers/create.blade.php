<x-tabler.layout :title="__('Tambah AI Provider')" :pretitle="__('Automation')">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">{{ __('Preset Cepat') }}</h3></div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('Klik untuk autofill — kamu bebas edit setelahnya.') }}</p>
                    <div class="d-flex flex-wrap gap-2" x-data>
                        <button type="button" class="btn btn-sm" @click="$dispatch('preset', { name: 'OpenAI', format: 'openai_compatible', url: 'https://api.openai.com/v1', model: 'gpt-4o-mini' })">OpenAI</button>
                        <button type="button" class="btn btn-sm" @click="$dispatch('preset', { name: 'Groq', format: 'openai_compatible', url: 'https://api.groq.com/openai/v1', model: 'llama-3.3-70b-versatile' })">Groq</button>
                        <button type="button" class="btn btn-sm" @click="$dispatch('preset', { name: 'DeepSeek', format: 'openai_compatible', url: 'https://api.deepseek.com', model: 'deepseek-chat' })">DeepSeek</button>
                        <button type="button" class="btn btn-sm" @click="$dispatch('preset', { name: 'Anthropic', format: 'anthropic', url: 'https://api.anthropic.com', model: 'claude-sonnet-4-6' })">Anthropic</button>
                        <button type="button" class="btn btn-sm" @click="$dispatch('preset', { name: 'Gemini', format: 'gemini', url: 'https://generativelanguage.googleapis.com', model: 'gemini-2.0-flash' })">Gemini</button>
                        <button type="button" class="btn btn-sm" @click="$dispatch('preset', { name: 'Ollama (local)', format: 'openai_compatible', url: 'http://localhost:11434/v1', model: 'llama3' })">Ollama</button>
                        <button type="button" class="btn btn-sm" @click="$dispatch('preset', { name: 'OpenRouter', format: 'openai_compatible', url: 'https://openrouter.ai/api/v1', model: 'meta-llama/llama-3.3-70b-instruct' })">OpenRouter</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('ai-providers.store') }}"
                        x-data="{ name: '', format: 'openai_compatible', url: '', model: '' }"
                        @preset.window="name = $event.detail.name; format = $event.detail.format; url = $event.detail.url; model = $event.detail.model">
                        @csrf

                        <x-tabler.field :label="__('Nama Provider *')" name="name" :required="true">
                            <input type="text" id="name" name="name" x-model="name" required placeholder="Mis. OpenAI Production / Groq Free Tier"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </x-tabler.field>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-tabler.field :label="__('API Format *')" name="api_format" :required="true" :help="__('OpenAI Compatible mencakup OpenAI, Groq, DeepSeek, Mistral, Together, Fireworks, OpenRouter, Ollama, LM Studio, dll.')">
                                    <select id="api_format" name="api_format" x-model="format" required class="form-select">
                                        <option value="openai_compatible">OpenAI Compatible</option>
                                        <option value="anthropic">Anthropic</option>
                                        <option value="gemini">Gemini</option>
                                        <option value="image_generic">Image Generic</option>
                                        <option value="ocr_generic">OCR Generic</option>
                                    </select>
                                </x-tabler.field>
                            </div>
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Default Model')" name="default_model">
                                    <input type="text" id="default_model" name="default_model" x-model="model" placeholder="gpt-4o-mini, claude-sonnet-4-6, llama-3.3-70b" class="form-control">
                                </x-tabler.field>
                            </div>
                        </div>

                        <x-tabler.field :label="__('Base URL *')" name="base_url" :required="true">
                            <input type="url" id="base_url" name="base_url" x-model="url" required placeholder="https://api.openai.com/v1"
                                class="form-control @error('base_url') is-invalid @enderror">
                            @error('base_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </x-tabler.field>

                        <x-tabler.field :label="__('API Key *')" name="api_key" :required="true" :help="__('Disimpan ter-encrypt di database. Tidak pernah ditampilkan ulang.')">
                            <input type="password" id="api_key" name="api_key" required placeholder="sk-..." autocomplete="new-password" class="form-control font-monospace">
                        </x-tabler.field>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Pricing Input ($/1k token)')" name="default_pricing_input">
                                    <input type="number" id="default_pricing_input" name="default_pricing_input" step="0.000001" min="0" value="{{ old('default_pricing_input', '0.00015') }}" class="form-control">
                                </x-tabler.field>
                            </div>
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Pricing Output ($/1k token)')" name="default_pricing_output">
                                    <input type="number" id="default_pricing_output" name="default_pricing_output" step="0.000001" min="0" value="{{ old('default_pricing_output', '0.0006') }}" class="form-control">
                                </x-tabler.field>
                            </div>
                        </div>

                        <x-tabler.field :label="__('Extra Headers (JSON, opsional)')" name="extra_headers">
                            <textarea id="extra_headers" name="extra_headers" rows="2" placeholder='{"x-custom-key": "value"}' class="form-control font-monospace small">{{ old('extra_headers') }}</textarea>
                            @error('extra_headers')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </x-tabler.field>

                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" checked class="form-check-input">
                                <span class="form-check-label">{{ __('Aktif') }}</span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('ai-providers.index') }}" class="btn">{{ __('Batal') }}</a>
                            <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Simpan Provider') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-tabler.layout>

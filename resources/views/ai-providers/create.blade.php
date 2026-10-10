<x-app-layout>
    <x-slot name="header">
        <h2 class="heading-unicorn flex items-center gap-2">{{ __('Tambah AI Provider') }} <span class="text-2xl">🤖</span></h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="card-unicorn !p-6 mb-6 bg-gradient-to-br from-pink-50/50 to-purple-50/50">
            <p class="font-display font-bold text-purple-900 mb-2">💡 Preset Cepat</p>
            <p class="text-sm text-purple-600 mb-3">Klik untuk autofill — kamu bebas edit setelahnya.</p>
            <div class="flex flex-wrap gap-2 text-xs" x-data>
                <button type="button" class="px-3 py-1.5 rounded-full bg-white/80 border border-purple-200 hover:border-pink-300" @click="$dispatch('preset', { name: 'OpenAI', format: 'openai_compatible', url: 'https://api.openai.com/v1', model: 'gpt-4o-mini' })">OpenAI</button>
                <button type="button" class="px-3 py-1.5 rounded-full bg-white/80 border border-purple-200 hover:border-pink-300" @click="$dispatch('preset', { name: 'Groq', format: 'openai_compatible', url: 'https://api.groq.com/openai/v1', model: 'llama-3.3-70b-versatile' })">Groq</button>
                <button type="button" class="px-3 py-1.5 rounded-full bg-white/80 border border-purple-200 hover:border-pink-300" @click="$dispatch('preset', { name: 'DeepSeek', format: 'openai_compatible', url: 'https://api.deepseek.com', model: 'deepseek-chat' })">DeepSeek</button>
                <button type="button" class="px-3 py-1.5 rounded-full bg-white/80 border border-purple-200 hover:border-pink-300" @click="$dispatch('preset', { name: 'Anthropic', format: 'anthropic', url: 'https://api.anthropic.com', model: 'claude-sonnet-4-6' })">Anthropic</button>
                <button type="button" class="px-3 py-1.5 rounded-full bg-white/80 border border-purple-200 hover:border-pink-300" @click="$dispatch('preset', { name: 'Gemini', format: 'gemini', url: 'https://generativelanguage.googleapis.com', model: 'gemini-2.0-flash' })">Gemini</button>
                <button type="button" class="px-3 py-1.5 rounded-full bg-white/80 border border-purple-200 hover:border-pink-300" @click="$dispatch('preset', { name: 'Ollama (local)', format: 'openai_compatible', url: 'http://localhost:11434/v1', model: 'llama3' })">Ollama</button>
                <button type="button" class="px-3 py-1.5 rounded-full bg-white/80 border border-purple-200 hover:border-pink-300" @click="$dispatch('preset', { name: 'OpenRouter', format: 'openai_compatible', url: 'https://openrouter.ai/api/v1', model: 'meta-llama/llama-3.3-70b-instruct' })">OpenRouter</button>
            </div>
        </div>

        <form method="POST" action="{{ route('ai-providers.store') }}" class="card-unicorn !p-8 space-y-5"
              x-data="{ name: '', format: 'openai_compatible', url: '', model: '' }"
              @preset.window="name = $event.detail.name; format = $event.detail.format; url = $event.detail.url; model = $event.detail.model">
            @csrf

            <div>
                <x-input-label :value="__('Nama Provider *')" />
                <x-text-input name="name" x-model="name" required class="mt-1 w-full" placeholder="Mis. OpenAI Production / Groq Free Tier" />
                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('API Format *')" />
                    <select name="api_format" x-model="format" required class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                        <option value="openai_compatible">OpenAI Compatible</option>
                        <option value="anthropic">Anthropic</option>
                        <option value="gemini">Gemini</option>
                        <option value="image_generic">Image Generic</option>
                        <option value="ocr_generic">OCR Generic</option>
                    </select>
                    <p class="text-xs text-purple-400 mt-1">{{ __('OpenAI Compatible mencakup OpenAI, Groq, DeepSeek, Mistral, Together, Fireworks, OpenRouter, Ollama, LM Studio, dll.') }}</p>
                </div>
                <div>
                    <x-input-label :value="__('Default Model')" />
                    <x-text-input name="default_model" x-model="model" class="mt-1 w-full" placeholder="gpt-4o-mini, claude-sonnet-4-6, llama-3.3-70b" />
                </div>
            </div>

            <div>
                <x-input-label :value="__('Base URL *')" />
                <x-text-input name="base_url" type="url" x-model="url" required class="mt-1 w-full" placeholder="https://api.openai.com/v1" />
                @error('base_url') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-input-label :value="__('API Key *')" />
                <x-text-input name="api_key" type="password" required class="mt-1 w-full font-mono" placeholder="sk-..." autocomplete="new-password" />
                <p class="text-xs text-purple-400 mt-1">🔐 {{ __('Disimpan ter-encrypt di database. Tidak pernah ditampilkan ulang.') }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="__('Pricing Input ($/1k token)')" />
                    <x-text-input name="default_pricing_input" type="number" step="0.000001" min="0" :value="old('default_pricing_input', '0.00015')" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label :value="__('Pricing Output ($/1k token)')" />
                    <x-text-input name="default_pricing_output" type="number" step="0.000001" min="0" :value="old('default_pricing_output', '0.0006')" class="mt-1 w-full" />
                </div>
            </div>

            <div>
                <x-input-label :value="__('Extra Headers (JSON, opsional)')" />
                <textarea name="extra_headers" rows="2" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full font-mono text-xs" placeholder='{"x-custom-key": "value"}'>{{ old('extra_headers') }}</textarea>
                @error('extra_headers') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-purple-300 text-pink-500 focus:ring-pink-300">
                <span class="text-sm font-semibold text-purple-700">Aktif</span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('ai-providers.index') }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                <button type="submit" class="btn-unicorn px-8">✦ {{ __('Simpan Provider') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>

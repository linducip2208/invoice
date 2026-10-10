<x-admin-layout title="Branding & Theme">
    <p class="text-sm text-purple-500 mb-6">{{ __('Whitelabel — atur nama app, logo, warna, dan tema. Berlaku ke seluruh aplikasi (login, dashboard, public pages, email).') }}</p>

    <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data" x-data="brandingForm({{ Js::from($branding) }}, {{ Js::from($themes) }})" class="space-y-6 max-w-5xl">
        @csrf

        <!-- Identity -->
        <div class="card-unicorn space-y-4">
            <h3 class="font-display font-bold text-lg text-purple-900">✦ Identitas App</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-purple-700">App Name *</label>
                    <input type="text" name="app_name" x-model="form.app_name" required maxlength="100" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                    <p class="text-xs text-purple-400 mt-1">Misal: "InvoicePro", "Tagihan.id"</p>
                </div>
                <div>
                    <label class="text-sm font-bold text-purple-700">Tagline</label>
                    <input type="text" name="tagline" x-model="form.tagline" maxlength="255" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                    <p class="text-xs text-purple-400 mt-1">Tampil di landing page & footer</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-purple-700">Logo</label>
                    <div class="mt-1 flex items-center gap-3">
                        @if(branding('logo_path'))
                            <img src="{{ asset('storage/' . branding('logo_path')) }}" alt="logo" class="h-12 w-auto rounded-lg border-2 border-purple-200 bg-white p-1">
                        @else
                            <div class="h-12 w-12 rounded-lg border-2 border-dashed border-purple-300 flex items-center justify-center text-purple-400">—</div>
                        @endif
                        <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="text-xs">
                    </div>
                    @if(branding('logo_path'))
                        <label class="flex items-center gap-2 text-xs text-rose-600 mt-2">
                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-rose-300"> Hapus logo
                        </label>
                    @endif
                    <p class="text-xs text-purple-400 mt-1">PNG/SVG, max 1MB. Recommended 200×200 atau wider rectangle.</p>
                </div>
                <div>
                    <label class="text-sm font-bold text-purple-700">Favicon</label>
                    <div class="mt-1 flex items-center gap-3">
                        @if(branding('favicon_path'))
                            <img src="{{ asset('storage/' . branding('favicon_path')) }}" alt="favicon" class="h-8 w-8 rounded border-2 border-purple-200 bg-white p-0.5">
                        @else
                            <div class="h-8 w-8 rounded border-2 border-dashed border-purple-300"></div>
                        @endif
                        <input type="file" name="favicon" accept="image/png,image/x-icon" class="text-xs">
                    </div>
                    @if(branding('favicon_path'))
                        <label class="flex items-center gap-2 text-xs text-rose-600 mt-2">
                            <input type="checkbox" name="remove_favicon" value="1" class="rounded border-rose-300"> Hapus favicon
                        </label>
                    @endif
                    <p class="text-xs text-purple-400 mt-1">PNG/ICO, max 512KB. 32×32 ideal.</p>
                </div>
            </div>
        </div>

        <!-- Theme preset -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ Theme Preset</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <template x-for="(t, slug) in themes" :key="slug">
                    <label class="cursor-pointer">
                        <input type="radio" name="theme_preset" :value="slug" x-model="form.theme_preset" @change="applyTheme(slug)" class="sr-only peer">
                        <div class="rounded-xl border-2 border-purple-100 peer-checked:border-pink-400 peer-checked:ring-2 peer-checked:ring-pink-200 p-3 transition">
                            <div class="flex gap-1 mb-2">
                                <span class="w-6 h-6 rounded-full" :style="`background:${t.primary || form.primary_color}`"></span>
                                <span class="w-6 h-6 rounded-full" :style="`background:${t.accent || form.accent_color}`"></span>
                                <span class="w-6 h-6 rounded-full" :style="`background:${t.sky || form.sky_color}`"></span>
                            </div>
                            <p class="text-xs font-semibold text-purple-700" x-text="t.label"></p>
                        </div>
                    </label>
                </template>
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-bold text-purple-700">Primary</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="color" x-model="form.primary_color" class="h-10 w-16 rounded-xl border-2 border-purple-200 cursor-pointer">
                        <input type="text" name="primary_color" x-model="form.primary_color" class="input-unicorn rounded-xl px-3 py-2 text-sm flex-1">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-bold text-purple-700">Accent</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="color" x-model="form.accent_color" class="h-10 w-16 rounded-xl border-2 border-purple-200 cursor-pointer">
                        <input type="text" name="accent_color" x-model="form.accent_color" class="input-unicorn rounded-xl px-3 py-2 text-sm flex-1">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-bold text-purple-700">Sky / Tertiary</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="color" x-model="form.sky_color" class="h-10 w-16 rounded-xl border-2 border-purple-200 cursor-pointer">
                        <input type="text" name="sky_color" x-model="form.sky_color" class="input-unicorn rounded-xl px-3 py-2 text-sm flex-1">
                    </div>
                </div>
            </div>
        </div>

        <!-- Typography -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ Typography</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-purple-700">Display Font (judul)</label>
                    <select name="font_display" x-model="form.font_display" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                        @foreach($fonts as $name => $label)
                            <option value="{{ $name }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-bold text-purple-700">Body Font (text utama)</label>
                    <select name="font_body" x-model="form.font_body" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                        @foreach($fonts as $name => $label)
                            <option value="{{ $name }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <p class="text-xs text-purple-400 mt-2">Font otomatis di-load dari fonts.bunny.net (Google Fonts mirror).</p>
        </div>

        <!-- Footer & Support -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ Footer & Support</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="text-sm font-bold text-purple-700">Footer Text</label>
                    <input type="text" name="footer_text" x-model="form.footer_text" maxlength="255" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                </div>
                <div>
                    <label class="text-sm font-bold text-purple-700">Support Email</label>
                    <input type="email" name="support_email" x-model="form.support_email" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-purple-700">Support URL</label>
                    <input type="url" name="support_url" x-model="form.support_url" placeholder="https://help.example.com" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                </div>
            </div>
        </div>

        <!-- Live preview -->
        <div class="card-unicorn">
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">✦ Live Preview</h3>
            <div class="rounded-xl p-6 border-2 border-purple-100"
                 :style="`background: linear-gradient(135deg, ${form.primary_color}10, ${form.accent_color}10, ${form.sky_color}10)`">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold shadow-lg"
                         :style="`background: linear-gradient(135deg, ${form.accent_color}, ${form.primary_color}, ${form.sky_color})`">
                        <span x-text="form.app_name?.[0] ?? 'X'"></span>
                    </div>
                    <div>
                        <p class="font-bold text-lg" :style="`color: ${form.primary_color}`" x-text="form.app_name || 'App Name'"></p>
                        <p class="text-xs text-purple-500" x-text="form.tagline || 'Tagline'"></p>
                    </div>
                </div>
                <button type="button" class="px-5 py-2 rounded-full text-white font-semibold text-sm" :style="`background: linear-gradient(135deg, ${form.accent_color}, ${form.primary_color}, ${form.sky_color})`">
                    Sample Button
                </button>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-unicorn">💾 Save Branding</button>
        </div>
    </form>

    <script>
        function brandingForm(initial, themes) {
            return {
                form: { ...initial },
                themes: themes,
                applyTheme(slug) {
                    const t = this.themes[slug];
                    if (t.primary) this.form.primary_color = t.primary;
                    if (t.accent)  this.form.accent_color  = t.accent;
                    if (t.sky)     this.form.sky_color     = t.sky;
                },
            };
        }
    </script>
</x-admin-layout>

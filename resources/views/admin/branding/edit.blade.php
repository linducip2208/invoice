<x-tabler.layout :title="__('Branding & Theme')" :pretitle="__('Superadmin')"
    :subtitle="__('Whitelabel — set the app name, logo, colors, and theme. Applies across the app (login, dashboard, public pages, email).')">
    <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data"
        x-data="brandingForm({{ Js::from($branding) }}, {{ Js::from($themes) }})">
        @csrf

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('App Identity') }}</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('App Name')" name="app_name" required :help="__('e.g. InvoicePro')">
                            <input type="text" id="app_name" name="app_name" x-model="form.app_name" required maxlength="100"
                                class="form-control @error('app_name') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Tagline')" name="tagline" :help="__('Shown on the landing page and footer.')">
                            <input type="text" id="tagline" name="tagline" x-model="form.tagline" maxlength="255"
                                class="form-control @error('tagline') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Logo')" name="logo" :help="__('PNG/SVG, max 1MB. 200×200 or a wider rectangle recommended.')">
                            <div class="d-flex align-items-center gap-3">
                                @if(branding('logo_path'))
                                    <img src="{{ asset('storage/' . branding('logo_path')) }}" alt="logo"
                                        class="avatar avatar-md rounded border" style="object-fit: contain; background: #fff;">
                                @else
                                    <span class="avatar avatar-md">—</span>
                                @endif
                                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="form-control">
                            </div>
                            @if(branding('logo_path'))
                                <label class="form-check mt-2">
                                    <input type="checkbox" name="remove_logo" value="1" class="form-check-input">
                                    <span class="form-check-label text-danger">{{ __('Remove logo') }}</span>
                                </label>
                            @endif
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Favicon')" name="favicon" :help="__('PNG/ICO, max 512KB. 32×32 ideal.')">
                            <div class="d-flex align-items-center gap-3">
                                @if(branding('favicon_path'))
                                    <img src="{{ asset('storage/' . branding('favicon_path')) }}" alt="favicon"
                                        class="avatar avatar-sm rounded border" style="object-fit: contain; background: #fff;">
                                @else
                                    <span class="avatar avatar-sm">—</span>
                                @endif
                                <input type="file" id="favicon" name="favicon" accept="image/png,image/x-icon" class="form-control">
                            </div>
                            @if(branding('favicon_path'))
                                <label class="form-check mt-2">
                                    <input type="checkbox" name="remove_favicon" value="1" class="form-check-input">
                                    <span class="form-check-label text-danger">{{ __('Remove favicon') }}</span>
                                </label>
                            @endif
                        </x-tabler.field>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Theme Preset') }}</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <template x-for="(t, slug) in themes" :key="slug">
                        <div class="col-6 col-sm-3">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="theme_preset" :value="slug" x-model="form.theme_preset" @change="applyTheme(slug)" class="form-selectgroup-input">
                                <span class="form-selectgroup-label">
                                    <span class="d-flex gap-1 mb-2">
                                        <span class="avatar avatar-xs rounded-circle" :style="`background:${t.primary || form.primary_color}`"></span>
                                        <span class="avatar avatar-xs rounded-circle" :style="`background:${t.accent || form.accent_color}`"></span>
                                        <span class="avatar avatar-xs rounded-circle" :style="`background:${t.sky || form.sky_color}`"></span>
                                    </span>
                                    <span class="d-block small fw-semibold" x-text="t.label"></span>
                                </span>
                            </label>
                        </div>
                    </template>
                </div>

                <div class="row g-2 mt-2">
                    <div class="col-sm-4">
                        <x-tabler.field :label="__('Primary')" name="primary_color">
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" x-model="form.primary_color" class="form-control form-control-color">
                                <input type="text" id="primary_color" name="primary_color" x-model="form.primary_color"
                                    class="form-control @error('primary_color') is-invalid @enderror">
                            </div>
                        </x-tabler.field>
                    </div>
                    <div class="col-sm-4">
                        <x-tabler.field :label="__('Accent')" name="accent_color">
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" x-model="form.accent_color" class="form-control form-control-color">
                                <input type="text" id="accent_color" name="accent_color" x-model="form.accent_color"
                                    class="form-control @error('accent_color') is-invalid @enderror">
                            </div>
                        </x-tabler.field>
                    </div>
                    <div class="col-sm-4">
                        <x-tabler.field :label="__('Sky / Tertiary')" name="sky_color">
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" x-model="form.sky_color" class="form-control form-control-color">
                                <input type="text" id="sky_color" name="sky_color" x-model="form.sky_color"
                                    class="form-control @error('sky_color') is-invalid @enderror">
                            </div>
                        </x-tabler.field>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Typography') }}</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Display Font (headings)')" name="font_display">
                            <select id="font_display" name="font_display" x-model="form.font_display" class="form-select @error('font_display') is-invalid @enderror">
                                @foreach($fonts as $name => $label)
                                    <option value="{{ $name }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Body Font (main text)')" name="font_body">
                            <select id="font_body" name="font_body" x-model="form.font_body" class="form-select @error('font_body') is-invalid @enderror">
                                @foreach($fonts as $name => $label)
                                    <option value="{{ $name }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                </div>
                <p class="text-muted small mb-0">{{ __('Fonts are loaded automatically from fonts.bunny.net (Google Fonts mirror).') }}</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Footer & Support') }}</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12">
                        <x-tabler.field :label="__('Footer Text')" name="footer_text">
                            <input type="text" id="footer_text" name="footer_text" x-model="form.footer_text" maxlength="255"
                                class="form-control @error('footer_text') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-4">
                        <x-tabler.field :label="__('Support Email')" name="support_email">
                            <input type="email" id="support_email" name="support_email" x-model="form.support_email"
                                class="form-control @error('support_email') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-8">
                        <x-tabler.field :label="__('Support URL')" name="support_url">
                            <input type="url" id="support_url" name="support_url" x-model="form.support_url" placeholder="https://help.example.com"
                                class="form-control @error('support_url') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('Live Preview') }}</h3>
            </div>
            <div class="card-body">
                <div class="rounded p-4 border"
                    :style="`background: linear-gradient(135deg, ${form.primary_color}10, ${form.accent_color}10, ${form.sky_color}10)`">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="avatar avatar-md text-white fw-bold"
                            :style="`background: linear-gradient(135deg, ${form.accent_color}, ${form.primary_color}, ${form.sky_color})`"
                            x-text="form.app_name?.[0] ?? 'X'"></span>
                        <div>
                            <p class="fw-bold fs-4 mb-0" :style="`color: ${form.primary_color}`" x-text="form.app_name || 'App Name'"></p>
                            <p class="text-muted small mb-0" x-text="form.tagline || 'Tagline'"></p>
                        </div>
                    </div>
                    <button type="button" class="btn text-white"
                        :style="`background: linear-gradient(135deg, ${form.accent_color}, ${form.primary_color}, ${form.sky_color})`">
                        {{ __('Sample Button') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <x-ti name="check" /> {{ __('Save Branding') }}
            </button>
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
</x-tabler.layout>

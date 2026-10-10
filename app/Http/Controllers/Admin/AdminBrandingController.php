<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminBrandingController extends Controller
{
    /** Available theme presets — color palettes admin bisa pilih. */
    public const THEMES = [
        'unicorn' => [
            'label'   => '🦄 Unicorn (default)',
            'primary' => '#A78BFA',
            'accent'  => '#FF7AC6',
            'sky'     => '#7DD3FC',
        ],
        'ocean' => [
            'label'   => '🌊 Ocean',
            'primary' => '#0EA5E9',
            'accent'  => '#06B6D4',
            'sky'     => '#22D3EE',
        ],
        'sunset' => [
            'label'   => '🌅 Sunset',
            'primary' => '#F97316',
            'accent'  => '#EF4444',
            'sky'     => '#FBBF24',
        ],
        'forest' => [
            'label'   => '🌲 Forest',
            'primary' => '#10B981',
            'accent'  => '#059669',
            'sky'     => '#84CC16',
        ],
        'midnight' => [
            'label'   => '🌙 Midnight',
            'primary' => '#6366F1',
            'accent'  => '#8B5CF6',
            'sky'     => '#A855F7',
        ],
        'rose' => [
            'label'   => '🌹 Rose',
            'primary' => '#EC4899',
            'accent'  => '#F43F5E',
            'sky'     => '#F472B6',
        ],
        'minimal' => [
            'label'   => '⚪ Minimal',
            'primary' => '#1F2937',
            'accent'  => '#4B5563',
            'sky'     => '#9CA3AF',
        ],
        'custom' => [
            'label'   => '🎨 Custom (atur sendiri)',
            'primary' => null,
            'accent'  => null,
            'sky'     => null,
        ],
    ];

    public function edit(): View
    {
        $branding = [
            'app_name'      => branding('app_name'),
            'tagline'       => branding('tagline'),
            'logo_path'     => branding('logo_path'),
            'favicon_path'  => branding('favicon_path'),
            'primary_color' => branding('primary_color'),
            'accent_color'  => branding('accent_color'),
            'sky_color'     => branding('sky_color'),
            'font_display'  => branding('font_display'),
            'font_body'     => branding('font_body'),
            'theme_preset'  => branding('theme_preset'),
            'footer_text'   => branding('footer_text'),
            'support_email' => branding('support_email'),
            'support_url'   => branding('support_url'),
        ];

        return view('admin.branding.edit', [
            'branding' => $branding,
            'themes'   => self::THEMES,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name'      => 'required|string|max:100',
            'tagline'       => 'nullable|string|max:255',
            'theme_preset'  => 'required|string|in:' . implode(',', array_keys(self::THEMES)),
            'primary_color' => 'required|string|max:7',
            'accent_color'  => 'required|string|max:7',
            'sky_color'     => 'required|string|max:7',
            'footer_text'   => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'support_url'   => 'nullable|url|max:255',
            'logo'          => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'favicon'       => 'nullable|image|mimes:png,ico|max:512',
            'remove_logo'    => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $oldPath = branding('logo_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('logo')->store('branding', 'public');
            $this->set('branding.logo_path', $path);
        } elseif (! empty($validated['remove_logo'])) {
            $oldPath = branding('logo_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $this->set('branding.logo_path', null);
        }

        if ($request->hasFile('favicon')) {
            $oldPath = branding('favicon_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('favicon')->store('branding', 'public');
            $this->set('branding.favicon_path', $path);
        } elseif (! empty($validated['remove_favicon'])) {
            $oldPath = branding('favicon_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $this->set('branding.favicon_path', null);
        }

        // Save text/color settings (font_* keys are legacy: kept in storage,
        // no longer edited since the UI uses the offline system stack).
        foreach (['app_name', 'tagline', 'theme_preset', 'primary_color', 'accent_color',
                 'sky_color', 'footer_text',
                 'support_email', 'support_url'] as $field) {
            $this->set('branding.' . $field, $validated[$field] ?? null);
        }

        return back()->with('success', __('Branding disimpan. Refresh halaman untuk lihat perubahan.'));
    }

    protected function set(string $key, mixed $value): void
    {
        AppSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => 'branding']
        );
    }
}

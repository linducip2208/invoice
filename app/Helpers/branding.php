<?php

use App\Models\AppSetting;
use Illuminate\Support\Arr;

if (! function_exists('app_setting')) {
    function app_setting(string $key, mixed $default = null): mixed
    {
        $all = AppSetting::all_cached();
        return Arr::get($all, $key, $default);
    }
}

if (! function_exists('branding')) {
    /**
     * Whitelabel branding helper. Returns one branding field with safe defaults.
     */
    function branding(string $field, mixed $default = null): mixed
    {
        static $defaults = [
            'app_name'      => 'Invocely',
            'tagline'       => 'Simple invoicing for small businesses',
            'logo_path'     => null,
            'favicon_path'  => null,
            'primary_color' => '#6d4ae0',
            'accent_color'  => '#F472B6',
            'sky_color'     => '#38BDF8',
            'font_display'  => 'Comfortaa',
            'font_body'     => 'Quicksand',
            'theme_preset'  => 'unicorn',
            'footer_text'   => 'Simple invoicing for small businesses',
            'support_email' => 'support@invocely.test',
            'support_url'   => null,
        ];

        $value = app_setting('branding.' . $field);

        if ($value !== null && $value !== '') {
            return $value;
        }

        return $default ?? $defaults[$field] ?? null;
    }
}

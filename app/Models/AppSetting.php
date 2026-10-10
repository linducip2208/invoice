<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('app_settings_all'));
        static::deleted(fn() => Cache::forget('app_settings_all'));
    }

    public static function all_cached(): array
    {
        return Cache::remember('app_settings_all', 3600, function () {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }
}

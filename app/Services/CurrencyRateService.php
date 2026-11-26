<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Konversi mata uang memakai kurs harian (Frankfurter, fallback open.er-api).
 * Hasil di-cache 12 jam; bila offline → null (view menyembunyikan konversi,
 * angka asli tidak pernah diubah).
 */
class CurrencyRateService
{
    /** @var array<string, float|null> */
    protected static array $memo = [];

    public static function flushMemo(): void
    {
        static::$memo = [];
    }

    /**
     * Prioritas: 1) mode manual + kurs manual di Setting,
     * 2) override .env CURRENCY_RATE_USD_IDR, 3) API + cache 12 jam,
     * 4) kurs manual sebagai fallback bila API mati.
     */
    public static function mode(): string
    {
        return static::setting('fx.mode', 'auto') === 'manual' ? 'manual' : 'auto';
    }

    public static function manualRate(string $from, string $to): ?float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);
        $v = static::setting("fx.manual_{$from}_{$to}");
        if (is_numeric($v) && (float) $v > 0) return (float) $v;
        $env = env("CURRENCY_RATE_{$from}_{$to}");
        if (is_numeric($env) && (float) $env > 0) return (float) $env;
        // Pasangan terbalik dari kurs manual (mis. IDR→USD dari USD_IDR).
        $v2 = static::setting("fx.manual_{$to}_{$from}");
        if (is_numeric($v2) && (float) $v2 > 0) return 1 / (float) $v2;
        $env2 = env("CURRENCY_RATE_{$to}_{$from}");
        if (is_numeric($env2) && (float) $env2 > 0) return 1 / (float) $env2;
        return null;
    }

    public static function rate(string $from, string $to): ?float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);
        if ($from === $to) return 1.0;

        $key = "fx:{$from}:{$to}";
        if (array_key_exists($key, static::$memo)) {
            return static::$memo[$key];
        }

        // Mode manual: pakai kurs dari Setting.
        if (static::mode() === 'manual') {
            return static::$memo[$key] = static::manualRate($from, $to);
        }

        $cached = Cache::get($key);
        if (is_array($cached) && isset($cached['rate'])) {
            static::$memo[$key] = (float) $cached['rate'];
            return static::$memo[$key];
        }

        $rate = static::fetchFrankfurter($from, $to) ?? static::fetchOpenErApi($from, $to);
        if ($rate !== null && $rate > 0) {
            Cache::put($key, ['rate' => $rate, 'date' => now()->toDateString()], now()->addHours(12));
            static::$memo[$key] = $rate;
            return $rate;
        }

        // API mati: fallback ke kurs manual bila ada.
        return static::$memo[$key] = static::manualRate($from, $to);
    }

    public static function convert(float $amount, string $from, string $to = 'IDR'): ?float
    {
        $rate = static::rate($from, $to);
        if ($rate === null) return null;
        return round($amount * $rate, 2);
    }

    /** Tanggal kurs yang dipakai (untuk label "kurs tgl ..."). */
    public static function rateDate(string $from, string $to): ?string
    {
        $cached = Cache::get('fx:' . strtoupper($from) . ':' . strtoupper($to));
        return $cached['date'] ?? null;
    }

    /** Sumber kurs yang dipakai: manual / live / cache / none. */
    public static function source(string $from, string $to): string
    {
        if (static::mode() === 'manual' && static::manualRate($from, $to)) return 'manual';
        $cached = Cache::get('fx:' . strtoupper($from) . ':' . strtoupper($to));
        return $cached ? 'live' : 'none';
    }

    protected static function setting(string $key, mixed $default = null): mixed
    {
        try {
            $all = \App\Models\AppSetting::all_cached();
            return $all[$key] ?? $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    protected static function fetchFrankfurter(string $from, string $to): ?float
    {
        try {
            $res = Http::timeout(8)->get('https://api.frankfurter.app/latest', [
                'from' => $from, 'to' => $to,
            ]);
            if ($res->successful()) {
                $rate = (float) data_get($res->json(), "rates.{$to}", 0);
                return $rate > 0 ? $rate : null;
            }
        } catch (\Throwable $e) {
            Log::warning('Kurs Frankfurter gagal', ['err' => $e->getMessage()]);
        }
        return null;
    }

    protected static function fetchOpenErApi(string $from, string $to): ?float
    {
        try {
            $res = Http::timeout(8)->get("https://open.er-api.com/v6/latest/{$from}");
            if ($res->successful()) {
                $rate = (float) data_get($res->json(), "rates.{$to}", 0);
                return $rate > 0 ? $rate : null;
            }
        } catch (\Throwable $e) {
            Log::warning('Kurs open.er-api gagal', ['err' => $e->getMessage()]);
        }
        return null;
    }
}

<?php

if (! function_exists('format_money')) {
    /**
     * Consistent money formatting: symbol/code prefix + thousands grouping.
     * Display-only; all math stays in decimal on the backend.
     */
    function format_money(float|int|string|null $amount, ?string $currency = null): string
    {
        $currency ??= workspace()?->currency ?? 'IDR';
        $symbols = ['IDR' => 'Rp', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'SGD' => 'S$', 'AUD' => 'A$', 'JPY' => '¥', 'CNY' => '¥'];
        $prefix = $symbols[$currency] ?? ($currency . ' ');
        $decimals = $currency === 'IDR' ? 0 : 2;
        return $prefix . ' ' . number_format((float) $amount, $decimals);
    }
}

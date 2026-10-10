<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'tagline',
        'description',
        'price_idr_monthly',
        'price_idr_yearly',
        'price_usd_monthly',
        'price_usd_yearly',
        'features',
        'trial_days',
        'is_public',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features'           => 'array',
            'is_public'          => 'boolean',
            'is_default'         => 'boolean',
            'price_idr_monthly'  => 'decimal:2',
            'price_idr_yearly'   => 'decimal:2',
            'price_usd_monthly'  => 'decimal:2',
            'price_usd_yearly'   => 'decimal:2',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function isFree(): bool
    {
        return $this->slug === 'free';
    }

    public function feature(string $key, mixed $default = null): mixed
    {
        return $this->features[$key] ?? $default;
    }

    public function priceFor(string $currency, string $period): float
    {
        $key = 'price_' . strtolower($currency) . '_' . $period;

        return (float) ($this->{$key} ?? 0);
    }

    public function yearlySavingsPercent(string $currency): int
    {
        $monthly = $this->priceFor($currency, 'monthly');
        $yearly  = $this->priceFor($currency, 'yearly');

        if ($monthly <= 0) {
            return 0;
        }

        $expected = $monthly * 12;
        if ($expected <= 0) {
            return 0;
        }

        return (int) round((1 - ($yearly / $expected)) * 100);
    }
}

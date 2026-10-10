<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'description', 'discount_type', 'discount_value', 'currency', 'plan_id', 'max_uses', 'uses_count', 'expires_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'expires_at'     => 'datetime',
            'is_active'      => 'boolean',
            'discount_value' => 'decimal:2',
        ];
    }

    public function plan() { return $this->belongsTo(Plan::class); }

    public function isUsable(): bool
    {
        if (! $this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->uses_count >= $this->max_uses) return false;
        return true;
    }

    public function applyTo(float $amount): float
    {
        if ($this->discount_type === 'percent') {
            return max(0, $amount - ($amount * $this->discount_value / 100));
        }
        return max(0, $amount - $this->discount_value);
    }
}

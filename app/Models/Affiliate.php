<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    protected $fillable = ['user_id', 'code', 'clicks', 'signups', 'paid_conversions', 'commission_pct', 'balance_idr', 'balance_usd', 'payout_method', 'payout_details'];

    protected function casts(): array
    {
        return [
            'payout_details' => 'array',
            'commission_pct' => 'decimal:2',
            'balance_idr' => 'decimal:2',
            'balance_usd' => 'decimal:2',
        ];
    }

    public static function forUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            ['code' => Str::lower(Str::random(8))]
        );
    }

    public function user() { return $this->belongsTo(User::class); }

    public function getReferralLinkAttribute(): string
    {
        return url('/?ref=' . $this->code);
    }
}

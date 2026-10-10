<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    protected $fillable = [
        'coupon_id', 'workspace_id', 'subject_type', 'subject_id',
        'discount_applied', 'redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'redeemed_at'      => 'datetime',
            'discount_applied' => 'decimal:2',
        ];
    }

    public function coupon()    { return $this->belongsTo(Coupon::class); }
    public function workspace() { return $this->belongsTo(Workspace::class); }
    public function subject()   { return $this->morphTo(); }
}

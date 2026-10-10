<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'workspace_id',
        'external_invoice_id',
        'reference_no',
        'amount',
        'currency',
        'status',
        'paid_at',
        'period_starts_at',
        'period_ends_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'paid_at'          => 'datetime',
            'period_starts_at' => 'datetime',
            'period_ends_at'   => 'datetime',
            'raw_payload'      => 'array',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}

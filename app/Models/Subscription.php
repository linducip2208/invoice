<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'plan_id',
        'payment_gateway_id',
        'payment_method',
        'manual_proof_path',
        'manual_payment_note',
        'status',
        'billing_period',
        'currency',
        'external_subscription_id',
        'external_customer_id',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'cancelled_at',
        'cancel_at_period_end',
        'last_payment_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at'            => 'datetime',
            'current_period_starts_at' => 'datetime',
            'current_period_ends_at'   => 'datetime',
            'cancelled_at'             => 'datetime',
            'last_payment_at'          => 'datetime',
            'cancel_at_period_end'     => 'boolean',
            'metadata'                 => 'array',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function isActive(): bool
    {
        if (in_array($this->status, ['cancelled', 'expired'])) {
            return false;
        }

        if ($this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isFree(): bool
    {
        return $this->plan?->isFree() ?? true;
    }

    public function feature(string $key, mixed $default = null): mixed
    {
        return $this->plan?->feature($key, $default);
    }

    public function within(string $key, int $current): bool
    {
        $limit = $this->feature($key);

        if ($limit === null || $limit === -1 || $limit === 'unlimited') {
            return true;
        }

        return $current < (int) $limit;
    }
}

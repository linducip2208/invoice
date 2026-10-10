<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class QrisCode extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'provider_id', 'amount',
        'qr_payload', 'qr_image_path', 'expires_at', 'status',
        'paid_at', 'provider_ref',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'expires_at' => 'datetime',
            'paid_at'    => 'datetime',
        ];
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function provider() { return $this->belongsTo(PaymentGateway::class, 'provider_id'); }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}

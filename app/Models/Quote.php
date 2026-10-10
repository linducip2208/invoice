<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quote extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'client_id', 'quote_no', 'status', 'issue_date', 'expiry_date',
        'subtotal', 'tax_rate', 'tax_amount', 'discount_value', 'discount_type', 'discount_amount',
        'total', 'currency', 'items', 'notes', 'terms', 'converted_invoice_id', 'public_token', 'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date'  => 'date',
            'expiry_date' => 'date',
            'accepted_at' => 'datetime',
            'items'       => 'array',
            'subtotal'        => 'decimal:2',
            'tax_amount'      => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total'           => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $q) {
            if (! $q->public_token) {
                $q->public_token = Str::random(32);
            }
        });
    }

    public function client() { return $this->belongsTo(Client::class); }
    public function convertedInvoice() { return $this->belongsTo(Invoice::class, 'converted_invoice_id'); }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'accepted', 'converted' => 'success',
            'rejected', 'expired' => 'danger',
            'sent' => 'info',
            default => 'warning',
        };
    }
}

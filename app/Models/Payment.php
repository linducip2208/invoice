<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'invoice_id', 'workspace_id', 'payment_gateway_id', 'gateway_transaction_id',
        'amount', 'payment_date', 'payment_method', 'channel',
        'gateway_status', 'gateway_raw', 'reference_no', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'gateway_raw' => 'json',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class InvoiceLateFee extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'applied_at', 'amount', 'type',
        'percent_value', 'fixed_amount', 'description',
    ];

    protected function casts(): array
    {
        return [
            'applied_at'    => 'datetime',
            'amount'        => 'decimal:2',
            'percent_value' => 'decimal:2',
            'fixed_amount'  => 'decimal:2',
        ];
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }
}

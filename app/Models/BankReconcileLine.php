<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class BankReconcileLine extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'batch_id', 'workspace_id', 'trx_date', 'amount', 'description',
        'matched_invoice_id', 'matched_payment_id', 'match_confidence',
        'match_method', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'trx_date' => 'date',
            'amount'   => 'decimal:2',
        ];
    }

    public function batch()           { return $this->belongsTo(BankReconcileBatch::class, 'batch_id'); }
    public function matchedInvoice()  { return $this->belongsTo(Invoice::class, 'matched_invoice_id'); }
    public function matchedPayment()  { return $this->belongsTo(Payment::class, 'matched_payment_id'); }
}

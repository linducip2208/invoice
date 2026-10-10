<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'client_id', 'category', 'description', 'amount', 'currency', 'expense_date', 'receipt_path', 'billable', 'invoiced', 'invoice_id', 'notes'];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount'       => 'decimal:2',
            'billable'     => 'boolean',
            'invoiced'     => 'boolean',
        ];
    }

    public function client()  { return $this->belongsTo(Client::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
}

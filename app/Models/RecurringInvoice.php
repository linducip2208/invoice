<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class RecurringInvoice extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'client_id', 'template', 'primary_color', 'accent_color',
        'frequency', 'interval_value', 'next_run_date', 'end_date',
        'max_occurrences', 'current_occurrence', 'status',
        'last_invoice_id', 'invoice_data',
    ];

    protected function casts(): array
    {
        return [
            'next_run_date' => 'date',
            'end_date' => 'date',
            'max_occurrences' => 'integer',
            'current_occurrence' => 'integer',
            'interval_value' => 'integer',
            'invoice_data' => 'array',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lastInvoice()
    {
        return $this->belongsTo(Invoice::class, 'last_invoice_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'recurring_invoice_id');
    }
}

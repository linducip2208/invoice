<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'client_id', 'user_id', 'description', 'hours', 'hourly_rate', 'entry_date', 'billable', 'invoiced', 'invoice_id'];

    protected function casts(): array
    {
        return [
            'entry_date'  => 'date',
            'hours'       => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'billable'    => 'boolean',
            'invoiced'    => 'boolean',
        ];
    }

    public function client() { return $this->belongsTo(Client::class); }
    public function user()   { return $this->belongsTo(User::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }

    public function getAmountAttribute(): float
    {
        return (float) $this->hours * (float) $this->hourly_rate;
    }
}

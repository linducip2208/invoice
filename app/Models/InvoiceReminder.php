<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class InvoiceReminder extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'schedule_offset', 'scheduled_at',
        'sent_at', 'template_subject', 'template_body', 'status', 'fail_reason',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at'      => 'datetime',
        ];
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeDue($q)     { return $q->where('scheduled_at', '<=', now()); }
}

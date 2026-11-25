<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class ScheduledInvoiceSend extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'to_email', 'subject', 'message',
        'send_at', 'status', 'sent_at', 'fail_reason',
    ];

    protected function casts(): array
    {
        return ['send_at' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

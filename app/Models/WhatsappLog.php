<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'quote_id', 'to_phone',
        'template_name', 'message_body', 'status', 'provider_msg_id',
        'fail_reason', 'sent_at', 'delivered_at', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at'      => 'datetime',
            'delivered_at' => 'datetime',
            'read_at'      => 'datetime',
        ];
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function quote()   { return $this->belongsTo(Quote::class); }
}

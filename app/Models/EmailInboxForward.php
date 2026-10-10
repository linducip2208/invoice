<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class EmailInboxForward extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'inbound_address', 'forwarded_at', 'from_email',
        'subject', 'attachments_count', 'status', 'expense_id',
        'raw_email_path', 'ai_extracted_data', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'forwarded_at'      => 'datetime',
            'ai_extracted_data' => 'array',
        ];
    }

    public function expense() { return $this->belongsTo(Expense::class); }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class QuoteApprovalChallenge extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'quote_id', 'revision', 'contact_email',
        'portal_user_id', 'purpose', 'token_hash', 'otp_hash',
        'expires_at', 'attempts', 'max_attempts', 'consumed_at', 'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime', 'consumed_at' => 'datetime',
            'last_sent_at' => 'datetime', 'revision' => 'integer',
            'attempts' => 'integer', 'max_attempts' => 'integer',
        ];
    }

    public function quote() { return $this->belongsTo(Quote::class); }
    public function portalUser() { return $this->belongsTo(ClientPortalUser::class, 'portal_user_id'); }

    public function isLive(): bool
    {
        return $this->consumed_at === null && $this->expires_at->isFuture();
    }
}

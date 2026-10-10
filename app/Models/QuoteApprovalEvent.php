<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class QuoteApprovalEvent extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'quote_id', 'revision', 'client_id',
        'approver_portal_user_id', 'approver_name', 'approver_role',
        'approver_email', 'verification_method', 'authority_basis',
        'consent_text_version', 'consent_text_snapshot',
        'document_sha256', 'pdf_sha256', 'outcome', 'decline_reason',
        'decided_at', 'ip', 'user_agent', 'correlation_id',
    ];

    protected function casts(): array
    {
        return ['decided_at' => 'datetime', 'revision' => 'integer'];
    }

    public function quote() { return $this->belongsTo(Quote::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function approver() { return $this->belongsTo(ClientPortalUser::class, 'approver_portal_user_id'); }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'rule_id', 'subject_type', 'subject_id',
        'requested_by', 'status', 'approver_responses',
        'final_decision_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'approver_responses' => 'array',
            'final_decision_at'  => 'datetime',
        ];
    }

    public function rule()      { return $this->belongsTo(ApprovalRule::class, 'rule_id'); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function subject()   { return $this->morphTo(); }
}

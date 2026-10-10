<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class ApprovalRule extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'name', 'applies_to', 'condition_type',
        'condition_value', 'approver_user_ids', 'require_all', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'approver_user_ids' => 'array',
            'require_all'       => 'boolean',
            'is_active'         => 'boolean',
            'condition_value'   => 'decimal:2',
        ];
    }

    public function requests() { return $this->hasMany(ApprovalRequest::class, 'rule_id'); }
}

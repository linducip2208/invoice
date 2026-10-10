<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class BankReconcileBatch extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'bank_name', 'account_no', 'file_path', 'file_format',
        'imported_by', 'total_lines', 'matched_count', 'unmatched_count', 'status',
    ];

    public function lines() { return $this->hasMany(BankReconcileLine::class, 'batch_id'); }
    public function importer() { return $this->belongsTo(User::class, 'imported_by'); }
}

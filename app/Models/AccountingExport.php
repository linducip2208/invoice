<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class AccountingExport extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'format', 'period_start', 'period_end',
        'file_path', 'record_count', 'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end'   => 'date',
        ];
    }

    public function generator() { return $this->belongsTo(User::class, 'generated_by'); }
}

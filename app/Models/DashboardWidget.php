<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'user_id', 'widget_type', 'position',
        'size', 'config', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'config'     => 'array',
            'is_visible' => 'boolean',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
}

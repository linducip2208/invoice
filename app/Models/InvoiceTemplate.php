<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceTemplate extends Model
{
    protected $fillable = [
        'workspace_id', 'name', 'slug', 'is_default',
        'preview_image', 'config',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'config' => 'array',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('workspace_id');
    }

    public function scopeForWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }
}

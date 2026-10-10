<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToWorkspace
{
    public static function bootBelongsToWorkspace(): void
    {
        static::creating(function ($model) {
            if (! $model->workspace_id && ($workspace = workspace())) {
                $model->workspace_id = $workspace->id;
            }
        });

        static::addGlobalScope('workspace', function (Builder $builder) {
            if ($workspace = workspace()) {
                $builder->where($builder->getModel()->getTable() . '.workspace_id', $workspace->id);
            }
        });
    }

    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }
}

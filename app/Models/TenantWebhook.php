<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TenantWebhook extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'url', 'secret', 'events', 'is_active', 'last_fired_at', 'failure_count'];

    protected function casts(): array
    {
        return ['events' => 'array', 'is_active' => 'boolean', 'last_fired_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $w) {
            if (! $w->secret) $w->secret = Str::random(48);
        });
    }
}

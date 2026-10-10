<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkspaceInvite extends Model
{
    protected $fillable = ['workspace_id', 'invited_by', 'email', 'role', 'token', 'accepted_at', 'expires_at'];

    protected function casts(): array
    {
        return ['accepted_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $i) {
            if (! $i->token) $i->token = Str::random(48);
            if (! $i->expires_at) $i->expires_at = now()->addDays(7);
        });
    }

    public function workspace() { return $this->belongsTo(Workspace::class); }
    public function invitedBy() { return $this->belongsTo(User::class, 'invited_by'); }

    public function isExpired(): bool { return $this->expires_at && $this->expires_at->isPast(); }
    public function isAccepted(): bool { return $this->accepted_at !== null; }
}

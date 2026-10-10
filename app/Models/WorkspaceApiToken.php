<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkspaceApiToken extends Model
{
    protected $fillable = ['workspace_id', 'user_id', 'name', 'token_hash', 'prefix', 'abilities', 'last_used_at', 'expires_at'];

    protected function casts(): array
    {
        return ['abilities' => 'array', 'last_used_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public static function generate(int $workspaceId, int $userId, string $name, array $abilities = ['*']): array
    {
        $plain  = Str::random(40);
        $prefix = 'inv_' . substr($plain, 0, 6);
        $token  = static::create([
            'workspace_id' => $workspaceId,
            'user_id'      => $userId,
            'name'         => $name,
            'prefix'       => $prefix,
            'token_hash'   => hash('sha256', $plain),
            'abilities'    => $abilities,
        ]);

        return ['token' => $prefix . '_' . $plain, 'record' => $token];
    }

    public function workspace() { return $this->belongsTo(Workspace::class); }
    public function user() { return $this->belongsTo(User::class); }
}

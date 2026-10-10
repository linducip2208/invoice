<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomDomain extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'hostname', 'verification_token', 'verification_method',
        'status', 'ssl_issued_at', 'ssl_expires_at', 'verified_at', 'last_check_at',
    ];

    protected function casts(): array
    {
        return [
            'ssl_issued_at'  => 'datetime',
            'ssl_expires_at' => 'datetime',
            'verified_at'    => 'datetime',
            'last_check_at'  => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $d) {
            if (! $d->verification_token) {
                $d->verification_token = 'invocely-verify=' . Str::random(40);
            }
        });
    }
}

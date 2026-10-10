<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserTwoFactorSecret extends Model
{
    protected $table = 'user_2fa_secrets';

    protected $fillable = [
        'user_id', 'secret_encrypted', 'recovery_codes_encrypted',
        'enabled_at', 'last_used_at',
    ];

    protected $hidden = ['secret_encrypted', 'recovery_codes_encrypted'];

    protected function casts(): array
    {
        return [
            'recovery_codes_encrypted' => 'array',
            'enabled_at'               => 'datetime',
            'last_used_at'             => 'datetime',
        ];
    }

    protected function secret(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->secret_encrypted ? Crypt::decryptString($this->secret_encrypted) : null,
            set: fn ($v) => ['secret_encrypted' => $v ? Crypt::encryptString($v) : null],
        );
    }

    public function user() { return $this->belongsTo(User::class); }
}

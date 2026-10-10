<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ExternalOauthIdentity extends Model
{
    protected $fillable = [
        'user_id', 'provider', 'provider_user_id', 'email', 'name',
        'avatar_url', 'token_encrypted', 'linked_at',
    ];

    protected function casts(): array
    {
        return ['linked_at' => 'datetime'];
    }

    protected function token(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->token_encrypted ? Crypt::decryptString($this->token_encrypted) : null,
            set: fn ($v) => ['token_encrypted' => $v ? Crypt::encryptString($v) : null],
        );
    }

    public function user() { return $this->belongsTo(User::class); }
}

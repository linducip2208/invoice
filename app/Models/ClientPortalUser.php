<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ClientPortalUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'client_portal_users';

    protected $fillable = [
        'client_id', 'workspace_id', 'email', 'password_hash',
        'last_login_at', 'remember_token', 'email_verified_at',
        'can_approve_quotes', 'is_active',
    ];

    protected $hidden = ['password_hash', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'can_approve_quotes' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function client() { return $this->belongsTo(Client::class); }
    public function workspace() { return $this->belongsTo(Workspace::class); }
}

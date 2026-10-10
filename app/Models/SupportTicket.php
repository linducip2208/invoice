<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = ['workspace_id', 'user_id', 'subject', 'priority', 'status', 'resolved_at'];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function workspace() { return $this->belongsTo(Workspace::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function messages() { return $this->hasMany(SupportMessage::class, 'ticket_id')->orderBy('created_at'); }
}

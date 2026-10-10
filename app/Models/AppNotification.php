<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'user_id', 'type', 'title', 'message', 'action_url', 'icon', 'read_at'];
    protected function casts(): array { return ['read_at' => 'datetime']; }

    public function user() { return $this->belongsTo(User::class); }

    public function scopeUnread($q) { return $q->whereNull('read_at'); }

    public static function notify(string $type, string $title, string $message, ?string $actionUrl = null, string $icon = '🔔'): self
    {
        return static::create([
            'user_id'    => auth()->id(),
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'action_url' => $actionUrl,
            'icon'       => $icon,
        ]);
    }
}

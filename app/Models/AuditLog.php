<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'user_id', 'action', 'subject_type', 'subject_id', 'description', 'changes', 'ip'];
    protected function casts(): array { return ['changes' => 'array']; }

    public function user() { return $this->belongsTo(User::class); }

    public static function record(string $action, string $description, ?Model $subject = null, array $changes = []): void
    {
        if (! workspace()) return;
        static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'description'  => $description,
            'changes'      => $changes,
            'ip'           => request()->ip(),
        ]);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NpsResponse extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'client_id', 'score', 'comment',
        'submitted_at', 'ip', 'ua', 'public_token',
    ];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $r) {
            if (! $r->public_token) $r->public_token = Str::random(32);
        });
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function client()  { return $this->belongsTo(Client::class); }

    public function getCategoryAttribute(): string
    {
        if ($this->score >= 9) return 'promoter';
        if ($this->score >= 7) return 'passive';
        return 'detractor';
    }
}

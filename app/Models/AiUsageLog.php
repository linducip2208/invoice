<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'provider_id', 'user_id', 'feature', 'model',
        'input_tokens', 'output_tokens', 'cost_estimate', 'duration_ms',
        'status', 'error_message',
    ];

    protected function casts(): array
    {
        return ['cost_estimate' => 'decimal:4'];
    }

    public function provider() { return $this->belongsTo(AiProvider::class, 'provider_id'); }
    public function user()     { return $this->belongsTo(User::class); }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class ReportSubscription extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'user_id', 'report_type', 'frequency',
        'day_of_week', 'day_of_month', 'hour', 'last_sent_at',
        'next_send_at', 'is_active', 'config',
    ];

    protected function casts(): array
    {
        return [
            'last_sent_at' => 'datetime',
            'next_send_at' => 'datetime',
            'is_active'    => 'boolean',
            'config'       => 'array',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }

    public function computeNextSend(): \Carbon\Carbon
    {
        $base = $this->last_sent_at ? $this->last_sent_at->copy() : now();
        return match ($this->frequency) {
            'weekly'    => $base->copy()->addDays(((int) ($this->day_of_week ?? 1) - $base->dayOfWeekIso + 7) % 7 ?: 7)->setTime($this->hour, 0),
            'monthly'   => $base->copy()->addMonth()->day(min($this->day_of_month ?? 1, 28))->setTime($this->hour, 0),
            'quarterly' => $base->copy()->addQuarter()->setTime($this->hour, 0),
            default     => $base->copy()->addWeek(),
        };
    }
}

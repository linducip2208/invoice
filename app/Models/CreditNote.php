<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'invoice_id', 'credit_no', 'reason', 'amount',
        'currency', 'issued_at', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'amount'    => 'decimal:2',
        ];
    }

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'applied' => 'success',
            'cancelled' => 'danger',
            'issued' => 'info',
            default => 'warning',
        };
    }

    public static function nextNumber(int $workspaceId): string
    {
        $year = now()->format('Y');
        $count = static::withoutGlobalScope('workspace')
            ->where('workspace_id', $workspaceId)
            ->whereYear('created_at', $year)
            ->count() + 1;
        return sprintf('CN-%s-%03d', $year, $count);
    }
}

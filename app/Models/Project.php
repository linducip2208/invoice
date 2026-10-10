<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'client_id', 'name', 'slug', 'description',
        'status', 'budget_amount', 'budget_hours', 'hourly_rate',
        'start_date', 'end_date', 'color',
    ];

    protected function casts(): array
    {
        return [
            'start_date'    => 'date',
            'end_date'      => 'date',
            'budget_amount' => 'decimal:2',
            'budget_hours'  => 'decimal:2',
            'hourly_rate'   => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $p) {
            if (! $p->slug) {
                $p->slug = Str::slug($p->name) . '-' . Str::random(4);
            }
        });
    }

    public function client()      { return $this->belongsTo(Client::class); }
    public function invoices()    { return $this->hasMany(Invoice::class); }
    public function timeEntries() { return $this->hasMany(TimeEntry::class); }
    public function expenses()    { return $this->hasMany(Expense::class); }
    public function quotes()      { return $this->hasMany(Quote::class); }
}

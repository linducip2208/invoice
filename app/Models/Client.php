<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'name', 'company_name', 'email', 'phone',
        'address', 'city', 'state', 'country', 'postal_code',
        'tax_id', 'notes', 'view_token', 'is_active', 'no_reminders',
    ];

    protected static function booted(): void
    {
        static::creating(function (Client $client) {
            if (! $client->view_token) {
                $client->view_token = (string) Str::uuid();
            }
        });
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function recurringInvoices()
    {
        return $this->hasMany(RecurringInvoice::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'logo_path', 'address', 'city', 'state',
        'country', 'postal_code', 'phone', 'email', 'tax_id',
        'invoice_prefix', 'next_invoice_no', 'default_template',
        'primary_color', 'accent_color', 'currency',
        'is_pkp', 'pkp_no', 'quote_prefix', 'next_quote_no',
        'invoice_number_format', 'reset_period',
        'default_late_fee_value', 'default_late_fee_type', 'default_late_fee_policy',
        'enable_client_comments', 'whatsapp_config', 'industry',
        'ai_monthly_budget_idr',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_config'        => 'array',
            'widget_allowed_domains' => 'array',
            'is_pkp'                 => 'boolean',
            'enable_client_comments' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Workspace $workspace) {
            if (! $workspace->slug) {
                $workspace->slug = Str::slug($workspace->name) . '-' . Str::random(6);
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function recurringInvoices()
    {
        return $this->hasMany(RecurringInvoice::class);
    }

    public function templates()
    {
        return $this->hasMany(InvoiceTemplate::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function activeSubscription(): Subscription
    {
        $sub = $this->subscription()->with('plan')->first();

        if ($sub) {
            return $sub;
        }

        $freePlan = Plan::where('slug', 'free')->first();
        abort_unless($freePlan, 500, 'Free plan not seeded.');

        return $this->subscription()->create([
            'plan_id'        => $freePlan->id,
            'status'         => 'active',
            'billing_period' => 'free',
            'currency'       => $this->currency ?? 'IDR',
        ])->load('plan');
    }

    public function planFeature(string $key, mixed $default = null): mixed
    {
        return $this->activeSubscription()->feature($key, $default);
    }

    public function isOnFreeTier(): bool
    {
        return $this->activeSubscription()->isFree();
    }
}

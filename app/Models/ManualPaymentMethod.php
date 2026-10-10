<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPaymentMethod extends Model
{
    protected $fillable = ['label', 'type', 'account_name', 'account_number', 'bank_or_provider', 'instructions', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
}

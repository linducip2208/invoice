<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NpwpValidation extends Model
{
    protected $fillable = [
        'npwp_number', 'canonical_npwp', 'is_valid', 'name_djp',
        'status_djp', 'last_checked_at', 'provider_response',
    ];

    protected function casts(): array
    {
        return [
            'is_valid'         => 'boolean',
            'last_checked_at'  => 'datetime',
            'provider_response' => 'array',
        ];
    }
}

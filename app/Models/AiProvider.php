<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AiProvider extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'name', 'api_format', 'base_url',
        'api_key_encrypted', 'extra_headers', 'default_model',
        'default_pricing_input', 'default_pricing_output', 'is_active',
    ];

    protected $hidden = ['api_key_encrypted'];

    protected function casts(): array
    {
        return [
            'extra_headers'          => 'array',
            'is_active'              => 'boolean',
            'default_pricing_input'  => 'decimal:6',
            'default_pricing_output' => 'decimal:6',
        ];
    }

    protected function apiKey(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->api_key_encrypted ? Crypt::decryptString($this->api_key_encrypted) : null,
            set: fn ($value) => ['api_key_encrypted' => $value ? Crypt::encryptString($value) : null],
        );
    }

    public function usageLogs() { return $this->hasMany(AiUsageLog::class, 'provider_id'); }
}

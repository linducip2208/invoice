<?php

namespace App\Models;

use App\Models\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'workspace_id', 'name', 'gateway', 'api_format',
        'base_url', 'api_key_encrypted', 'api_secret_encrypted',
        'merchant_id_encrypted', 'client_key_encrypted', 'server_key_encrypted',
        'extra_headers', 'supported_channels',
        'is_active', 'is_sandbox', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'extra_headers' => 'json',
            'supported_channels' => 'json',
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getChannelLabel(string $channel): string
    {
        return match ($channel) {
            'cash'              => '💵 Tunai',
            'bank_transfer'     => '🏦 Transfer Bank',
            'bca'               => '🏦 BCA Virtual Account',
            'bni'               => '🏦 BNI Virtual Account',
            'bri'               => '🏦 BRI Virtual Account',
            'mandiri'           => '🏦 Mandiri Virtual Account',
            'permata'           => '🏦 Permata Virtual Account',
            'cimb'              => '🏦 CIMB Niaga Virtual Account',
            'credit_card'       => '💳 Kartu Kredit',
            'gopay'             => '🔵 GoPay',
            'ovo'               => '🟣 OVO',
            'dana'              => '🔷 DANA',
            'shopeepay'         => '🟠 ShopeePay',
            'linkaja'           => '🔴 LinkAja',
            'qris'              => '📱 QRIS',
            'convenience_store' => '🏪 Alfamart / Indomaret',
            'va'                => '🏦 Virtual Account',
            'other'             => '✦ Lainnya',
            default             => ucwords(str_replace('_', ' ', $channel)),
        };
    }

    public function getNonEncryptedKey(string $key): ?string
    {
        $encrypted = $this->getAttribute($key . '_encrypted');
        if (! $encrypted) return null;
        try {
            return decrypt($encrypted);
        } catch (\Exception) {
            return null;
        }
    }

    public function setNonEncryptedKey(string $key, ?string $value): void
    {
        if ($value === null) {
            $this->setAttribute($key . '_encrypted', null);
            return;
        }
        $this->setAttribute($key . '_encrypted', encrypt($value));
    }

    /** Convenience accessor used by the recurring billing adapter. */
    public function getApiKeyDecryptedAttribute(): ?string
    {
        return $this->getNonEncryptedKey('api_key');
    }

    public function getApiSecretDecryptedAttribute(): ?string
    {
        return $this->getNonEncryptedKey('api_secret');
    }
}

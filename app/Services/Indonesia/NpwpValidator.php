<?php

namespace App\Services\Indonesia;

use App\Models\NpwpValidation;
use Illuminate\Support\Facades\Http;

class NpwpValidator
{
    /**
     * Validate NPWP. Cache result 30 days.
     * Returns: ['valid'=>bool, 'canonical'=>string, 'name'=>?string, 'status'=>?string]
     */
    public function validate(string $npwp): array
    {
        $canonical = $this->normalize($npwp);

        if (! $this->checksumValid($canonical)) {
            return ['valid' => false, 'canonical' => $canonical, 'name' => null, 'status' => 'invalid_format'];
        }

        // Check cache (30 day TTL)
        $cached = NpwpValidation::where('canonical_npwp', $canonical)
            ->where('last_checked_at', '>=', now()->subDays(30))
            ->first();

        if ($cached) {
            return [
                'valid'     => $cached->is_valid,
                'canonical' => $cached->canonical_npwp,
                'name'      => $cached->name_djp,
                'status'    => $cached->status_djp,
            ];
        }

        // Try DJP API if configured
        $djpUrl = config('services.djp_npwp_url');
        $djpKey = config('services.djp_npwp_key');

        if ($djpUrl && $djpKey) {
            try {
                $resp = Http::timeout(10)
                    ->withToken($djpKey)
                    ->post($djpUrl, ['npwp' => $canonical]);

                if ($resp->successful()) {
                    $body = $resp->json();
                    NpwpValidation::updateOrCreate(
                        ['canonical_npwp' => $canonical],
                        [
                            'npwp_number'      => $npwp,
                            'is_valid'         => $body['valid'] ?? true,
                            'name_djp'         => $body['name'] ?? null,
                            'status_djp'       => $body['status'] ?? 'active',
                            'last_checked_at'  => now(),
                            'provider_response' => $body,
                        ]
                    );
                    return [
                        'valid' => $body['valid'] ?? true,
                        'canonical' => $canonical,
                        'name' => $body['name'] ?? null,
                        'status' => $body['status'] ?? 'active',
                    ];
                }
            } catch (\Throwable $e) {
                // Fall through to checksum-only result
            }
        }

        // Offline fallback: checksum valid → mark valid (name unknown)
        NpwpValidation::updateOrCreate(
            ['canonical_npwp' => $canonical],
            [
                'npwp_number'     => $npwp,
                'is_valid'        => true,
                'name_djp'        => null,
                'status_djp'      => 'unverified_checksum_only',
                'last_checked_at' => now(),
                'provider_response' => null,
            ]
        );

        return ['valid' => true, 'canonical' => $canonical, 'name' => null, 'status' => 'unverified_checksum_only'];
    }

    /**
     * Normalize NPWP to 16-digit canonical (Coretax).
     * Old format 15-digit padded to 16 with leading 0.
     */
    public function normalize(string $npwp): string
    {
        $digits = preg_replace('/\D+/', '', $npwp);
        if (strlen($digits) === 15) $digits = '0' . $digits;
        return $digits;
    }

    /**
     * Validate NPWP checksum (Modulus 11).
     */
    private function checksumValid(string $canonical16): bool
    {
        if (strlen($canonical16) !== 16) return false;

        // First 9 digits + checksum logic (NPWP checksum)
        // Modulus 11 with weights — DJP standard
        $weights = [3, 2, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += (int) $canonical16[$i] * $weights[$i];
        }
        $expected = (11 - ($sum % 11)) % 10;

        return $expected === (int) $canonical16[8];
    }
}

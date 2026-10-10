<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;

/**
 * Verifies payment/billing webhook authenticity per provider scheme.
 *
 * Design:
 * - When the workspace configured a verification secret for the provider,
 *   the signature is REQUIRED: invalid/missing signature => verified=false.
 * - When no secret is configured (mode=unconfigured), callers must fall back
 *   to hardening guards (transactions, replay checks, stored-amount-wins)
 *   and log loudly. Live signature coverage stays BLOCKED until secrets exist.
 * - NEVER log secrets, keys, tokens, or full payloads here.
 */
class WebhookSignatureVerifier
{
    /**
     * @return array{verified: bool, mode: string, reason: string|null}
     *         mode: 'signature' | 'unconfigured' | 'unsupported'
     */
    public function verify(string $gateway, Request $request, ?PaymentGateway $config): array
    {
        return match ($gateway) {
            'midtrans' => $this->verifyMidtrans($request, $config?->getNonEncryptedKey('server_key')),
            'xendit'   => $this->verifyXendit($request, $config?->getNonEncryptedKey('api_key')),
            'tripay'   => $this->verifyTripay($request, $config?->getNonEncryptedKey('api_secret'), $config?->getNonEncryptedKey('merchant_id')),
            'duitku'   => $this->verifyDuitku($request, $config?->getNonEncryptedKey('api_key'), $config?->getNonEncryptedKey('merchant_id')),
            'doku'     => $this->verifyDoku($request, $config?->getNonEncryptedKey('api_secret'), $config?->getNonEncryptedKey('api_key')),
            default    => ['verified' => false, 'mode' => 'unsupported', 'reason' => 'no verifier for gateway'],
        };
    }

    /**
     * Midtrans docs: signature_key = SHA512(order_id + status_code + gross_amount + ServerKey).
     */
    public function verifyMidtrans(Request $request, ?string $serverKey): array
    {
        if (! $serverKey) {
            return ['verified' => false, 'mode' => 'unconfigured', 'reason' => 'server key not configured'];
        }
        $given = (string) $request->input('signature_key', '');
        $expected = hash('sha512', implode('', [
            (string) $request->input('order_id', ''),
            (string) $request->input('status_code', ''),
            (string) $request->input('gross_amount', ''),
            $serverKey,
        ]));
        if ($given === '' || ! hash_equals($expected, $given)) {
            return ['verified' => false, 'mode' => 'signature', 'reason' => 'signature mismatch'];
        }
        return ['verified' => true, 'mode' => 'signature', 'reason' => null];
    }

    /**
     * Xendit docs: callbacks carry header X-CALLBACK-TOKEN equal to the
     * account's callback verification token (stored here in api_key).
     */
    public function verifyXendit(Request $request, ?string $callbackToken): array
    {
        if (! $callbackToken) {
            return ['verified' => false, 'mode' => 'unconfigured', 'reason' => 'callback token not configured'];
        }
        $given = (string) $request->header('X-CALLBACK-TOKEN', '');
        if ($given === '' || ! hash_equals($callbackToken, $given)) {
            return ['verified' => false, 'mode' => 'signature', 'reason' => 'callback token mismatch'];
        }
        return ['verified' => true, 'mode' => 'signature', 'reason' => null];
    }

    /**
     * Tripay docs: signature = HMAC_SHA256(merchant_code + merchant_ref + amount, privateKey).
     */
    public function verifyTripay(Request $request, ?string $privateKey, ?string $merchantCode): array
    {
        if (! $privateKey || ! $merchantCode) {
            return ['verified' => false, 'mode' => 'unconfigured', 'reason' => 'private key/merchant not configured'];
        }
        $given = (string) ($request->input('signature') ?? $request->input('SIGNATURE') ?? '');
        $expected = hash_hmac('sha256', $merchantCode . (string) ($request->input('merchant_ref') ?? $request->input('reference') ?? '') . (string) ($request->input('total_amount') ?? $request->input('amount') ?? ''), $privateKey);
        if ($given === '' || ! hash_equals($expected, strtolower($given))) {
            return ['verified' => false, 'mode' => 'signature', 'reason' => 'signature mismatch'];
        }
        return ['verified' => true, 'mode' => 'signature', 'reason' => null];
    }

    /**
     * Duitku docs: signature = MD5(merchantCode + merchantOrderId + paymentAmount + apiKey).
     */
    public function verifyDuitku(Request $request, ?string $apiKey, ?string $merchantCode): array
    {
        if (! $apiKey || ! $merchantCode) {
            return ['verified' => false, 'mode' => 'unconfigured', 'reason' => 'api key/merchant not configured'];
        }
        $given = (string) $request->input('signature', '');
        $expected = md5($merchantCode . (string) $request->input('merchantOrderId', '') . (string) $request->input('amount', '') . $apiKey);
        if ($given === '' || ! hash_equals($expected, strtolower($given))) {
            return ['verified' => false, 'mode' => 'signature', 'reason' => 'signature mismatch'];
        }
        return ['verified' => true, 'mode' => 'signature', 'reason' => null];
    }

    /**
     * DOKU Checkout v1 docs (simplified HMAC):
     * Signature = Base64(HMAC_SHA256("Client-Id:{id}\nRequest-Id:{rid}\nRequest-Timestamp:{ts}\nRequest-Target:{target}\nDigest:{digest}", secret)),
     * header "Signature: HMACSHA256=<sig>", Digest = Base64(SHA256(rawBody)).
     */
    public function verifyDoku(Request $request, ?string $secret, ?string $clientId): array
    {
        if (! $secret || ! $clientId) {
            return ['verified' => false, 'mode' => 'unconfigured', 'reason' => 'secret/client id not configured'];
        }
        $header = (string) $request->header('Signature', '');
        if (! str_starts_with($header, 'HMACSHA256=')) {
            return ['verified' => false, 'mode' => 'signature', 'reason' => 'missing signature header'];
        }
        $given = substr($header, strlen('HMACSHA256='));
        $digest = base64_encode(hash('sha256', $request->getContent(), true));
        $target = '/' . ltrim($request->path(), '/');
        $raw = implode("\n", [
            'Client-Id:' . $clientId,
            'Request-Id:' . (string) $request->header('Request-Id', ''),
            'Request-Timestamp:' . (string) $request->header('Request-Timestamp', ''),
            'Request-Target:' . $target,
            'Digest:' . $digest,
        ]);
        $expected = base64_encode(hash_hmac('sha256', $raw, $secret, true));
        if (! hash_equals($expected, $given)) {
            return ['verified' => false, 'mode' => 'signature', 'reason' => 'signature mismatch'];
        }
        return ['verified' => true, 'mode' => 'signature', 'reason' => null];
    }
}

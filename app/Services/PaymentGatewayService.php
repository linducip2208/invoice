<?php

namespace App\Services;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Create a payment transaction via the configured gateway.
     */
    public function createTransaction(PaymentGateway $gateway, array $data): array
    {
        $apiKey = $gateway->getNonEncryptedKey('api_key');
        $apiSecret = $gateway->getNonEncryptedKey('api_secret');
        $serverKey = $gateway->getNonEncryptedKey('server_key');
        $clientKey = $gateway->getNonEncryptedKey('client_key');
        $merchantId = $gateway->getNonEncryptedKey('merchant_id');

        $baseUrl = rtrim($gateway->base_url, '/');
        $extraHeaders = $gateway->extra_headers ?? [];

        return match ($gateway->gateway) {
            'midtrans' => $this->midtransCharge($baseUrl, $serverKey, $data),
            'xendit'   => $this->xenditCharge($baseUrl, $apiKey, $data),
            'tripay'   => $this->tripayCharge($baseUrl, $apiKey, $apiSecret, $merchantId, $data),
            'duitku'   => $this->duitkuCharge($baseUrl, $apiKey, $merchantId, $data),
            'doku'     => $this->dokuCharge($baseUrl, $apiKey, $apiSecret, $merchantId, $data),
            'oy'       => $this->oyCharge($baseUrl, $apiKey, $apiSecret, $data),
            'ipaymu'   => $this->ipaymuCharge($baseUrl, $apiKey, $apiSecret, $data),
            'finpay'   => $this->finpayCharge($baseUrl, $apiKey, $apiSecret, $merchantId, $data),
            default    => $this->genericCharge($baseUrl, $apiKey, $apiSecret, $extraHeaders, $data),
        };
    }

    /**
     * Check transaction status from the gateway.
     */
    public function checkStatus(PaymentGateway $gateway, string $transactionId): array
    {
        $apiKey = $gateway->getNonEncryptedKey('api_key');
        $apiSecret = $gateway->getNonEncryptedKey('api_secret');
        $serverKey = $gateway->getNonEncryptedKey('server_key');
        $merchantId = $gateway->getNonEncryptedKey('merchant_id');

        $baseUrl = rtrim($gateway->base_url, '/');

        return match ($gateway->gateway) {
            'midtrans' => $this->midtransStatus($baseUrl, $serverKey, $transactionId),
            'tripay'   => $this->tripayStatus($baseUrl, $apiKey, $apiSecret, $merchantId, $transactionId),
            'ipaymu'   => $this->ipaymuStatus($baseUrl, $apiKey, $apiSecret, $transactionId),
            default    => $this->genericStatus($baseUrl, $apiKey, $apiSecret, $transactionId),
        };
    }

    // ─── MIDTRANS ────────────────────────────────
    private function midtransCharge(string $baseUrl, ?string $serverKey, array $data): array
    {
        $payload = [
            'payment_type' => $this->mapChannelToMidtransType($data['channel'] ?? 'bank_transfer'),
            'transaction_details' => [
                'order_id' => $data['order_id'],
                'gross_amount' => (int) $data['amount'],
            ],
            'customer_details' => [
                'first_name' => $data['customer_name'] ?? 'Customer',
                'email' => $data['customer_email'] ?? '',
                'phone' => $data['customer_phone'] ?? '',
            ],
            'item_details' => [[
                'id' => $data['invoice_no'] ?? 'INV',
                'price' => (int) $data['amount'],
                'quantity' => 1,
                'name' => 'Invoice ' . ($data['invoice_no'] ?? ''),
            ]],
        ];

        if ($data['channel'] === 'bank_transfer' || in_array($data['channel'], ['bca','bni','bri','mandiri','permata','cimb'])) {
            $bank = $data['channel'] === 'bank_transfer' ? 'bca' : $data['channel'];
            $payload['bank_transfer'] = ['bank' => $bank];
        }
        if ($data['channel'] === 'gopay') {
            $payload['gopay'] = ['enable_callback' => true];
        }
        if ($data['channel'] === 'shopeepay') {
            $payload['shopeepay'] = ['enable_callback' => true];
        }
        if ($data['channel'] === 'qris') {
            $payload['payment_type'] = 'gopay';
        }
        if ($data['channel'] === 'convenience_store') {
            $payload['cstore'] = ['store' => 'alfamart'];
        }

        $auth = base64_encode($serverKey . ':');
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/charge', $payload);

        Log::info('Midtrans charge', ['response' => $response->json()]);
        return $this->normalizeMidtransResponse($response->json());
    }

    private function midtransStatus(string $baseUrl, ?string $serverKey, string $orderId): array
    {
        $auth = base64_encode($serverKey . ':');
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
        ])->get($baseUrl . '/' . $orderId . '/status');

        return $this->normalizeMidtransResponse($response->json());
    }

    private function normalizeMidtransResponse(array $resp): array
    {
        $status = $resp['transaction_status'] ?? 'pending';
        return [
            'success' => in_array($status, ['settlement', 'capture']),
            'transaction_id' => $resp['transaction_id'] ?? $resp['order_id'] ?? null,
            'status' => $status,
            'payment_url' => $resp['redirect_url'] ?? null,
            'va_number' => $resp['va_numbers'][0]['va_number'] ?? $resp['permata_va_number'] ?? null,
            'payment_code' => $resp['payment_code'] ?? null,
            'raw' => $resp,
        ];
    }

    // ─── XENDIT ───────────────────────────────────
    private function xenditCharge(string $baseUrl, ?string $apiKey, array $data): array
    {
        $channel = $data['channel'] ?? 'bank_transfer';
        $bankMap = ['bca'=>'BCA','bni'=>'BNI','bri'=>'BRI','mandiri'=>'MANDIRI'];

        if (isset($bankMap[$channel]) || $channel === 'bank_transfer') {
            $bank = $bankMap[$channel] ?? 'BCA';
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/callback_virtual_accounts', [
                'external_id' => $data['order_id'],
                'bank_code' => $bank,
                'name' => $data['customer_name'] ?? 'Customer',
                'expected_amount' => (int) $data['amount'],
                'is_closed' => true,
                'is_single_use' => true,
                'expiration_date' => now()->addDays(1)->toISOString(),
            ]);
        } elseif ($channel === 'qris') {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/qr_codes', [
                'external_id' => $data['order_id'],
                'type' => 'DYNAMIC',
                'callback_url' => $data['callback_url'] ?? '',
                'amount' => (int) $data['amount'],
            ]);
        } else {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':'),
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/ewallets/charges', [
                'reference_id' => $data['order_id'],
                'currency' => 'IDR',
                'amount' => (int) $data['amount'],
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => strtoupper($channel),
                'channel_properties' => [
                    'success_redirect_url' => $data['success_url'] ?? url('/'),
                ],
            ]);
        }

        $resp = $response->json();
        $transactionId = $resp['id'] ?? $resp['reference_id'] ?? null;

        // Fail closed: never report success when the provider rejected the
        // charge (HTTP error or missing transaction reference). Callers
        // create local Payment rows only after this returns success.
        if (! $response->successful() || ! $transactionId) {
            throw new \RuntimeException(
                'Xendit charge failed: ' . ($resp['message'] ?? $resp['error'] ?? 'HTTP ' . $response->status())
            );
        }

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => $resp['status'] ?? 'PENDING',
            'payment_url' => $resp['actions'][0]['url'] ?? null,
            'va_number' => $resp['account_number'] ?? null,
            'raw' => $resp,
        ];
    }

    // ─── TRIPAY ───────────────────────────────────
    private function tripayCharge(string $baseUrl, ?string $apiKey, ?string $apiSecret, ?string $merchantId, array $data): array
    {
        $channel = $this->mapChannelToTripay($data['channel'] ?? 'bank_transfer');
        $payload = [
            'method' => $channel,
            'merchant_ref' => $data['order_id'],
            'amount' => (int) $data['amount'],
            'customer_name' => $data['customer_name'] ?? 'Customer',
            'customer_email' => $data['customer_email'] ?? '',
            'customer_phone' => $data['customer_phone'] ?? '',
            'order_items' => [[
                'name' => 'Invoice ' . ($data['invoice_no'] ?? ''),
                'price' => (int) $data['amount'],
                'quantity' => 1,
            ]],
            'expired_time' => time() + 86400,
            'signature' => hash_hmac('sha256', $merchantId . $data['order_id'] . (int)$data['amount'], $apiSecret),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/transaction/create', $payload);

        $resp = $response->json();
        return [
            'success' => $resp['success'] ?? false,
            'transaction_id' => $resp['data']['reference'] ?? null,
            'status' => $resp['data']['status'] ?? 'UNPAID',
            'payment_url' => $resp['data']['checkout_url'] ?? null,
            'va_number' => $resp['data']['pay_code'] ?? null,
            'raw' => $resp,
        ];
    }

    private function tripayStatus(string $baseUrl, ?string $apiKey, ?string $apiSecret, ?string $merchantId, string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->get($baseUrl . '/transaction/detail', [
            'reference' => $reference,
            'merchant_ref' => $reference,
        ]);

        $resp = $response->json();
        return [
            'success' => $resp['success'] ?? false,
            'transaction_id' => $resp['data']['reference'] ?? null,
            'status' => $resp['data']['status'] ?? 'UNPAID',
            'raw' => $resp,
        ];
    }

    // ─── DUITKU ───────────────────────────────────
    private function duitkuCharge(string $baseUrl, ?string $apiKey, ?string $merchantId, array $data): array
    {
        $channel = $this->mapChannelToDuitku($data['channel'] ?? 'bank_transfer');
        $amount = (int) $data['amount'];
        $timestamp = time();
        $signature = hash('sha256', $merchantId . $data['order_id'] . $amount . $apiKey);

        $payload = [
            'merchantCode' => $merchantId,
            'paymentAmount' => $amount,
            'paymentMethod' => $channel,
            'merchantOrderId' => $data['order_id'],
            'productDetails' => 'Invoice ' . ($data['invoice_no'] ?? ''),
            'customerVaName' => $data['customer_name'] ?? 'Customer',
            'email' => $data['customer_email'] ?? '',
            'phoneNumber' => $data['customer_phone'] ?? '',
            'callbackUrl' => $data['callback_url'] ?? '',
            'signature' => $signature,
            'expiryPeriod' => 1440,
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($baseUrl . '/inquiry', $payload);

        $resp = $response->json();
        return [
            'success' => ($resp['statusCode'] ?? '') === '00',
            'transaction_id' => $resp['reference'] ?? $data['order_id'],
            'status' => ($resp['statusCode'] ?? '') === '00' ? 'PENDING' : 'FAILED',
            'payment_url' => $resp['paymentUrl'] ?? null,
            'va_number' => $resp['vaNumber'] ?? null,
            'raw' => $resp,
        ];
    }

    // ─── DOKU ─────────────────────────────────────
    private function dokuCharge(string $baseUrl, ?string $apiKey, ?string $apiSecret, ?string $merchantId, array $data): array
    {
        $requestId = $data['order_id'];
        $requestTimestamp = now()->toISOString();
        $digest = base64_encode(hash('sha256', json_encode([
            'order' => ['invoice_number' => $data['order_id'], 'amount' => (int) $data['amount']],
        ]), true));
        $signature = base64_encode(hash_hmac('sha256', "Client-Id:{$apiKey}\nRequest-Id:{$requestId}\nRequest-Timestamp:{$requestTimestamp}\nRequest-Target:/checkout/v1/payment", $apiSecret, true));

        $response = Http::withHeaders([
            'Client-Id' => $apiKey,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestTimestamp,
            'Signature' => 'HMACSHA256=' . $signature,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/checkout/v1/payment', [
            'order' => [
                'invoice_number' => $data['order_id'],
                'amount' => (int) $data['amount'],
            ],
            'payment' => [
                'payment_due_date' => 60,
            ],
            'customer' => [
                'name' => $data['customer_name'] ?? 'Customer',
                'email' => $data['customer_email'] ?? '',
            ],
        ]);

        $resp = $response->json();
        $transactionId = $resp['order']['invoice_number'] ?? null;

        // Fail closed: never report success when DOKU rejected the payment.
        if (! $response->successful() || ! $transactionId) {
            throw new \RuntimeException(
                'DOKU charge failed: ' . ($resp['error']['message'] ?? $resp['message'] ?? 'HTTP ' . $response->status())
            );
        }

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => 'PENDING',
            'payment_url' => $resp['response']['url'] ?? null,
            'raw' => $resp,
        ];
    }

    // ─── OY! INDONESIA ────────────────────────────
    private function oyCharge(string $baseUrl, ?string $apiKey, ?string $apiSecret, array $data): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'x-oy-username' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/generate-static-va', [
            'partner_user_id' => $data['customer_email'] ?? 'customer@example.com',
            'bank_code' => $this->mapChannelToOyBank($data['channel'] ?? 'bca'),
            'amount' => (int) $data['amount'],
            'is_open' => false,
            'is_single_use' => true,
            'expiration' => 60,
            'trx_expiration_time' => 60,
            'notes' => 'Invoice ' . ($data['invoice_no'] ?? ''),
        ]);

        $resp = $response->json();
        return [
            'success' => $resp['success'] ?? false,
            'transaction_id' => $resp['trx_id'] ?? null,
            'status' => $resp['status'] ?? 'PENDING',
            'va_number' => $resp['va_number'] ?? null,
            'raw' => $resp,
        ];
    }

    // ─── IPAYMU ───────────────────────────────────
    private function ipaymuCharge(string $baseUrl, ?string $apiKey, ?string $apiSecret, array $data): array
    {
        $body = [
            'product' => ['Invoice ' . ($data['invoice_no'] ?? '')],
            'qty' => [1],
            'price' => [(int) $data['amount']],
            'amount' => (int) $data['amount'],
            'returnUrl' => $data['success_url'] ?? url('/'),
            'cancelUrl' => $data['cancel_url'] ?? url('/'),
            'notifyUrl' => $data['callback_url'] ?? '',
            'referenceId' => $data['order_id'],
            'buyerName' => $data['customer_name'] ?? 'Customer',
            'buyerEmail' => $data['customer_email'] ?? '',
            'buyerPhone' => $data['customer_phone'] ?? '',
        ];

        $bodyEncoded = json_encode($body);
        $signature = hash_hmac('sha256', $bodyEncoded, $apiSecret);

        $response = Http::withHeaders([
            'va' => $apiKey,
            'signature' => $signature,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/payment', $body);

        $resp = $response->json();
        return [
            'success' => ($resp['Status'] ?? 0) === 200,
            'transaction_id' => $resp['Data']['TransactionId'] ?? null,
            'status' => $resp['Data']['Status'] ?? 'pending',
            'payment_url' => $resp['Data']['Url'] ?? null,
            'va_number' => $resp['Data']['PaymentNo'] ?? null,
            'raw' => $resp,
        ];
    }

    private function ipaymuStatus(string $baseUrl, ?string $apiKey, ?string $apiSecret, string $transactionId): array
    {
        $body = json_encode(['transactionId' => $transactionId]);
        $signature = hash_hmac('sha256', $body, $apiSecret);

        $response = Http::withHeaders([
            'va' => $apiKey,
            'signature' => $signature,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/transaction', ['transactionId' => $transactionId]);

        $resp = $response->json();
        return [
            'success' => ($resp['Status'] ?? 0) === 200,
            'transaction_id' => $transactionId,
            'status' => strtolower($resp['Data']['Status'] ?? 'pending'),
            'raw' => $resp,
        ];
    }

    // ─── FINPAY ───────────────────────────────────
    private function finpayCharge(string $baseUrl, ?string $apiKey, ?string $apiSecret, ?string $merchantId, array $data): array
    {
        $timestamp = now()->format('Y-m-d\TH:i:s\Z');
        $signature = hash_hmac('sha256', $merchantId . '|' . $data['order_id'] . '|' . (int)$data['amount'] . '|' . $timestamp, $apiSecret);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'X-Timestamp' => $timestamp,
            'X-Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->post($baseUrl . '/payment', [
            'merchant_id' => $merchantId,
            'invoice_no' => $data['order_id'],
            'amount' => (int) $data['amount'],
            'payment_type' => 'va',
            'customer_name' => $data['customer_name'] ?? 'Customer',
            'customer_email' => $data['customer_email'] ?? '',
            'callback_url' => $data['callback_url'] ?? '',
        ]);

        $resp = $response->json();
        return [
            'success' => ($resp['response_code'] ?? '') === '00',
            'transaction_id' => $resp['transaction_id'] ?? null,
            'status' => 'PENDING',
            'va_number' => $resp['va_number'] ?? null,
            'raw' => $resp,
        ];
    }

    // ─── GENERIC (custom gateway) ─────────────────
    private function genericCharge(string $baseUrl, ?string $apiKey, ?string $apiSecret, array $extraHeaders, array $data): array
    {
        $headers = array_merge([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . ($apiKey ?? ''),
        ], $extraHeaders);

        $payload = [
            'order_id' => $data['order_id'],
            'amount' => (int) $data['amount'],
            'invoice_no' => $data['invoice_no'] ?? '',
            'customer_name' => $data['customer_name'] ?? 'Customer',
            'customer_email' => $data['customer_email'] ?? '',
            'customer_phone' => $data['customer_phone'] ?? '',
            'channel' => $data['channel'] ?? 'bank_transfer',
            'callback_url' => $data['callback_url'] ?? '',
        ];

        $response = Http::withHeaders($headers)->post($baseUrl . '/create-payment', $payload);
        $resp = $response->json();

        return [
            'success' => $resp['success'] ?? $response->successful(),
            'transaction_id' => $resp['transaction_id'] ?? $resp['id'] ?? null,
            'status' => $resp['status'] ?? 'PENDING',
            'payment_url' => $resp['payment_url'] ?? $resp['redirect_url'] ?? null,
            'va_number' => $resp['va_number'] ?? $resp['payment_code'] ?? null,
            'raw' => $resp,
        ];
    }

    private function genericStatus(string $baseUrl, ?string $apiKey, ?string $apiSecret, string $transactionId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . ($apiKey ?? ''),
        ])->get($baseUrl . '/payment-status/' . $transactionId);

        $resp = $response->json();
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => $resp['status'] ?? 'PENDING',
            'raw' => $resp,
        ];
    }

    // ─── CHANNEL MAPPING HELPERS ──────────────────

    private function mapChannelToMidtransType(string $channel): string
    {
        return match ($channel) {
            'bca', 'bni', 'bri', 'mandiri', 'permata', 'cimb' => 'bank_transfer',
            'credit_card' => 'credit_card',
            'gopay' => 'gopay',
            'shopeepay' => 'shopeepay',
            'qris' => 'gopay',
            'convenience_store' => 'cstore',
            default => 'bank_transfer',
        };
    }

    private function mapChannelToTripay(string $channel): string
    {
        return match ($channel) {
            'bca' => 'BCAVA',
            'bni' => 'BNIVA',
            'bri' => 'BRIVA',
            'mandiri' => 'MANDIRIVA',
            'permata' => 'PERMATAVA',
            'cimb' => 'CIMBVA',
            'qris' => 'QRISC',
            'convenience_store' => 'ALFAMART',
            default => 'BCAVA',
        };
    }

    private function mapChannelToDuitku(string $channel): string
    {
        return match ($channel) {
            'bca' => 'BC',
            'bni' => 'BT',
            'bri' => 'BR',
            'mandiri' => 'M2',
            'cimb' => 'NC',
            'credit_card' => 'CC',
            'qris' => 'QR',
            'convenience_store' => 'AT',
            default => 'BC',
        };
    }

    private function mapChannelToOyBank(string $channel): string
    {
        return match ($channel) {
            'bca' => '014',
            'bni' => '009',
            'bri' => '002',
            'mandiri' => '008',
            'permata' => '013',
            default => '002',
        };
    }
}

<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;

/**
 * Format-based generic WA adapter — covers most Indonesian WA providers
 * (Wablas, Fonnte, Watzap, Mpedia, etc) yang pakai REST POST sederhana.
 *
 * Config dari workspace settings (JSON):
 * {
 *   "endpoint": "https://api.fonnte.com/send",
 *   "auth_header": "Authorization: <token>",
 *   "phone_field": "target",
 *   "message_field": "message",
 *   "extra_payload": {"countryCode": "62"}
 * }
 */
class GenericRestAdapter implements WhatsappAdapter
{
    public function __construct(
        private string $endpoint,
        private string $authHeader,
        private string $phoneField = 'target',
        private string $messageField = 'message',
        private array $extraPayload = [],
    ) {}

    public function sendText(string $toPhone, string $message): array
    {
        try {
            [$headerName, $headerValue] = $this->parseAuthHeader();

            $payload = array_merge([
                $this->phoneField => $this->normalizePhone($toPhone),
                $this->messageField => $message,
            ], $this->extraPayload);

            $resp = Http::timeout(15)
                ->withHeaders([$headerName => $headerValue])
                ->asForm()
                ->post($this->endpoint, $payload);

            if (! $resp->successful()) {
                return ['ok' => false, 'msg_id' => null, 'error' => "HTTP {$resp->status()}: " . substr($resp->body(), 0, 200)];
            }

            $body = $resp->json();
            $msgId = $body['id'] ?? $body['message_id'] ?? $body['data']['id'] ?? null;

            return ['ok' => true, 'msg_id' => $msgId, 'error' => null];
        } catch (\Throwable $e) {
            return ['ok' => false, 'msg_id' => null, 'error' => $e->getMessage()];
        }
    }

    private function parseAuthHeader(): array
    {
        $parts = explode(':', $this->authHeader, 2);
        if (count($parts) === 2) {
            return [trim($parts[0]), trim($parts[1])];
        }
        return ['Authorization', $this->authHeader];
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
        if (! str_starts_with($phone, '62') && strlen($phone) >= 8) $phone = '62' . $phone;
        return $phone;
    }
}

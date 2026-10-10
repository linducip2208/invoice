<?php

namespace App\Services\Ai\Features;

use App\Services\Ai\AiClient;

class InvoiceFromEmail
{
    /**
     * Parse email body & extract invoice draft data.
     */
    public function extract(string $emailSubject, string $emailBody, ?string $senderEmail = null): array
    {
        $client = AiClient::forFeature('invoice_from_email');
        if (! $client) {
            throw new \RuntimeException('AI provider belum diset.');
        }

        $prompt = <<<EOT
Email berikut adalah pesan dari klien yang minta dibuatkan invoice.
Ekstrak data untuk bikin invoice draft.

Subject: {$emailSubject}
From: {$senderEmail}

Body:
{$emailBody}

Output JSON:
{
  "client_name": "string atau null",
  "client_email": "{$senderEmail}",
  "items": [
    {"description": "deliverable spesifik", "quantity": number, "unit_price": number}
  ],
  "currency": "IDR",
  "due_in_days": 14,
  "notes": "opsional dari user"
}

HANYA JSON valid, tanpa markdown fence.
EOT;

        $response = $client->chat([
            ['role' => 'system', 'content' => 'You extract invoice data from client emails in Indonesian/English.'],
            ['role' => 'user', 'content' => $prompt],
        ], maxTokens: 1000, temperature: 0.2);

        $text = trim($response['text'] ?? '');
        $text = preg_replace('/^```(json)?|```$/m', '', $text);
        $data = json_decode(trim($text), true);

        if (! is_array($data)) {
            throw new \RuntimeException('AI response invalid.');
        }

        return $data;
    }
}

<?php

namespace App\Services\Ai\Features;

use App\Services\Ai\AiClient;
use Illuminate\Support\Facades\Storage;

class OcrReceipt
{
    /**
     * Extract receipt data from an uploaded image.
     * Returns array with: merchant_name, date, subtotal, tax, total, currency, items[].
     */
    public function extract(string $publicImagePath): array
    {
        $client = AiClient::forFeature('ocr_receipt');
        if (! $client) {
            throw new \RuntimeException('AI provider belum diset. Setup di /ai-providers (butuh model vision-capable).');
        }

        $absolutePath = Storage::disk('public')->path($publicImagePath);
        if (! file_exists($absolutePath)) {
            throw new \RuntimeException('File tidak ditemukan.');
        }

        $imageData = base64_encode(file_get_contents($absolutePath));
        $mime = mime_content_type($absolutePath) ?: 'image/jpeg';

        $prompt = <<<EOT
Foto berikut adalah struk/receipt belanja atau invoice supplier.
Extract data berikut dalam format JSON:
{
  "merchant_name": "string atau null",
  "date": "YYYY-MM-DD atau null",
  "subtotal": number atau null,
  "tax": number atau null,
  "total": number atau null,
  "currency": "IDR|USD|... atau null",
  "category_suggested": "Office Supplies|Travel|Meals|Software|Marketing|Equipment|Other",
  "items": [
    {"description": "string", "qty": number, "unit_price": number, "total": number}
  ]
}

Aturan:
- Output HANYA valid JSON, tanpa markdown fence, tanpa penjelasan.
- Kalau tidak terbaca → kembalikan field sebagai null.
- Tidak yakin → null.
- Untuk Indonesia, currency biasanya IDR.
EOT;

        // Build vision message — OpenAI compatible format
        $messages = [
            [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $prompt],
                    ['type' => 'image_url', 'image_url' => ['url' => "data:{$mime};base64,{$imageData}"]],
                ],
            ],
        ];

        $response = $client->chat($messages, maxTokens: 1500, temperature: 0.1);
        $text = trim($response['text'] ?? '');

        // Strip markdown fences if any
        $text = preg_replace('/^```(json)?|```$/m', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);
        if (! is_array($data)) {
            throw new \RuntimeException('AI response tidak valid JSON. Coba upload ulang.');
        }

        return $data;
    }
}

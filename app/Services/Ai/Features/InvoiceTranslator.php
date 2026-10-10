<?php

namespace App\Services\Ai\Features;

use App\Models\Invoice;
use App\Services\Ai\AiClient;
use Illuminate\Support\Facades\DB;

class InvoiceTranslator
{
    public const LOCALES = [
        'en' => 'English',
        'zh' => 'Chinese (Simplified)',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'es' => 'Spanish',
        'fr' => 'French',
    ];

    public function translate(Invoice $invoice, string $locale): array
    {
        if (! isset(self::LOCALES[$locale])) {
            throw new \RuntimeException('Locale not supported.');
        }

        $client = AiClient::forFeature('invoice_translator');
        if (! $client) {
            throw new \RuntimeException('Belum ada AI provider aktif.');
        }

        $invoice->loadMissing('items');
        $sourceFields = [
            'notes' => $invoice->notes ?? '',
            'terms' => $invoice->terms ?? '',
            'items' => $invoice->items->map(fn ($i) => $i->description ?? '')->toArray(),
        ];

        $targetLang = self::LOCALES[$locale];
        $sourceJson = json_encode($sourceFields, JSON_UNESCAPED_UNICODE);

        $prompt = <<<EOT
Translate the following invoice fields from Indonesian to {$targetLang}.
Preserve format. Return ONLY valid JSON with the same structure.

Source:
{$sourceJson}

Output JSON:
EOT;

        $response = $client->chat([
            ['role' => 'system', 'content' => "You are a professional translator specializing in business invoices."],
            ['role' => 'user', 'content' => $prompt],
        ], maxTokens: 2000, temperature: 0.3);

        $text = trim($response['text'] ?? '');
        $text = preg_replace('/^```(json)?|```$/m', '', $text);
        $data = json_decode(trim($text), true);

        if (! is_array($data)) {
            throw new \RuntimeException('AI response tidak valid JSON.');
        }

        DB::table('invoice_translations')->updateOrInsert(
            ['invoice_id' => $invoice->id, 'locale' => $locale],
            [
                'fields'        => json_encode($data, JSON_UNESCAPED_UNICODE),
                'translated_at' => now(),
                'updated_at'    => now(),
                'created_at'    => now(),
            ]
        );

        return $data;
    }
}

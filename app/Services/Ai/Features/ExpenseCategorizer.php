<?php

namespace App\Services\Ai\Features;

use App\Services\Ai\AiClient;

class ExpenseCategorizer
{
    public const CATEGORIES = ['Office Supplies', 'Travel', 'Meals', 'Software', 'Marketing', 'Equipment', 'Subscription', 'Other'];

    /**
     * Suggest category based on description, vendor, and amount.
     * Returns ['category' => string, 'confidence' => float]
     */
    public function suggest(string $description, ?string $vendor = null, ?float $amount = null): array
    {
        $client = AiClient::forFeature('expense_categorizer');
        if (! $client) {
            return ['category' => 'Other', 'confidence' => 0];
        }

        $context = "Vendor: " . ($vendor ?: '-') . "\nAmount: " . ($amount ?: '-') . "\nDescription: {$description}";
        $cats = implode(', ', self::CATEGORIES);

        $prompt = <<<EOT
Anda kategorisator expense bisnis. Klasifikasikan transaksi berikut ke dalam SATU kategori dari list ini:
{$cats}

Transaksi:
{$context}

Output JSON: {"category": "...", "confidence": 0.0-1.0}
HANYA JSON, tanpa penjelasan.
EOT;

        try {
            $response = $client->chat([
                ['role' => 'system', 'content' => 'You categorize business expenses concisely.'],
                ['role' => 'user', 'content' => $prompt],
            ], maxTokens: 100, temperature: 0.1);

            $text = trim($response['text'] ?? '');
            $text = preg_replace('/^```(json)?|```$/m', '', $text);
            $data = json_decode(trim($text), true);

            if (is_array($data) && in_array($data['category'] ?? '', self::CATEGORIES)) {
                return [
                    'category'   => $data['category'],
                    'confidence' => max(0, min(1, (float) ($data['confidence'] ?? 0.5))),
                ];
            }
        } catch (\Throwable $e) {
            // Fall through
        }

        return ['category' => 'Other', 'confidence' => 0];
    }
}

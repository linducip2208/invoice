<?php

namespace App\Services\Ai\Features;

use App\Services\Ai\AiClient;

class DescriptionGenerator
{
    /**
     * Expand a brief keyword/title into a polished invoice line description.
     */
    public function expand(string $keyword, ?string $industry = null): string
    {
        $client = AiClient::forFeature('description_generator');
        if (! $client) {
            throw new \RuntimeException('Belum ada AI provider aktif. Setup di /ai-providers.');
        }

        $industryHint = $industry ? "Industri: {$industry}." : '';
        $prompt = "Anda penulis invoice profesional. Input user: \"{$keyword}\". {$industryHint}\n"
            . "Tugas: buat deskripsi 1-2 kalimat untuk line item invoice yang:\n"
            . "- Spesifik (sebut deliverable konkret)\n"
            . "- Bahasa Indonesia formal\n"
            . "- Maksimal 200 karakter\n"
            . "- Tidak ambigu\n\n"
            . "Output: HANYA deskripsi, tanpa penjelasan tambahan, tanpa tanda kutip.";

        $response = $client->chat(
            messages: [
                ['role' => 'system', 'content' => 'You write concise, professional invoice line descriptions in Indonesian.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            maxTokens: 200,
            temperature: 0.5,
        );

        return trim($response['text'] ?? '', " \"\n\t\r");
    }
}

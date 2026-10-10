<?php

namespace App\Services\Ai;

use App\Models\AiProvider;
use App\Models\AiUsageLog;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Generic AI client — format-based, no vendor names.
 * Routes calls to the right adapter shape (OpenAI compatible, Anthropic, Gemini).
 */
class AiClient
{
    public function __construct(private AiProvider $provider) {}

    public static function forFeature(string $feature, ?int $workspaceId = null): ?self
    {
        $provider = AiProvider::where('is_active', true)
            ->latest()
            ->first();

        return $provider ? new self($provider) : null;
    }

    /**
     * Chat completion — auto-routes to format-specific endpoint.
     */
    public function chat(array $messages, ?string $model = null, int $maxTokens = 500, float $temperature = 0.7): array
    {
        $model = $model ?? $this->provider->default_model ?? 'gpt-4o-mini';
        $start = microtime(true);

        try {
            $response = match ($this->provider->api_format) {
                'openai_compatible' => $this->openaiChat($messages, $model, $maxTokens, $temperature),
                'anthropic'         => $this->anthropicChat($messages, $model, $maxTokens, $temperature),
                'gemini'            => $this->geminiChat($messages, $model, $maxTokens, $temperature),
                default             => throw new \RuntimeException('Format not supported: ' . $this->provider->api_format),
            };

            $duration = (int) ((microtime(true) - $start) * 1000);
            $this->logUsage($model, $response, $duration, 'success');

            return $response;
        } catch (\Throwable $e) {
            $duration = (int) ((microtime(true) - $start) * 1000);
            $this->logUsage($model, [], $duration, 'error', $e->getMessage());
            throw $e;
        }
    }

    private function openaiChat(array $messages, string $model, int $maxTokens, float $temperature): array
    {
        $resp = $this->httpClient()->post('/chat/completions', [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);
        $resp->throw();
        $body = $resp->json();
        return [
            'text'          => $body['choices'][0]['message']['content'] ?? '',
            'input_tokens'  => $body['usage']['prompt_tokens'] ?? 0,
            'output_tokens' => $body['usage']['completion_tokens'] ?? 0,
            'raw'           => $body,
        ];
    }

    private function anthropicChat(array $messages, string $model, int $maxTokens, float $temperature): array
    {
        $system = '';
        $userMessages = [];
        foreach ($messages as $m) {
            if ($m['role'] === 'system') $system .= $m['content'] . "\n";
            else $userMessages[] = $m;
        }

        $resp = $this->httpClient()
            ->withHeaders(['anthropic-version' => '2023-06-01', 'x-api-key' => $this->provider->api_key])
            ->post('/v1/messages', [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'system' => trim($system),
                'messages' => $userMessages,
            ]);
        $resp->throw();
        $body = $resp->json();
        return [
            'text'          => $body['content'][0]['text'] ?? '',
            'input_tokens'  => $body['usage']['input_tokens'] ?? 0,
            'output_tokens' => $body['usage']['output_tokens'] ?? 0,
            'raw'           => $body,
        ];
    }

    private function geminiChat(array $messages, string $model, int $maxTokens, float $temperature): array
    {
        $contents = [];
        $system = '';
        foreach ($messages as $m) {
            if ($m['role'] === 'system') $system .= $m['content'] . "\n";
            else $contents[] = ['role' => $m['role'] === 'assistant' ? 'model' : 'user', 'parts' => [['text' => $m['content']]]];
        }

        $url = "/v1beta/models/{$model}:generateContent?key={$this->provider->api_key}";
        $body = [
            'contents' => $contents,
            'generationConfig' => ['maxOutputTokens' => $maxTokens, 'temperature' => $temperature],
        ];
        if ($system) $body['systemInstruction'] = ['parts' => [['text' => trim($system)]]];

        $resp = Http::baseUrl($this->provider->base_url)->timeout(30)->post($url, $body);
        $resp->throw();
        $j = $resp->json();
        return [
            'text'          => $j['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'input_tokens'  => $j['usageMetadata']['promptTokenCount'] ?? 0,
            'output_tokens' => $j['usageMetadata']['candidatesTokenCount'] ?? 0,
            'raw'           => $j,
        ];
    }

    private function httpClient(): PendingRequest
    {
        return Http::baseUrl(rtrim($this->provider->base_url, '/'))
            ->timeout(30)
            ->withToken($this->provider->api_key)
            ->withHeaders($this->provider->extra_headers ?? []);
    }

    private function logUsage(string $model, array $response, int $durationMs, string $status, ?string $errorMessage = null): void
    {
        $inputTokens = $response['input_tokens'] ?? 0;
        $outputTokens = $response['output_tokens'] ?? 0;
        $costEstimate = 0;
        if ($this->provider->default_pricing_input) {
            $costEstimate += $inputTokens * (float) $this->provider->default_pricing_input / 1000;
        }
        if ($this->provider->default_pricing_output) {
            $costEstimate += $outputTokens * (float) $this->provider->default_pricing_output / 1000;
        }

        AiUsageLog::create([
            'workspace_id'  => $this->provider->workspace_id,
            'provider_id'   => $this->provider->id,
            'user_id'       => auth()->id(),
            'feature'       => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4)[3]['function'] ?? 'unknown',
            'model'         => $model,
            'input_tokens'  => $inputTokens,
            'output_tokens' => $outputTokens,
            'cost_estimate' => round($costEstimate, 4),
            'duration_ms'   => $durationMs,
            'status'        => $status,
            'error_message' => $errorMessage ? substr($errorMessage, 0, 500) : null,
        ]);
    }
}

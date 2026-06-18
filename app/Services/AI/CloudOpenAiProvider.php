<?php

namespace App\Services\AI;

use App\Data\AI\AiRequest;
use App\Data\AI\AiResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class CloudOpenAiProvider implements AiProviderInterface
{
    public function generate(AiRequest $request): AiResponse
    {
        $apiKey = config('ai.cloud_api_key');
        $model = $this->modelName();
        if (! $apiKey || ! $model) return AiResponse::failed('Cloud AI provider is not configured.', $this->providerName(), $model ?: null);
        try {
            $res = Http::withToken($apiKey)->timeout((int) config('ai.timeout_seconds', 60))->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an assistant for NAXAS WorkProof. Output suggestions only. Cite source IDs when provided.'],
                    ['role' => 'user', 'content' => $request->prompt."\n\nContext JSON:\n".json_encode($request->context)],
                ],
                'max_tokens' => $request->max_tokens,
                'temperature' => $request->temperature ?? 0.2,
            ]);
            if (! $res->successful()) return AiResponse::failed('Cloud AI provider failed.', $this->providerName(), $model);
            $json = $res->json();
            return new AiResponse(true, $json['choices'][0]['message']['content'] ?? '', $this->providerName(), $model, $json['usage']['prompt_tokens'] ?? null, $json['usage']['completion_tokens'] ?? null, null, null, $json);
        } catch (Throwable $e) {
            report($e);
            return AiResponse::failed('Cloud AI provider is unavailable.', $this->providerName(), $model);
        }
    }
    public function providerName(): string { return (string) config('ai.cloud_provider', 'openai'); }
    public function modelName(): string { return (string) config('ai.cloud_model', ''); }
}

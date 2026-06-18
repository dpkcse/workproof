<?php

namespace App\Services\AI;

use App\Data\AI\AiRequest;
use App\Data\AI\AiResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class LocalOllamaProvider implements AiProviderInterface
{
    public function generate(AiRequest $request): AiResponse
    {
        try {
            $payload = [
                'model' => $this->modelName(),
                'prompt' => $request->prompt."\n\nContext JSON:\n".json_encode($request->context),
                'stream' => false,
                'options' => array_filter(['temperature' => $request->temperature, 'num_predict' => $request->max_tokens]),
            ];
            $res = Http::timeout((int) config('ai.timeout_seconds', 60))->post(rtrim(config('ai.local_endpoint'), '/').'/api/generate', $payload);
            if (! $res->successful()) return AiResponse::failed('Local AI provider failed.', $this->providerName(), $this->modelName());
            $json = $res->json();
            return new AiResponse(true, $json['response'] ?? '', $this->providerName(), $this->modelName(), $json['prompt_eval_count'] ?? null, $json['eval_count'] ?? null, null, null, $json);
        } catch (Throwable $e) {
            report($e);
            return AiResponse::failed('Local AI provider is unavailable.', $this->providerName(), $this->modelName());
        }
    }
    public function providerName(): string { return 'ollama'; }
    public function modelName(): string { return (string) config('ai.local_model', 'llama3.1'); }
}

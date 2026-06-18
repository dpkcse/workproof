<?php

namespace App\Services\AI;

use App\Data\AI\AiRequest;
use App\Data\AI\AiResponse;

class NullAiProvider implements AiProviderInterface
{
    public function generate(AiRequest $request): AiResponse
    {
        return AiResponse::failed('AI is disabled.', $this->providerName(), $this->modelName());
    }
    public function providerName(): string { return 'disabled'; }
    public function modelName(): string { return 'none'; }
}

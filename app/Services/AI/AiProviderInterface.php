<?php

namespace App\Services\AI;

use App\Data\AI\AiRequest;
use App\Data\AI\AiResponse;

interface AiProviderInterface
{
    public function generate(AiRequest $request): AiResponse;
    public function providerName(): string;
    public function modelName(): string;
}

<?php

namespace App\Data\AI;

class AiRequest
{
    public function __construct(
        public int $workspace_id,
        public ?int $user_id,
        public string $feature,
        public string $prompt,
        public array $context = [],
        public ?int $max_tokens = null,
        public ?float $temperature = null,
        public ?string $source_type = null,
        public ?array $source_ids = null,
    ) {}
}

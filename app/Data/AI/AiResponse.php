<?php

namespace App\Data\AI;

class AiResponse
{
    public function __construct(
        public bool $success,
        public ?string $text,
        public ?string $provider,
        public ?string $model,
        public ?int $input_tokens = null,
        public ?int $output_tokens = null,
        public ?float $estimated_cost = null,
        public ?string $error_message = null,
        public mixed $raw = null,
    ) {}

    public static function failed(string $message, ?string $provider = null, ?string $model = null): self
    {
        return new self(false, null, $provider, $model, null, null, null, $message);
    }
}

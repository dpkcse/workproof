<?php

return [
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'disabled'),
    'local_provider' => env('AI_LOCAL_PROVIDER', 'ollama'),
    'local_endpoint' => env('AI_LOCAL_ENDPOINT', 'http://localhost:11434'),
    'local_model' => env('AI_LOCAL_MODEL', 'llama3.1'),
    'cloud_provider' => env('AI_CLOUD_PROVIDER', 'openai'),
    'cloud_model' => env('AI_CLOUD_MODEL'),
    'cloud_api_key' => env('AI_CLOUD_API_KEY'),
    'monthly_quota_enabled' => env('AI_MONTHLY_QUOTA_ENABLED', true),
    'store_prompt' => env('AI_STORE_PROMPT', false),
    'store_response' => env('AI_STORE_RESPONSE', true),
    'timeout_seconds' => env('AI_TIMEOUT_SECONDS', 60),
];

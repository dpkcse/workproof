<?php

namespace App\Services\AI;

use App\Data\AI\AiRequest;
use App\Data\AI\AiResponse;
use App\Models\AiSummary;
use App\Models\AiUsageLog;
use App\Models\Workspace;
use App\Services\Feature\FeatureService;
use Illuminate\Support\Carbon;

class AiService
{
    public function __construct(private FeatureService $features) {}

    public function generate(Workspace $workspace, string $feature, string $prompt, array $context = [], array $options = []): AiResponse
    {
        $userId = $options['user_id'] ?? auth()->id();
        $providerKey = $options['provider'] ?? ($workspace->settings?->feature_flags['ai_preferred_provider'] ?? config('ai.default_provider', 'disabled'));
        $sourceIds = $options['source_ids'] ?? [];
        $sourceType = $options['source_type'] ?? null;

        if (! $workspace->settings?->ai_enabled || $providerKey === 'disabled') {
            return $this->blocked($workspace, $userId, $feature, 'AI is disabled for this workspace.', $sourceType, $sourceIds);
        }
        if (! $this->features->enabled('ai_enabled', $workspace)) {
            return $this->blocked($workspace, $userId, $feature, 'AI is not available on this plan.', $sourceType, $sourceIds);
        }
        if (in_array($providerKey, ['cloud', 'hybrid'], true) && ! $this->quotaAllowsCloud($workspace)) {
            return $this->blocked($workspace, $userId, $feature, 'Monthly cloud AI quota exceeded.', $sourceType, $sourceIds);
        }

        $provider = $this->provider($providerKey, $workspace);
        $request = new AiRequest($workspace->id, $userId, $feature, $prompt, $context, $options['max_tokens'] ?? 700, $options['temperature'] ?? 0.2, $sourceType, $sourceIds);
        $response = $provider->generate($request);
        $this->log($workspace, $userId, $feature, $response->success ? 'success' : 'failed', $response, $sourceType, $sourceIds, $prompt);
        return $response->success ? $response : AiResponse::failed($response->error_message ?: 'AI generation failed safely.', $response->provider, $response->model);
    }

    public function saveSummary(Workspace $workspace, string $type, string $content, array $attributes = []): AiSummary
    {
        return AiSummary::query()->create(array_merge($attributes, [
            'workspace_id' => $workspace->id,
            'summary_type' => $type,
            'content' => $content,
            'status' => 'generated',
            'generated_at' => now(),
        ]));
    }

    private function provider(string $key, Workspace $workspace): AiProviderInterface
    {
        if ($key === 'local') return new LocalOllamaProvider();
        if ($key === 'cloud') return new CloudOpenAiProvider();
        if ($key === 'hybrid') return $this->features->enabled('local_ai', $workspace) ? new LocalOllamaProvider() : new CloudOpenAiProvider();
        return new NullAiProvider();
    }

    private function quotaAllowsCloud(Workspace $workspace): bool
    {
        if (! config('ai.monthly_quota_enabled', true)) return true;
        $limit = $this->features->limit('ai_monthly_quota', $workspace);
        if ($limit === null || $limit === '' || (int) $limit <= 0) return true;
        $used = AiUsageLog::withoutWorkspaceScope()->where('workspace_id', $workspace->id)->where('status', 'success')->where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        return $used < (int) $limit;
    }

    private function blocked(Workspace $workspace, ?int $userId, string $feature, string $message, ?string $sourceType, array $sourceIds): AiResponse
    {
        $response = AiResponse::failed($message);
        $this->log($workspace, $userId, $feature, 'blocked', $response, $sourceType, $sourceIds, null);
        return $response;
    }

    private function log(Workspace $workspace, ?int $userId, string $feature, string $status, AiResponse $response, ?string $sourceType, array $sourceIds, ?string $prompt): void
    {
        AiUsageLog::query()->create([
            'workspace_id' => $workspace->id,'user_id' => $userId,'feature' => $feature,'provider' => $response->provider,'model' => $response->model,
            'input_tokens' => $response->input_tokens,'output_tokens' => $response->output_tokens,'estimated_cost' => $response->estimated_cost,'status' => $status,
            'error_message' => $response->error_message,'source_type' => $sourceType,'source_id' => $sourceIds[0] ?? null,'created_at' => now(),
            'metadata' => array_filter(['source_ids' => $sourceIds, 'prompt' => config('ai.store_prompt') ? $prompt : null, 'response' => config('ai.store_response') ? $response->text : null]),
        ]);
    }
}

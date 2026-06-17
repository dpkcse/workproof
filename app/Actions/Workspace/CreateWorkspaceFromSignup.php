<?php

namespace App\Actions\Workspace;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceDomain;
use App\Models\WorkspaceOnboardingStep;
use App\Notifications\WorkspaceWelcomeNotification;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CreateWorkspaceFromSignup
{
    public const ONBOARDING_STEPS = [
        'company_profile',
        'departments_teams',
        'invite_users',
        'work_policy',
        'ai_settings',
        'complete',
    ];

    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function handle(array $input): array
    {
        return DB::transaction(function () use ($input): array {
            $trialEndsAt = now()->addDays((int) config('workproof.defaults.trial_days', 14))->toDateString();

            $user = User::query()->create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'status' => 'active',
                'default_timezone' => config('workproof.defaults.timezone', 'Asia/Dhaka'),
            ]);

            $workspace = Workspace::query()->create([
                'name' => $input['company_name'],
                'slug' => $input['workspace_slug'],
                'status' => config('workproof.defaults.workspace_status', 'trial'),
                'owner_id' => $user->id,
                'edition' => config('workproof.edition', 'saas'),
                'timezone' => config('workproof.defaults.timezone', 'Asia/Dhaka'),
                'currency' => config('workproof.defaults.currency', 'BDT'),
                'trial_ends_at' => $trialEndsAt,
            ]);

            $mainDomain = config('workproof.domains.main');

            if ($mainDomain) {
                WorkspaceDomain::query()->create([
                    'workspace_id' => $workspace->id,
                    'domain' => $workspace->slug.'.'.$mainDomain,
                    'type' => 'subdomain',
                    'is_primary' => true,
                    'is_verified' => true,
                ]);
            } else {
                Log::warning('Workspace signup completed without creating a domain because APP_MAIN_DOMAIN is missing.', [
                    'workspace_id' => $workspace->id,
                ]);
            }

            $workspace->users()->attach($user->id, [
                'role_key' => 'workspace_owner',
                'status' => 'active',
                'joined_at' => now(),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('workspace_owner');
            }

            $workspace->settings()->create([
                'workspace_id' => $workspace->id,
                'company_name' => $input['company_name'],
                'timezone' => config('workproof.defaults.timezone', 'Asia/Dhaka'),
                'currency' => config('workproof.defaults.currency', 'BDT'),
                'daily_report_required' => true,
                'proof_required_by_default' => false,
                'ai_enabled' => false,
            ]);

            foreach (self::ONBOARDING_STEPS as $step) {
                WorkspaceOnboardingStep::query()->create([
                    'workspace_id' => $workspace->id,
                    'step_key' => $step,
                ]);
            }

            $starterPlan = Plan::query()->where('slug', 'starter')->first();

            if (! $starterPlan) {
                Log::warning('Starter plan was not found during workspace signup.', [
                    'workspace_id' => $workspace->id,
                ]);
            }

            $subscription = Subscription::query()->create([
                'workspace_id' => $workspace->id,
                'plan_id' => $starterPlan?->id,
                'status' => 'trial',
                'trial_ends_at' => $trialEndsAt,
                'current_period_start' => now()->toDateString(),
                'current_period_end' => $trialEndsAt,
                'billing_cycle' => 'monthly',
            ]);

            $user->notify(new WorkspaceWelcomeNotification($workspace));

            $this->auditLogService->log('owner_created', $user, [], ['email' => $user->email], $workspace->id);
            $this->auditLogService->log('workspace_created', $workspace, [], ['slug' => $workspace->slug], $workspace->id);
            $this->auditLogService->log('subscription_created', $subscription, [], ['status' => $subscription->status], $workspace->id);

            return ['user' => $user, 'workspace' => $workspace, 'subscription' => $subscription];
        });
    }
}

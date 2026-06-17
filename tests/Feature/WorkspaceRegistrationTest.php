<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_a_workspace_with_foundation_records(): void
    {
        Plan::query()->create([
            'name' => 'Starter',
            'slug' => 'starter',
            'monthly_price' => 19,
            'currency' => 'USD',
            'user_limit' => 5,
            'project_limit' => 3,
            'storage_limit_mb' => 500,
            'is_public' => true,
            'is_active' => true,
        ]);

        $response = $this->post('/register', $this->validPayload());

        $workspace = Workspace::query()->where('slug', 'acme-team')->firstOrFail();
        $owner = $workspace->owner;

        $response->assertRedirect('/onboarding');
        $this->assertAuthenticatedAs($owner);
        $this->assertDatabaseHas('workspace_user', [
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'role_key' => 'workspace_owner',
            'status' => 'active',
        ]);
        $this->assertNotNull($workspace->settings);
        $this->assertNotNull($workspace->subscription);
        $this->assertSame('trial', $workspace->subscription->status);
        $this->assertCount(6, $workspace->onboardingSteps);
        $this->assertDatabaseHas('workspace_domains', [
            'workspace_id' => $workspace->id,
            'domain' => 'acme-team.'.config('workproof.domains.main'),
            'is_primary' => true,
            'is_verified' => true,
        ]);
        $this->assertDatabaseHas('audit_logs', ['workspace_id' => $workspace->id, 'action' => 'workspace_created']);
    }

    public function test_owner_can_access_onboarding(): void
    {
        $this->post('/register', $this->validPayload());

        $this->get('/onboarding')->assertOk()->assertSee('Set up Acme Team');
    }

    public function test_owner_can_complete_company_profile_step(): void
    {
        $this->post('/register', $this->validPayload());

        $this->post('/onboarding/company-profile', [
            'company_name' => 'Acme Updated',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
        ])->assertRedirect();

        $workspace = Workspace::query()->where('slug', 'acme-team')->firstOrFail();
        $this->assertDatabaseHas('workspace_settings', ['workspace_id' => $workspace->id, 'company_name' => 'Acme Updated']);
        $this->assertDatabaseHas('workspace_onboarding_steps', ['workspace_id' => $workspace->id, 'step_key' => 'company_profile', 'is_completed' => true]);
    }

    public function test_owner_can_complete_work_policy_step(): void
    {
        $this->post('/register', $this->validPayload());

        $this->post('/onboarding/policies', [
            'daily_report_required' => true,
            'daily_report_deadline' => '18:00',
            'working_days' => ['mon', 'tue', 'wed', 'thu', 'fri'],
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
            'proof_required_by_default' => false,
        ])->assertRedirect();

        $workspace = Workspace::query()->where('slug', 'acme-team')->firstOrFail();
        $this->assertDatabaseHas('workspace_onboarding_steps', ['workspace_id' => $workspace->id, 'step_key' => 'work_policy', 'is_completed' => true]);
    }

    public function test_owner_can_complete_onboarding_and_reach_dashboard(): void
    {
        $this->post('/register', $this->validPayload());

        $this->post('/onboarding/complete')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk()->assertSee('Welcome to Acme Team');
    }

    public function test_duplicate_workspace_slug_is_rejected(): void
    {
        $this->post('/register', $this->validPayload());

        $this->post('/register', array_merge($this->validPayload('other@example.com'), [
            'workspace_slug' => 'acme-team',
        ]))->assertSessionHasErrors('workspace_slug');
    }

    public function test_reserved_workspace_slug_is_rejected(): void
    {
        $this->post('/register', array_merge($this->validPayload(), [
            'workspace_slug' => 'admin',
        ]))->assertSessionHasErrors('workspace_slug');
    }

    private function validPayload(string $email = 'owner@example.com'): array
    {
        return [
            'name' => 'Owner User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Acme Team',
            'workspace_slug' => 'acme-team',
        ];
    }
}

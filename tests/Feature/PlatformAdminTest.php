<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PlatformAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_can_view_dashboard_workspace_list_and_detail(): void
    {
        [$owner, $workspace] = $this->tenantWorkspace();
        $admin = $this->user(['email' => 'admin@example.com', 'is_platform_user' => true]);

        $this->actingAs($admin)->get('/platform/dashboard')->assertOk()->assertSee('Platform Dashboard');
        $this->actingAs($admin)->get('/platform/workspaces')->assertOk()->assertSee($workspace->name);
        $this->actingAs($admin)->get('/platform/workspaces/'.$workspace->id)->assertOk()->assertSee($owner->email);
    }

    public function test_tenant_user_cannot_access_platform_dashboard(): void
    {
        [$owner] = $this->tenantWorkspace();

        $this->actingAs($owner)->get('/platform/dashboard')->assertForbidden();
    }

    public function test_platform_admin_can_suspend_and_reactivate_workspace(): void
    {
        [$owner, $workspace] = $this->tenantWorkspace();
        $admin = $this->user(['email' => 'admin@example.com', 'is_platform_user' => true]);

        $this->actingAs($admin)->post('/platform/workspaces/'.$workspace->id.'/suspend', [
            'suspension_reason' => 'Manual QA suspension',
        ])->assertRedirect();

        $workspace->refresh();
        $this->assertSame('suspended', $workspace->status);
        $this->assertDatabaseHas('audit_logs', ['workspace_id' => $workspace->id, 'action' => 'workspace_suspended']);

        $this->withSession(['current_workspace_id' => $workspace->id])
            ->actingAs($owner)
            ->get('/dashboard')
            ->assertForbidden();

        $this->actingAs($admin)->post('/platform/workspaces/'.$workspace->id.'/reactivate')->assertRedirect();

        $workspace->refresh();
        $this->assertContains($workspace->status, ['trial', 'active']);
        $this->assertDatabaseHas('audit_logs', ['workspace_id' => $workspace->id, 'action' => 'workspace_reactivated']);

        $this->withSession(['current_workspace_id' => $workspace->id])
            ->actingAs($owner)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_platform_admin_can_create_plan_and_pricing_displays_public_plans(): void
    {
        $admin = $this->user(['email' => 'admin@example.com', 'is_platform_user' => true]);

        $this->actingAs($admin)->post('/platform/plans', [
            'name' => 'Growth',
            'slug' => 'growth',
            'monthly_price' => 79,
            'yearly_price' => 790,
            'currency' => 'USD',
            'user_limit' => 25,
            'project_limit' => 50,
            'storage_limit_mb' => 10240,
            'ai_monthly_quota' => 2000,
            'is_public' => true,
            'is_active' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('plans', ['slug' => 'growth', 'is_public' => true, 'is_active' => true]);
        $this->get('/pricing')->assertOk()->assertSee('Growth');
    }

    private function tenantWorkspace(): array
    {
        $owner = $this->user(['email' => 'owner@example.com']);
        $workspace = Workspace::query()->create([
            'name' => 'Acme Workspace',
            'slug' => 'acme-workspace',
            'status' => 'trial',
            'owner_id' => $owner->id,
            'edition' => 'saas',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
            'trial_ends_at' => now()->addDays(10)->toDateString(),
        ]);
        $workspace->users()->attach($owner->id, ['role_key' => 'workspace_owner', 'status' => 'active', 'joined_at' => now()]);
        $workspace->settings()->create(['workspace_id' => $workspace->id, 'company_name' => $workspace->name, 'timezone' => 'Asia/Dhaka', 'currency' => 'BDT']);
        Subscription::query()->create(['workspace_id' => $workspace->id, 'status' => 'trial', 'trial_ends_at' => now()->addDays(10)->toDateString(), 'billing_cycle' => 'monthly']);

        return [$owner, $workspace];
    }

    private function user(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Test User',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'status' => 'active',
            'is_platform_user' => false,
        ], $attributes));
    }
}

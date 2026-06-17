<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WorkspaceSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_route_fails_without_workspace_context(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertNotFound();
    }

    public function test_workspace_a_user_cannot_access_workspace_b(): void
    {
        $user = $this->user();
        $workspaceA = $this->workspace('Workspace A', 'workspace-a');
        $workspaceB = $this->workspace('Workspace B', 'workspace-b');

        $workspaceA->users()->attach($user->id, ['status' => 'active']);

        $this->withSession(['current_workspace_id' => $workspaceB->id])
            ->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_suspended_workspace_cannot_access_dashboard(): void
    {
        $user = $this->user();
        $workspace = $this->workspace('Suspended Workspace', 'suspended-workspace', 'suspended');
        $workspace->users()->attach($user->id, ['status' => 'active']);

        $this->withSession(['current_workspace_id' => $workspace->id])
            ->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden()
            ->assertSee('Workspace is currently suspended');
    }

    public function test_platform_admin_can_access_platform_dashboard(): void
    {
        $admin = $this->user(['is_platform_user' => true]);

        $this->actingAs($admin)
            ->get('/platform/dashboard')
            ->assertOk()
            ->assertSee('Platform Dashboard');
    }

    public function test_normal_tenant_user_cannot_access_platform_dashboard(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->get('/platform/dashboard')
            ->assertForbidden();
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

    private function workspace(string $name, string $slug, string $status = 'active'): Workspace
    {
        return Workspace::query()->create([
            'name' => $name,
            'slug' => $slug,
            'status' => $status,
            'edition' => 'saas',
            'timezone' => 'Asia/Dhaka',
            'currency' => 'BDT',
        ]);
    }
}

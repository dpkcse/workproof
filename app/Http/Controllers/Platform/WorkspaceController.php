<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Notifications\WorkspaceReactivatedNotification;
use App\Notifications\WorkspaceSuspendedNotification;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(Request $request): View
    {
        $workspaces = Workspace::query()
            ->with(['owner', 'subscription.plan'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('owner', fn ($owner) => $owner->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('edition'), fn ($query) => $query->where('edition', $request->string('edition')->toString()))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('platform.workspaces.index', ['workspaces' => $workspaces]);
    }

    public function show(Workspace $workspace): View
    {
        $workspace->loadMissing(['owner', 'subscription.plan', 'onboardingSteps']);

        return view('platform.workspaces.show', [
            'workspace' => $workspace,
            'usersCount' => $workspace->users()->count(),
            'departmentsCount' => \App\Models\Department::query()->withoutWorkspaceScope()->where('workspace_id', $workspace->id)->count(),
            'teamsCount' => \App\Models\Team::query()->withoutWorkspaceScope()->where('workspace_id', $workspace->id)->count(),
            'auditLogs' => \App\Models\AuditLog::query()->withoutWorkspaceScope()->where('workspace_id', $workspace->id)->latest()->limit(15)->get(),
        ]);
    }

    public function suspend(Request $request, Workspace $workspace, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorizePlatform($request, 'platform.workspaces.suspend');

        $validated = $request->validate([
            'suspension_reason' => ['required', 'string', 'max:1000'],
        ]);

        $oldValues = $workspace->only(['status', 'suspended_at', 'suspended_by', 'suspension_reason']);

        $workspace->forceFill([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspended_by' => $request->user()->id,
            'suspension_reason' => $validated['suspension_reason'],
        ])->save();

        $auditLogService->log('workspace_suspended', $workspace, $oldValues, $workspace->fresh()->only(['status', 'suspended_at', 'suspended_by', 'suspension_reason']), $workspace->id);
        $workspace->owner?->notify(new WorkspaceSuspendedNotification($workspace, $validated['suspension_reason']));

        return back()->with('status', 'Workspace suspended successfully.');
    }

    public function reactivate(Request $request, Workspace $workspace, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorizePlatform($request, 'platform.workspaces.reactivate');

        $oldValues = $workspace->only(['status', 'suspended_at', 'suspended_by', 'suspension_reason']);
        $subscription = $workspace->subscription;
        $status = 'active';

        if ($subscription?->status === 'trial' && $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
            $status = 'trial';
        } elseif ($subscription?->status === 'active') {
            $status = 'active';
        }

        $workspace->forceFill([
            'status' => $status,
            'suspended_at' => null,
            'suspended_by' => null,
            'suspension_reason' => null,
        ])->save();

        $auditLogService->log('workspace_reactivated', $workspace, $oldValues, $workspace->fresh()->only(['status', 'suspended_at', 'suspended_by', 'suspension_reason']), $workspace->id);
        $workspace->owner?->notify(new WorkspaceReactivatedNotification($workspace));

        return back()->with('status', 'Workspace reactivated successfully.');
    }

    private function authorizePlatform(Request $request, string $permission): void
    {
        $user = $request->user();

        if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($permission)) {
            return;
        }

        if ($user?->isPlatformUser()) {
            return;
        }

        abort(403, 'Missing required platform permission.');
    }
}

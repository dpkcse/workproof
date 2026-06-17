<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use App\Notifications\UserInvitedToWorkspaceNotification;
use App\Services\Audit\AuditLogService;
use App\Support\CurrentWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(CurrentWorkspace $currentWorkspace): View
    {
        $workspace = $currentWorkspace->required()->loadMissing(['settings', 'onboardingSteps']);

        return view('onboarding.index', ['workspace' => $workspace]);
    }

    public function companyProfile(Request $request, CurrentWorkspace $currentWorkspace): RedirectResponse
    {
        $workspace = $currentWorkspace->required();
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'timezone' => ['required', 'string', 'max:100'],
            'currency' => ['required', 'string', 'size:3'],
            'logo' => ['nullable', 'file', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('workspace-logos');
        }

        unset($validated['logo']);
        $workspace->settings()->updateOrCreate(['workspace_id' => $workspace->id], $validated);
        $this->completeStep($workspace, 'company_profile');

        return back()->with('status', 'Company profile saved.');
    }

    public function departments(Request $request, CurrentWorkspace $currentWorkspace): RedirectResponse
    {
        $workspace = $currentWorkspace->required();
        $validated = $request->validate([
            'department_name' => ['required', 'string', 'max:150'],
            'team_name' => ['nullable', 'string', 'max:150'],
        ]);

        $department = Department::query()->create([
            'workspace_id' => $workspace->id,
            'name' => $validated['department_name'],
            'is_active' => true,
        ]);

        if (! empty($validated['team_name'])) {
            Team::query()->create([
                'workspace_id' => $workspace->id,
                'department_id' => $department->id,
                'name' => $validated['team_name'],
                'is_active' => true,
            ]);
        }

        $this->completeStep($workspace, 'departments_teams');

        return back()->with('status', 'Department and team setup saved.');
    }

    public function inviteUsers(Request $request, CurrentWorkspace $currentWorkspace): RedirectResponse
    {
        $workspace = $currentWorkspace->required();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:150'],
            'team' => ['nullable', 'string', 'max:150'],
            'reporting_manager' => ['nullable', 'string', 'max:150'],
        ]);

        $user = User::query()->firstOrCreate(
            ['email' => $validated['email']],
            ['name' => $validated['name'], 'password' => Hash::make(str()->password(32)), 'status' => 'invited']
        );

        $workspace->users()->syncWithoutDetaching([
            $user->id => [
                'role_key' => $validated['role'],
                'status' => 'invited',
                'invited_by' => $request->user()?->id,
            ],
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole($validated['role']);
        }

        $user->notify(new UserInvitedToWorkspaceNotification($workspace, $validated['role']));
        $this->completeStep($workspace, 'invite_users');

        return back()->with('status', 'User invitation recorded. Secure invitation tokens will be added later.');
    }

    public function policies(Request $request, CurrentWorkspace $currentWorkspace): RedirectResponse
    {
        $workspace = $currentWorkspace->required();
        $validated = $request->validate([
            'daily_report_required' => ['required', 'boolean'],
            'daily_report_deadline' => ['nullable', 'date_format:H:i'],
            'working_days' => ['nullable', 'array'],
            'office_start_time' => ['nullable', 'date_format:H:i'],
            'office_end_time' => ['nullable', 'date_format:H:i'],
            'proof_required_by_default' => ['required', 'boolean'],
        ]);

        $workspace->settings()->updateOrCreate(['workspace_id' => $workspace->id], $validated);
        $this->completeStep($workspace, 'work_policy');

        return back()->with('status', 'Work policy saved.');
    }

    public function aiSettings(Request $request, CurrentWorkspace $currentWorkspace): RedirectResponse
    {
        $workspace = $currentWorkspace->required();
        $validated = $request->validate(['ai_enabled' => ['required', 'boolean']]);

        $workspace->settings()->updateOrCreate(['workspace_id' => $workspace->id], $validated);
        $this->completeStep($workspace, 'ai_settings');

        return back()->with('status', 'AI setting saved. Processing will be implemented later.');
    }

    public function complete(CurrentWorkspace $currentWorkspace, AuditLogService $auditLogService): RedirectResponse
    {
        $workspace = $currentWorkspace->required();

        $workspace->onboardingSteps()->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        $auditLogService->log('onboarding_completed', $workspace, [], ['completed' => true], $workspace->id);

        return redirect()->route('tenant.dashboard');
    }

    private function completeStep($workspace, string $stepKey): void
    {
        $workspace->onboardingSteps()->where('step_key', $stepKey)->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);
    }
}

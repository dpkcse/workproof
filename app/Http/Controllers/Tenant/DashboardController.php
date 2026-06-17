<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Support\CurrentWorkspace;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(CurrentWorkspace $currentWorkspace): View
    {
        $workspace = $currentWorkspace->required()->loadMissing([
            'subscription.plan',
            'onboardingSteps',
        ]);

        $totalSteps = $workspace->onboardingSteps->count();
        $completedSteps = $workspace->onboardingSteps->where('is_completed', true)->count();

        return view('tenant.dashboard', [
            'workspace' => $workspace,
            'subscription' => $workspace->subscription,
            'usersCount' => $workspace->users()->count(),
            'departmentsCount' => \App\Models\Department::query()->forWorkspace($workspace->id)->count(),
            'teamsCount' => \App\Models\Team::query()->forWorkspace($workspace->id)->count(),
            'onboardingPercent' => $totalSteps > 0 ? (int) round(($completedSteps / $totalSteps) * 100) : 0,
        ]);
    }
}

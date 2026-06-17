<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('platform.dashboard', [
            'totalWorkspaces' => Workspace::query()->count(),
            'trialWorkspaces' => Workspace::query()->where('status', 'trial')->count(),
            'activeWorkspaces' => Workspace::query()->where('status', 'active')->count(),
            'suspendedWorkspaces' => Workspace::query()->where('status', 'suspended')->count(),
            'totalUsers' => User::query()->count(),
            'recentWorkspaces' => Workspace::query()->with('owner')->latest()->limit(8)->get(),
            'subscriptionsByStatus' => Subscription::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'totalPublicPlans' => Plan::query()->where('is_public', true)->count(),
        ]);
    }
}

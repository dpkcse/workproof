<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('platform.plans.index', [
            'plans' => Plan::query()->latest()->paginate(20),
        ]);
    }

    public function store(Request $request, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorizePlatform($request, 'platform.plans.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:100', Rule::unique('plans', 'slug')],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'yearly_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'user_limit' => ['nullable', 'integer', 'min:0'],
            'project_limit' => ['nullable', 'integer', 'min:0'],
            'storage_limit_mb' => ['nullable', 'integer', 'min:0'],
            'ai_monthly_quota' => ['nullable', 'integer', 'min:0'],
            'is_public' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_public'] = (bool) ($validated['is_public'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $plan = Plan::query()->create($validated);
        $auditLogService->log('plan_created', $plan, [], $plan->toArray());

        return back()->with('status', 'Plan created successfully.');
    }

    private function authorizePlatform(Request $request, string $permission): void
    {
        $user = $request->user();

        if ((method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($permission)) || $user?->isPlatformUser()) {
            return;
        }

        abort(403, 'Missing required platform permission.');
    }
}

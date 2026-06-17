<?php

namespace App\Http\Middleware;

use App\Support\CurrentWorkspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantMember
{
    public function __construct(private readonly CurrentWorkspace $currentWorkspace)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $workspace = $this->currentWorkspace->required();
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($this->hasAuditedImpersonation($request)) {
            return $next($request);
        }

        if (! $user->activeWorkspaceMembership($workspace->id)) {
            abort(403, 'You do not have active access to this workspace.');
        }

        return $next($request);
    }

    private function hasAuditedImpersonation(Request $request): bool
    {
        return (bool) $request->session()->get('platform_impersonating_workspace_user')
            && (bool) $request->session()->get('platform_impersonation_audit_id');
    }
}

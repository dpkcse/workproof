<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use App\Models\WorkspaceDomain;
use App\Support\CurrentWorkspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCurrentWorkspace
{
    public function __construct(private readonly CurrentWorkspace $currentWorkspace)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->currentWorkspace->clear();

        if ($this->isPlatformHost($request->getHost())) {
            return $next($request);
        }

        $workspace = $this->resolveFromHost($request);

        if (! $workspace && $this->isTenantHost($request->getHost())) {
            abort(404, 'Workspace could not be resolved for this tenant subdomain.');
        }

        $workspace ??= $this->resolveFromSession($request)
            ?? $this->resolvePrivateOrEnterpriseDefault();

        if (! $workspace) {
            abort(404, 'Workspace could not be resolved for this request.');
        }

        $this->currentWorkspace->set($workspace);
        $request->attributes->set('current_workspace', $workspace);

        return $next($request);
    }

    private function resolveFromHost(Request $request): ?Workspace
    {
        $host = strtolower($request->getHost());
        $subdomainRoot = strtolower((string) config('workproof.domains.tenant_suffix', config('workproof.domains.subdomain_root')));
        $mainDomain = strtolower((string) config('workproof.domains.main'));

        if ($host === $subdomainRoot || $host === $mainDomain) {
            return null;
        }

        $domain = WorkspaceDomain::query()
            ->with('workspace')
            ->where('domain', $host)
            ->first();

        if ($domain?->workspace) {
            return $domain->workspace;
        }

        if ($subdomainRoot && str_ends_with($host, '.'.$subdomainRoot)) {
            $slug = str($host)->before('.'.$subdomainRoot)->toString();

            return Workspace::query()->where('slug', $slug)->first();
        }

        return null;
    }

    private function resolveFromSession(Request $request): ?Workspace
    {
        $workspaceId = $request->session()->get('current_workspace_id');

        if (! $workspaceId) {
            return null;
        }

        return Workspace::query()->find($workspaceId);
    }

    private function resolvePrivateOrEnterpriseDefault(): ?Workspace
    {
        if (! in_array(config('workproof.edition'), ['private', 'enterprise'], true)) {
            return null;
        }

        return Workspace::query()
            ->whereIn('status', ['trial', 'active'])
            ->oldest('id')
            ->first()
            ?? Workspace::query()->find(1);
    }

    private function isPlatformHost(string $host): bool
    {
        return strtolower($host) === strtolower((string) config('workproof.domains.admin'));
    }

    private function isTenantHost(string $host): bool
    {
        $host = strtolower($host);
        $tenantSuffix = strtolower((string) config('workproof.domains.tenant_suffix', config('workproof.domains.subdomain_root')));

        return $tenantSuffix !== ''
            && str_ends_with($host, '.'.$tenantSuffix)
            && $host !== strtolower((string) config('workproof.domains.admin'));
    }
}

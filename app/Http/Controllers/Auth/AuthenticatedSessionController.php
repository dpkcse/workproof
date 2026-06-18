<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectAuthenticatedUser($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $loginUrl = $this->urlForCurrentHost($request, '/login');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($loginUrl)->with('status', 'You have been logged out.');
    }

    private function redirectAuthenticatedUser(Request $request): RedirectResponse
    {
        $user = $request->user();
        $host = strtolower($request->getHost());

        if ($user?->isPlatformUser()) {
            return redirect()->intended($this->isAdminHost($host) ? $this->urlForCurrentHost($request, '/dashboard') : $this->absoluteUrl(config('workproof.domains.admin'), '/dashboard', $request));
        }

        $workspace = $this->workspaceForRequest($request);

        if (! $workspace instanceof Workspace) {
            return redirect($this->urlForMainHost('/workspace/setup', $request))
                ->with('status', 'Please finish setting up your workspace to continue.');
        }

        $request->session()->put('current_workspace_id', $workspace->id);

        return redirect()->intended($this->workspaceUrl($workspace, '/dashboard', $request));
    }

    private function workspaceForRequest(Request $request): ?Workspace
    {
        $host = strtolower($request->getHost());
        $tenantSuffix = strtolower((string) config('workproof.domains.tenant_suffix'));

        if ($tenantSuffix && str_ends_with($host, '.'.$tenantSuffix)) {
            $slug = str($host)->before('.'.$tenantSuffix)->toString();
            $workspace = $request->user()?->workspaces()->where('slug', $slug)->first();

            if ($workspace instanceof Workspace) {
                return $workspace;
            }
        }

        return $request->user()?->workspaces()
            ->orderByRaw("CASE WHEN workspace_user.status = 'active' THEN 0 ELSE 1 END")
            ->oldest('workspaces.id')
            ->first();
    }

    private function isAdminHost(string $host): bool
    {
        return $host === strtolower((string) config('workproof.domains.admin'));
    }

    private function workspaceUrl(Workspace $workspace, string $path, Request $request): string
    {
        return $this->absoluteUrl($workspace->slug.'.'.config('workproof.domains.tenant_suffix'), $path, $request);
    }

    private function urlForMainHost(string $path, Request $request): string
    {
        return $this->absoluteUrl(config('workproof.domains.main'), $path, $request);
    }

    private function urlForCurrentHost(Request $request, string $path): string
    {
        return $this->absoluteUrl($request->getHost(), $path, $request);
    }

    private function absoluteUrl(?string $host, string $path, Request $request): string
    {
        $port = $request->getPort();
        $portSuffix = in_array($port, [80, 443], true) ? '' : ':'.$port;

        return $request->getScheme().'://'.$host.$portSuffix.$path;
    }
}

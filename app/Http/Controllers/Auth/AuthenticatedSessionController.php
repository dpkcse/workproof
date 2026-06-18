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
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }

    private function redirectAuthenticatedUser(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->isPlatformUser()) {
            return redirect()->intended(route('platform.dashboard'));
        }

        $workspace = $user?->workspaces()
            ->orderByRaw("CASE WHEN workspace_user.status = 'active' THEN 0 ELSE 1 END")
            ->oldest('workspaces.id')
            ->first();

        if (! $workspace instanceof Workspace) {
            return redirect()->route('workspace.setup')
                ->with('status', 'Please finish setting up your workspace to continue.');
        }

        $request->session()->put('current_workspace_id', $workspace->id);

        if (in_array($workspace->status, ['suspended', 'cancelled', 'expired'], true)) {
            return redirect()->route('tenant.dashboard');
        }

        return redirect()->intended(route('tenant.dashboard'));
    }
}

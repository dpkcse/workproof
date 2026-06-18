<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Workspace\CreateWorkspaceFromSignup;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredWorkspaceController extends Controller
{
    private const RESERVED_SLUGS = [
        'admin', 'app', 'api', 'www', 'mail', 'support', 'billing', 'platform',
        'login', 'register', 'dashboard', 'assets', 'static',
    ];

    public function create(): View
    {
        return view('auth.register', ['reservedSlugs' => self::RESERVED_SLUGS]);
    }

    public function store(Request $request, CreateWorkspaceFromSignup $action): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'company_name' => ['required', 'string', 'max:150'],
            'workspace_slug' => [
                'required', 'string', 'lowercase', 'alpha_dash', 'min:3', 'max:50',
                'unique:workspaces,slug', Rule::notIn(self::RESERVED_SLUGS),
            ],
        ]);

        $result = $action->handle($validated);

        Auth::login($result['user']);

        $request->session()->regenerate();
        $request->session()->put('current_workspace_id', $result['workspace']->id);

        $host = $result['workspace']->slug.'.'.config('workproof.domains.tenant_suffix');
        $port = $request->getPort();
        $portSuffix = in_array($port, [80, 443], true) ? '' : ':'.$port;

        return redirect($request->getScheme().'://'.$host.$portSuffix.'/onboarding');
    }
}

<?php

namespace App\Http\Middleware;

use App\Support\CurrentWorkspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceActive
{
    public function __construct(private readonly CurrentWorkspace $currentWorkspace)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $workspace = $this->currentWorkspace->required();

        if (in_array($workspace->status, ['suspended', 'cancelled', 'expired'], true)) {
            return response()->view('errors.workspace-suspended', ['workspace' => $workspace], 403);
        }

        return $next($request);
    }
}

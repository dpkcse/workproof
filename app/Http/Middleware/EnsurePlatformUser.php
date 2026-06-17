<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $hasPlatformRole = method_exists($user, 'hasRole') && $user->hasRole('platform_admin');

        if (! $user->isPlatformUser() && ! $hasPlatformRole) {
            abort(403, 'Platform access is restricted.');
        }

        return $next($request);
    }
}

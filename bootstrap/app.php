<?php

use App\Http\Middleware\EnsurePlatformUser;
use App\Http\Middleware\EnsureSaasEnabled;
use App\Http\Middleware\EnsureTenantMember;
use App\Http\Middleware\EnsureWorkspaceActive;
use App\Http\Middleware\ResolveCurrentWorkspace;
use App\Support\CurrentWorkspace;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'resolve.current.workspace' => ResolveCurrentWorkspace::class,
            'ensure.workspace.active' => EnsureWorkspaceActive::class,
            'ensure.tenant.member' => EnsureTenantMember::class,
            'ensure.platform.user' => EnsurePlatformUser::class,
            'ensure.saas.enabled' => EnsureSaasEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->registered(function (Application $app): void {
        $app->scoped(CurrentWorkspace::class, fn () => new CurrentWorkspace());
        $app->alias(CurrentWorkspace::class, 'CurrentWorkspace');
    })
    ->create();

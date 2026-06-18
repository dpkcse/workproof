<?php
namespace App\Http\Middleware;use Closure;use Illuminate\Http\Request;
class EnsureSaasEnabled{public function handle(Request $request,Closure $next){abort_unless(filter_var(config('workproof.saas_enabled'),FILTER_VALIDATE_BOOL),404);return $next($request);}}

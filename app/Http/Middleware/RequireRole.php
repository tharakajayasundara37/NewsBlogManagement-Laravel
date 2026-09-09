<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, $roles, true), 403);
        return $next($request);
    }
}

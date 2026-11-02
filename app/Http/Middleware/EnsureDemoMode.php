<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoMode
{
    /**
     * Handle an incoming request. Returns 404 when demo is disabled.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! function_exists('demo_enabled') || ! demo_enabled()) {
            abort(404, 'Not Found');
        }

        return $next($request);
    }
}

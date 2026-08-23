<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyVideoToken
{
    /**
     * Handle an incoming request for secure video streaming.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasValidSignature()) {
            return response()->json([
                'success' => false,
                'message' => 'Security Token Expired or Invalid Signature.',
            ], 403);
        }

        return $next($request);
    }
}

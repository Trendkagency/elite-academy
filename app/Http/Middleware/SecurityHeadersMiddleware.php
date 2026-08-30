<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach standard security and caching headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Do not alter binary downloads or stream responses if headers cannot be modified
        if (! method_exists($response, 'header')) {
            return $response;
        }

        // 1. Content-Security-Policy
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.gstatic.com https://fonts.googleapis.com https://cdn.jsdelivr.net https://apis.google.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https://*.googleapis.com https://*.firebaseio.com wss://*.firebaseio.com https://*.google.com https://fonts.googleapis.com https://fonts.gstatic.com",
            "frame-src 'self' https://www.youtube-nocookie.com https://www.youtube.com https://player.vimeo.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp), false);

        // 2. Strict-Transport-Security (HSTS - 2 years)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        // 3. Clickjacking & MIME protections
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 4. Cross-Origin-Opener-Policy
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        // 5. Permissions-Policy (modern replacement for Feature-Policy)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        return $response;
    }
}

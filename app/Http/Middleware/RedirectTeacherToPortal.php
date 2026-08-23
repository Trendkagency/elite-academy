<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTeacherToPortal
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isTeacher()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Teachers are automatically directed to the Teacher Portal.'),
                    'redirect_url' => route('teacher-portal'),
                ], 200);
            }

            return redirect()->route('teacher-portal');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Authentication required.')], 401);
            }

            return redirect()->route('login')->with('error', __('Authentication required.'));
        }

        $statusVal = $user->status instanceof AccountStatus ? $user->status->value : (string) $user->status;
        if ($statusVal !== AccountStatus::APPROVED->value && $statusVal !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Account pending approval.')], 403);
            }

            auth()->logout();

            return redirect()->route('login')->with('error', __('app.auth.account_pending'));
        }

        if (! $user->isStudent() && ! $user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Unauthorized Access. Student role required.')], 403);
            }

            abort(403, __('Unauthorized Access. Student portal is restricted to enrolled students.'));
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParentRole
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

        if (! $user->isParent() && ! $user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Unauthorized Access. Parent role required.')], 403);
            }

            abort(403, __('Unauthorized Access. Parent portal is restricted to authorized parents.'));
        }

        return $next($request);
    }
}

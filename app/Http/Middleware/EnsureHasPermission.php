<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Authentication required.'),
                    'code'    => 'UNAUTHENTICATED',
                ], 401);
            }

            return redirect()->route('login')->with('error', __('Authentication required.'));
        }

        $statusVal = $user->status instanceof AccountStatus ? $user->status->value : (string) $user->status;
        if ($statusVal !== AccountStatus::APPROVED->value && $statusVal !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Account pending approval.'),
                    'code'    => 'ACCOUNT_PENDING',
                ], 403);
            }

            auth()->logout();

            return redirect()->route('login')->with('error', __('app.auth.account_pending'));
        }

        foreach ($permissions as $permission) {
            if (! $user->hasPermission($permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('You do not have permission to access this resource.'),
                        'code'    => 'FORBIDDEN',
                    ], 403);
                }

                abort(403, __('You do not have permission to access this resource.'));
            }
        }

        return $next($request);
    }
}

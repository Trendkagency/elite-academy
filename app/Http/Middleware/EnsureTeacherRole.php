<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            return redirect()->route('login')->with('error', __('Authentication required.'));
        }

        $statusVal = $user->status instanceof AccountStatus ? $user->status->value : (string) $user->status;
        if ($statusVal !== AccountStatus::APPROVED->value && $statusVal !== 'approved') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Account pending approval.'], 403);
            }

            auth()->logout();

            return redirect()->route('login')->with('error', __('app.auth.account_pending'));
        }

        if (! $user->isTeacher() && ! $user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized Access. Teacher role required.'], 403);
            }

            abort(403, __('Unauthorized Access. Teacher portal is restricted to active faculty members.'));
        }

        return $next($request);
    }
}

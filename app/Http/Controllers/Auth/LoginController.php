<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->status !== \App\Enums\AccountStatus::APPROVED) {
                auth()->logout();
            } else {
                if ($user->isAdmin()) {
                    return redirect('/admin');
                }
                if ($user->isTeacher()) {
                    return redirect()->route('teachers');
                }
                if ($user->isParent()) {
                    return redirect()->route('parent-portal');
                }

                return redirect()->route('student-portal');
            }
        }

        return view('pages.login', [
            'pageTitle' => 'Log In — Elite Academy',
            'activeNav' => 'login',
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! auth()->attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'success' => false,
                'message' => __('app.auth.invalid_credentials')
            ], 401);
        }

        $user = auth()->user();

        // Account status validation
        if ($user->status === \App\Enums\AccountStatus::SUSPENDED) {
            auth()->logout();
            return response()->json([
                'success' => false,
                'message' => __('app.auth.account_suspended')
            ], 403);
        }

        if ($user->status === \App\Enums\AccountStatus::PENDING) {
            auth()->logout();
            return response()->json([
                'success' => false,
                'message' => __('app.auth.account_pending')
            ], 403);
        }

        if ($user->status === \App\Enums\AccountStatus::REJECTED) {
            auth()->logout();
            return response()->json([
                'success' => false,
                'message' => __('app.auth.account_rejected')
            ], 403);
        }

        $redirectUrl = match (true) {
            $user->isAdmin() => url('/admin'),
            $user->isTeacher() => route('teachers'),
            $user->isParent() => route('parent-portal'),
            $user->isStudent() => route('student-portal'),
            default => route('student-portal'),
        };

        return response()->json([
            'success' => true,
            'message' => __('app.auth.login_success'),
            'redirect_url' => $redirectUrl,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => match (true) {
                    $user->isAdmin() => 'admin',
                    $user->isTeacher() => 'teacher',
                    $user->isParent() => 'parent',
                    default => 'student',
                }
            ]
        ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Logged out successfully']);
        }

        return redirect()->route('home');
    }
}

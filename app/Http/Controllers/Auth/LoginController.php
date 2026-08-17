<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isAdmin()) {
                return redirect('/admin');
            }
            if ($user->isParent()) {
                return redirect()->route('parent-portal');
            }

            return redirect()->route('student-portal');
        }

        return view('pages.login', [
            'pageTitle' => 'Log In — Elite Academy',
            'activeNav' => 'login',
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! auth()->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => __('app.auth.invalid_credentials')], 401);
        }

        $user = auth()->user();

        $redirectUrl = match (true) {
            $user->isAdmin() => url('/admin'),
            $user->isParent() => route('parent-portal'),
            default => route('student-portal'),
        };

        return response()->json([
            'success' => true,
            'message' => __('app.auth.login_success'),
            'redirect_url' => $redirectUrl,
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

<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\AdminProfile;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
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

        return view('pages.register', [
            'pageTitle' => 'Register — Elite Academy',
            'activeNav' => 'register',
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => $data['phone'] ?? null,
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);

        $userType = $data['user_type'] ?? 'student';
        match ($userType) {
            'teacher' => TeacherProfile::create(['user_id' => $user->id, 'slug' => Str::slug($user->name)]),
            'parent' => ParentProfile::create(['user_id' => $user->id]),
            'admin' => AdminProfile::create(['user_id' => $user->id]),
            default => StudentProfile::create(['user_id' => $user->id, 'grade_level_id' => $data['grade_level_id'] ?? null, 'school_name' => $data['school_name'] ?? null]),
        };

        auth()->login($user);

        $redirectUrl = match (true) {
            $user->isAdmin() => url('/admin'),
            $user->isTeacher() => route('teachers'),
            $user->isParent() => route('parent-portal'),
            $user->isStudent() => route('student-portal'),
            default => route('student-portal'),
        };

        return response()->json([
            'success' => true,
            'message' => __('app.auth.registered_success'),
            'redirect_url' => $redirectUrl,
        ], 201);
    }
}

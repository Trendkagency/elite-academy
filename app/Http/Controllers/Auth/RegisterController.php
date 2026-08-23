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

use App\Models\GradeLevel;
use App\Models\Subject;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->status !== AccountStatus::APPROVED) {
                auth()->logout();
            } else {
                if ($user->isAdmin()) {
                    return redirect('/admin');
                }
                if ($user->isTeacher()) {
                    return redirect()->route('teacher-portal');
                }
                if ($user->isParent()) {
                    return redirect()->route('parent-portal');
                }

                return redirect()->route('student-portal');
            }
        }

        $gradeLevels = GradeLevel::orderBy('sort_order')->get();
        $subjects    = Subject::with('category')->orderBy('sort_order')->get();
        $parents     = User::whereHas('parentProfile')->select('id', 'name', 'email')->get();

        return view('pages.register', [
            'pageTitle'   => 'Register — Elite Academy',
            'activeNav'   => 'register',
            'gradeLevels' => $gradeLevels,
            'subjects'    => $subjects,
            'parents'     => $parents,
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => bcrypt($data['password']),
            'phone'             => $data['phone'] ?? null,
            'status'            => AccountStatus::PENDING,
            'email_verified_at' => now(),
        ]);

        $userType   = $data['user_type'] ?? 'student';
        $subjectIds = $data['subject_ids'] ?? [];

        if ($userType === 'teacher') {
            $profile = TeacherProfile::create([
                'user_id' => $user->id,
                'slug'    => Str::slug($user->name) . '-' . rand(100, 999),
            ]);
            if (!empty($subjectIds)) {
                $profile->subjects()->sync($subjectIds);
            }
        } elseif ($userType === 'parent') {
            ParentProfile::create(['user_id' => $user->id]);
        } elseif ($userType === 'admin') {
            AdminProfile::create(['user_id' => $user->id]);
        } else {
            // Student (default)
            $profile = StudentProfile::create([
                'user_id'        => $user->id,
                'grade_level_id' => $data['grade_level_id'] ?? null,
                'school_name'    => $data['school_name'] ?? null,
            ]);

            if (!empty($subjectIds)) {
                $profile->subjects()->sync($subjectIds);

                // Auto-enroll in active courses matching student's grade & selected subjects
                $matchingCourses = \App\Models\Course::where('is_active', true)
                    ->whereIn('subject_id', $subjectIds)
                    ->when($data['grade_level_id'] ?? null, function ($q, $gid) {
                        $q->where('grade_level_id', $gid);
                    })
                    ->get();

                foreach ($matchingCourses as $course) {
                    \App\Models\CourseEnrollment::firstOrCreate([
                        'student_user_id' => $user->id,
                        'course_id'       => $course->id,
                    ], [
                        'status'      => 'active',
                        'enrolled_at' => now(),
                    ]);
                }
            }
        }

        return response()->json([
            'success'           => true,
            'message'           => __('app.auth.account_pending'),
            'redirect_url'      => route('login'),
            'requires_approval' => true,
        ], 201);
    }
}

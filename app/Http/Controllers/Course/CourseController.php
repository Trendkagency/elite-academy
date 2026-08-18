<?php

namespace App\Http\Controllers\Course;

use App\Enums\SessionProgressStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category');

        $courses = Course::where('is_active', true)
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $query->whereHas('subject', function ($q) use ($selectedCategory) {
                    $q->where('name', 'like', "%{$selectedCategory}%")
                      ->orWhereHas('category', function ($catQuery) use ($selectedCategory) {
                          $catQuery->where('name', 'like', "%{$selectedCategory}%");
                      });
                });
            })
            ->with(['subject.category', 'teacher.user', 'gradeLevel'])
            ->paginate(6)
            ->withQueryString();

        $enrolledCourseIds = auth()->check()
            ? CourseEnrollment::where('student_user_id', auth()->id())->pluck('course_id')->toArray()
            : [];

        return view('pages.courses', [
            'pageTitle' => 'Courses — Elite Academy',
            'activeNav' => 'courses',
            'courses' => $courses,
            'selectedCategory' => $selectedCategory,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }

    public function show(?string $slug = null): View
    {
        $course = Course::where('is_active', true)
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->with(['subject', 'teacher.user', 'sessions.assignments'])
            ->first();

        $isEnrolled = ($course && auth()->check())
            ? CourseEnrollment::where('student_user_id', auth()->id())->where('course_id', $course->id)->exists()
            : false;

        return view('pages.course-details', [
            'pageTitle' => $course ? $course->title : 'Course Details — Elite Academy',
            'activeNav' => 'courses',
            'course' => $course,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    public function enroll(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $activePackage = \App\Models\StudentPackage::where('student_user_id', $user->id)
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $activePackage) {
            return response()->json([
                'success' => false,
                'package_required' => true,
                'message' => app()->getLocale() === 'ar'
                    ? 'عذراً! يلزم الاشتراك في باقة حصص نشطة وتحتوي على رصيد للتسجيل والدخول للكورسات.'
                    : 'An active package subscription with available session credits is required to enroll in courses. Please subscribe to a package first.',
            ], 403);
        }

        $course = Course::find($id);
        if (! $course) {
            return response()->json(['success' => false, 'message' => 'Course not found'], 404);
        }

        $existing = CourseEnrollment::where('student_user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'already_enrolled' => true,
                'message' => 'You are already enrolled in this course! Access your lessons from your Student Portal.',
            ], 422);
        }

        $enrollment = CourseEnrollment::create([
            'student_user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $firstSession = CourseSession::where('course_id', $course->id)->orderBy('sort_order')->first();
        if ($firstSession) {
            CourseSessionProgress::firstOrCreate([
                'course_enrollment_id' => $enrollment->id,
                'course_session_id' => $firstSession->id,
            ], [
                'status' => SessionProgressStatus::UNLOCKED->value,
                'unlocked_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => app()->getLocale() === 'ar' ? 'تم التسجيل في الكورس بنجاح!' : 'Enrolled in course successfully!',
            'enrollment_id' => $enrollment->id,
            'redirect_url' => route('student-portal'),
        ], 201);
    }
}

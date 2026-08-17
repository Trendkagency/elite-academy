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
            ->get();

        return view('pages.courses', [
            'pageTitle' => 'Courses — Elite Academy',
            'activeNav' => 'courses',
            'courses' => $courses,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(?string $slug = null): View
    {
        $course = Course::where('is_active', true)
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->with(['subject', 'teacher.user', 'sessions.assignments'])
            ->first();

        return view('pages.course-details', [
            'pageTitle' => $course ? $course->title : 'Course Details — Elite Academy',
            'activeNav' => 'courses',
            'course' => $course,
        ]);
    }

    public function enroll(Request $request, int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $course = Course::find($id);
        if (! $course) {
            return response()->json(['success' => false, 'message' => 'Course not found'], 404);
        }

        $enrollment = CourseEnrollment::firstOrCreate([
            'student_user_id' => $user->id,
            'course_id' => $course->id,
        ], [
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
            'message' => 'Enrolled in course successfully!',
            'enrollment_id' => $enrollment->id,
        ], 201);
    }
}

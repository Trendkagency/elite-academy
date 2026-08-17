<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(
        protected CourseRepositoryInterface $courseRepository
    ) {}

    public function index()
    {
        $courses = $this->courseRepository->getPublishedCourses();

        return CourseResource::collection($courses);
    }

    public function show(int $id)
    {
        $course = $this->courseRepository->find($id);

        if (! $course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return new CourseResource($course);
    }

    public function enroll(Request $request, int $id)
    {
        $user = auth()->user();
        $course = $this->courseRepository->find($id);

        if (! $course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $enrollment = CourseEnrollment::firstOrCreate([
            'student_user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // Automatically unlock first session
        $firstSession = CourseSession::where('course_id', $course->id)->orderBy('sort_order')->first();
        if ($firstSession) {
            CourseSessionProgress::firstOrCreate([
                'course_enrollment_id' => $enrollment->id,
                'course_session_id' => $firstSession->id,
            ], [
                'status' => \App\Enums\SessionProgressStatus::UNLOCKED->value,
                'unlocked_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Enrolled successfully',
            'enrollment_id' => $enrollment->id,
        ], 201);
    }
}

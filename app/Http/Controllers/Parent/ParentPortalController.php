<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParentPortalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $parentProfile = ParentProfile::where('user_id', $user->id)->first();

        $linkedStudents = [];
        if ($parentProfile) {
            $linkedStudentUserIds = DB::table('parent_student')
                ->where('parent_user_id', $user->id)
                ->pluck('student_user_id');

            $linkedStudents = StudentProfile::whereIn('user_id', $linkedStudentUserIds)
                ->with(['user', 'gradeLevel'])
                ->get();
        }

        return view('pages.parent-portal', [
            'pageTitle' => __('app.parent_portal') . ' — Elite Academy',
            'activeNav' => 'portal',
            'linkedStudents' => $linkedStudents,
        ]);
    }

    public function studentProgress(int $studentUserId): JsonResponse
    {
        $user = auth()->user();

        // Security check: Verify the user is a Parent or Admin
        if (! $user->isAdmin()) {
            $parentProfile = ParentProfile::where('user_id', $user->id)->first();
            if (! $parentProfile) {
                return response()->json(['success' => false, 'message' => __('app.auth.unauthorized')], 403);
            }

            $isLinked = DB::table('parent_student')
                ->where('parent_user_id', $user->id)
                ->where('student_user_id', $studentUserId)
                ->exists();

            if (! $isLinked) {
                return response()->json(['success' => false, 'message' => 'Unauthorized: This student is not linked to your parent account.'], 403);
            }
        }

        $enrollments = CourseEnrollment::where('student_user_id', $studentUserId)
            ->with(['course.subject', 'progress'])
            ->get();

        $submissions = AssignmentSubmission::where('student_user_id', $studentUserId)
            ->with(['assignment.session'])
            ->get();

        return response()->json([
            'success' => true,
            'student_id' => $studentUserId,
            'enrollments_count' => $enrollments->count(),
            'enrollments' => $enrollments,
            'submissions' => $submissions,
        ]);
    }
}

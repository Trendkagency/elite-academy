<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $studentProfile = $user ? StudentProfile::where('user_id', $user->id)->with('gradeLevel')->first() : null;

        $package = $user ? StudentPackage::where('student_user_id', $user->id)
            ->with('packageTemplate')
            ->orderBy('created_at', 'desc')
            ->first() : null;

        $hasActivePackage = $package && $package->status === 'active' && $package->remaining_sessions > 0 && (! $package->expires_at || $package->expires_at->isFuture());

        $upcomingSessions = $user ? LiveSession::where('student_user_id', $user->id)
            ->with(['teacherProfile.user', 'subject', 'course'])
            ->orderBy('scheduled_at', 'asc')
            ->get() : collect();

        $enrollments = $user ? CourseEnrollment::where('student_user_id', $user->id)
            ->with(['course.subject', 'course.teacher.user', 'progress'])
            ->get() : collect();

        $submissions = $user ? AssignmentSubmission::where('student_user_id', $user->id)
            ->with(['assignment.course', 'assignment.session', 'assignment.liveSession', 'answers'])
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $submittedAssignmentIds = $submissions->pluck('assignment_id')->filter()->toArray();

        $availableAssignments = \App\Models\Assignment::with(['questions.options', 'course', 'session', 'liveSession'])
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->whereNotIn('id', $submittedAssignmentIds)
            ->where(function ($q) use ($enrollments, $upcomingSessions) {
                $courseIds = $enrollments->pluck('course_id')->filter()->toArray();
                $sessionIds = $upcomingSessions->pluck('id')->filter()->toArray();

                $q->whereIn('course_id', $courseIds)
                  ->orWhereIn('live_session_id', $sessionIds)
                  ->orWhereNull('course_id');
            })
            ->orderBy('due_at', 'asc')
            ->get()
            ->reject(fn ($a) => $a->isExpired());

        $exceptions = $user ? ExceptionRequest::where('student_user_id', $user->id)
            ->with('liveSession')
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $userNotifications = $user ? \App\Models\UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'notifications_page')
            ->withQueryString() : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);

        return view('pages.student-portal', [
            'pageTitle' => 'Student Portal — Learner Dashboard',
            'activeNav' => 'portal',
            'studentProfile' => $studentProfile,
            'package' => $package,
            'hasActivePackage' => $hasActivePackage,
            'upcomingSessions' => $upcomingSessions,
            'enrollments' => $enrollments,
            'submissions' => $submissions,
            'availableAssignments' => $availableAssignments,
            'exceptions' => $exceptions,
            'userNotifications' => $userNotifications,
        ]);
    }
}

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

        $upcomingSessions = $user ? LiveSession::where('student_user_id', $user->id)
            ->with(['teacherProfile.user', 'subject', 'course'])
            ->orderBy('scheduled_at', 'asc')
            ->get() : collect();

        $enrollments = $user ? CourseEnrollment::where('student_user_id', $user->id)
            ->with(['course.subject', 'course.teacher.user', 'progress'])
            ->get() : collect();

        $submissions = $user ? AssignmentSubmission::where('student_user_id', $user->id)
            ->with(['assignment.session.course'])
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $exceptions = $user ? ExceptionRequest::where('student_user_id', $user->id)
            ->with('liveSession')
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        return view('pages.student-portal', [
            'pageTitle' => 'Student Portal — Learner Dashboard',
            'activeNav' => 'portal',
            'studentProfile' => $studentProfile,
            'package' => $package,
            'upcomingSessions' => $upcomingSessions,
            'enrollments' => $enrollments,
            'submissions' => $submissions,
            'exceptions' => $exceptions,
        ]);
    }
}

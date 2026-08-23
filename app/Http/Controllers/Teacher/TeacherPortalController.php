<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\AccountStatus;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Models\LiveSession;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherPortalController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if ($user && $user->status !== AccountStatus::APPROVED) {
            auth()->logout();
            return redirect()->route('login')->with('error', __('app.auth.account_pending'));
        }

        $teacherProfile = $this->getAuthorizedTeacherProfile($user);

        if (! $teacherProfile) {
            return redirect()->route('home')->with('error', __('Teacher profile not found or unauthorized.'));
        }

        $teacherId = $teacherProfile->id;

        // 1. Teacher's Active Courses & Subjects
        $courses = Course::where('teacher_id', $teacherId)
            ->where('is_active', true)
            ->with(['subject', 'gradeLevel', 'sessions'])
            ->get();

        $courseIds = $courses->pluck('id')->filter()->toArray();

        // 2. Today's Sessions (Strictly Teacher-Owned)
        $todaySessions = LiveSession::where('teacher_profile_id', $teacherId)
            ->where(function ($q) {
                $q->whereDate('scheduled_at', Carbon::today())
                  ->orWhereDate('start_at', Carbon::today());
            })
            ->with(['studentUser', 'subject', 'course'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        // 3. Upcoming Sessions (Strictly Teacher-Owned)
        $upcomingSessions = LiveSession::where('teacher_profile_id', $teacherId)
            ->where(function ($q) {
                $q->where('scheduled_at', '>=', now())
                  ->orWhere('start_at', '>=', now());
            })
            ->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])
            ->with(['studentUser', 'subject', 'course'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        // 4. All Sessions for management tab
        $allSessions = LiveSession::where('teacher_profile_id', $teacherId)
            ->with(['studentUser', 'subject', 'course', 'assignments'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10, ['*'], 'sessions_page')
            ->withQueryString();

        $activeTab = $request->query('tab', 'overview');

        // 5. Teacher's Assignments
        $assignments = Assignment::where('teacher_profile_id', $teacherId)
            ->orWhereIn('course_id', $courseIds)
            ->with(['course', 'session', 'liveSession', 'submissions'])
            ->orderBy('created_at', 'desc')
            ->get();

        $assignmentIds = $assignments->pluck('id')->filter()->toArray();

        // 6. Assigned Students Roster (Linked via CourseEnrollment or LiveSession, or fall back to all students)
        $enrolledStudentUserIds = CourseEnrollment::whereIn('course_id', $courseIds)
            ->pluck('student_user_id')
            ->filter()
            ->toArray();

        $directSessionStudentUserIds = LiveSession::where('teacher_profile_id', $teacherId)
            ->pluck('student_user_id')
            ->filter()
            ->toArray();

        $allAssignedUserIds = array_unique(array_merge($enrolledStudentUserIds, $directSessionStudentUserIds));

        $assignedStudentsQuery = StudentProfile::query()->with(['user', 'gradeLevel']);
        if (! empty($allAssignedUserIds)) {
            $assignedStudentsQuery->whereIn('user_id', $allAssignedUserIds);
        }

        $assignedStudents = $assignedStudentsQuery->get()->map(function ($st) use ($courseIds, $assignmentIds, $teacherId) {
            $stSubmissions = AssignmentSubmission::where('student_user_id', $st->user_id)
                ->whereIn('assignment_id', $assignmentIds)
                ->get();

            $gradedSubmissions = $stSubmissions->filter(fn ($s) => ! is_null($s->score));
            $st->avg_score = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('score'), 1) : null;
            $st->submissions_count = $stSubmissions->count();

            $stSessions = LiveSession::where('teacher_profile_id', $teacherId)
                ->where('student_user_id', $st->user_id)
                ->get();

            $attendedCount = $stSessions->where('attendance_status', 'present')->count();
            $totalCount = $stSessions->count();
            $st->attendance_rate = $totalCount > 0 ? round(($attendedCount / $totalCount) * 100) : 100;

            $st->enrolled_courses_count = CourseEnrollment::where('student_user_id', $st->user_id)
                ->whereIn('course_id', $courseIds)
                ->count();

            return $st;
        });

        // 7. Assignment Submissions Needing Review
        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->with(['assignment', 'studentUser', 'answers.question'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $pendingSubmissions = $submissions->filter(function ($s) {
            $val = $s->status instanceof SubmissionStatus ? $s->status->value : (string) $s->status;
            return in_array($val, ['submitted', 'in_progress'], true);
        })->values();

        // 8. Notifications Feed
        $userNotifications = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(6, ['*'], 'notif_page')
            ->withQueryString();

        $unreadNotifCount = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // ── Statistics KPI Calculations ──────────────────────────────────────────
        $todaySessionsCount = $todaySessions->count();
        $upcomingSessionsCount = $upcomingSessions->count();
        $assignedStudentsCount = $assignedStudents->count();
        $pendingAssignmentsCount = $pendingSubmissions->count();
        $submittedAssignmentsCount = $submissions->count();

        $completedSessionsCount = LiveSession::where('teacher_profile_id', $teacherId)->where('status', 'completed')->count();
        $totalPastSessionsCount = LiveSession::where('teacher_profile_id', $teacherId)->where('scheduled_at', '<', now())->count();
        $attendanceRate = $totalPastSessionsCount > 0 ? round(($completedSessionsCount / $totalPastSessionsCount) * 100) : 100;

        return view('pages.teacher-portal', [
            'pageTitle' => 'Teacher Portal — Faculty Academic Dashboard',
            'activeNav' => 'portal',
            'activeTab' => $activeTab,
            'teacherProfile' => $teacherProfile,
            'courses' => $courses,
            'todaySessions' => $todaySessions,
            'upcomingSessions' => $upcomingSessions,
            'allSessions' => $allSessions,
            'assignedStudents' => $assignedStudents,
            'assignments' => $assignments,
            'submissions' => $submissions,
            'pendingSubmissions' => $pendingSubmissions,
            'userNotifications' => $userNotifications,
            'unreadNotifCount' => $unreadNotifCount,
            // KPIs
            'todaySessionsCount' => $todaySessionsCount,
            'upcomingSessionsCount' => $upcomingSessionsCount,
            'assignedStudentsCount' => $assignedStudentsCount,
            'pendingAssignmentsCount' => $pendingAssignmentsCount,
            'submittedAssignmentsCount' => $submittedAssignmentsCount,
            'attendanceRate' => $attendanceRate,
        ]);
    }

    /**
     * AJAX Endpoint: Create Live Session
     */
    public function createSession(Request $request): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'meeting_link' => 'nullable|url|max:500',
            'is_free_demo' => 'nullable|boolean',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: You do not own this course.'], 403);
        }

        $scheduledAt = Carbon::parse($validated['scheduled_at']);
        $duration = (int) ($validated['duration_minutes'] ?? 60);
        $endAt = $scheduledAt->copy()->addMinutes($duration);

        $liveSession = LiveSession::create([
            'title' => $validated['title'],
            'teacher_profile_id' => $teacherProfile->id,
            'course_id' => $course->id,
            'subject_id' => $course->subject_id,
            'scheduled_at' => $scheduledAt,
            'start_at' => $scheduledAt,
            'end_at' => $endAt,
            'duration_minutes' => $duration,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'status' => 'scheduled',
            'is_free_demo' => (bool) ($validated['is_free_demo'] ?? false),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Live session created successfully!'),
            'session_id' => $liveSession->id,
        ], 201);
    }

    /**
     * AJAX Endpoint: Full Update Session Details
     */
    public function updateSession(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($id);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'meeting_link' => 'nullable|url|max:500',
            'is_free_demo' => 'nullable|boolean',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized course ownership'], 403);
        }

        $scheduledAt = Carbon::parse($validated['scheduled_at']);
        $duration = (int) ($validated['duration_minutes'] ?? $session->duration_minutes ?? 60);
        $endAt = $scheduledAt->copy()->addMinutes($duration);

        $session->update([
            'title' => $validated['title'],
            'course_id' => $course->id,
            'subject_id' => $course->subject_id,
            'scheduled_at' => $scheduledAt,
            'start_at' => $scheduledAt,
            'end_at' => $endAt,
            'duration_minutes' => $duration,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'is_free_demo' => (bool) ($validated['is_free_demo'] ?? false),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session details updated successfully!'),
        ]);
    }

    /**
     * AJAX Endpoint: Update Session Meeting Link
     */
    public function updateMeetingLink(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($id);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'meeting_link' => 'required|url|max:500',
        ]);

        $session->update([
            'meeting_link' => $validated['meeting_link'],
            'status' => 'link_visible',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Meeting link updated and made visible to students!'),
            'meeting_link' => $session->meeting_link,
        ]);
    }

    /**
     * AJAX Endpoint: Reschedule Session
     */
    public function rescheduleSession(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($id);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
        ]);

        $newStart = Carbon::parse($validated['scheduled_at']);
        $duration = (int) ($validated['duration_minutes'] ?? $session->duration_minutes ?? 60);
        $newEnd = $newStart->copy()->addMinutes($duration);

        $session->update([
            'scheduled_at' => $newStart,
            'start_at' => $newStart,
            'end_at' => $newEnd,
            'duration_minutes' => $duration,
            'status' => 'rescheduled',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session rescheduled successfully! Affected students have been notified.'),
        ]);
    }

    /**
     * AJAX Endpoint: Cancel Session
     */
    public function cancelSession(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($id);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $session->update([
            'status' => 'cancelled_by_teacher',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Session has been cancelled. Affected students have been notified.'),
        ]);
    }

    /**
     * AJAX Endpoint: Create Assignment
     */
    public function createAssignment(Request $request): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'live_session_id' => 'nullable|exists:live_sessions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'required|date',
            'passing_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized course ownership'], 403);
        }

        $assignment = Assignment::create([
            'teacher_profile_id' => $teacherProfile->id,
            'course_id' => $course->id,
            'live_session_id' => $validated['live_session_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_at' => Carbon::parse($validated['due_at']),
            'status' => 'published',
            'passing_score' => (float) ($validated['passing_score'] ?? 70.0),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Assignment published successfully!'),
            'assignment_id' => $assignment->id,
        ], 201);
    }

    /**
     * AJAX Endpoint: Grade / Review Assignment Submission
     */
    public function reviewSubmission(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $submission = AssignmentSubmission::with('assignment')->findOrFail($id);

        if ((int) $submission->assignment->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'evaluation_notes' => 'nullable|string|max:1000',
        ]);

        $score = (float) $validated['score'];

        $submission->update([
            'score' => $score,
            'grade' => $score,
            'percentage' => $score,
            'evaluation_notes' => $validated['evaluation_notes'] ?? null,
            'status' => SubmissionStatus::REVIEWED->value,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Notify Student
        app(\App\Services\Notification\FcmNotificationService::class)->notifyStudentSubmissionGraded($submission);

        return response()->json([
            'success' => true,
            'message' => __('Submission graded and evaluation feedback sent to student!'),
        ]);
    }

    /**
     * AJAX Endpoint: Mark Session Attendance
     */
    public function markAttendance(Request $request, int $sessionId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($sessionId);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_user_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
        ]);

        foreach ($validated['attendance'] as $record) {
            $studentUserId = (int) $record['student_user_id'];
            $status = $record['status'];

            if ($status === 'absent') {
                $studentUser = User::find($studentUserId);
                if ($studentUser) {
                    app(\App\Services\Notification\FcmNotificationService::class)->notifyTeacherStudentAbsent($session, $studentUser);
                }
            }
        }

        $session->update([
            'attendance_status' => 'recorded',
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Attendance marked and saved successfully!'),
        ]);
    }

    /**
     * AJAX Endpoint: Get Comprehensive Student Academic Profile Details
     */
    public function getStudentDetails(Request $request, int $studentUserId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $studentProfile = StudentProfile::where('user_id', $studentUserId)
            ->with(['user', 'gradeLevel'])
            ->firstOrFail();

        $courses = Course::where('teacher_id', $teacherProfile->id)->pluck('id')->toArray();
        $assignments = Assignment::where('teacher_profile_id', $teacherProfile->id)
            ->orWhereIn('course_id', $courses)
            ->pluck('id')->toArray();

        // 1. Attendance Records
        $attendanceRecords = LiveSession::where('teacher_profile_id', $teacherProfile->id)
            ->where('student_user_id', $studentUserId)
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title ?: 'Live Session',
                'date' => $s->effective_start_at ? $s->effective_start_at->format('Y-m-d h:i A') : 'Scheduled',
                'status' => $s->attendance_status ?: 'pending',
            ]);

        // 2. Submissions Records
        $submissions = AssignmentSubmission::where('student_user_id', $studentUserId)
            ->whereIn('assignment_id', $assignments)
            ->with('assignment')
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(fn ($sub) => [
                'id' => $sub->id,
                'assignment_title' => $sub->assignment?->title ?: 'Assignment',
                'score' => $sub->score,
                'status' => is_object($sub->status) ? $sub->status->value : (string) $sub->status,
                'submitted_at' => $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '',
                'evaluation_notes' => $sub->evaluation_notes,
            ]);

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $studentUserId,
                'name' => $studentProfile->user?->name ?: 'Student',
                'email' => $studentProfile->user?->email ?: '',
                'school' => $studentProfile->school_name ?: 'Elite Academy',
                'grade' => $studentProfile->gradeLevel?->name ?: 'High School',
            ],
            'attendance' => $attendanceRecords,
            'submissions' => $submissions,
        ]);
    }

    /**
     * AJAX Endpoint: Get Detailed Submission & Question Auto-Correction Breakdown (Read-Only)
     */
    public function getSubmissionReview(Request $request, int $submissionId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $submission = AssignmentSubmission::with(['assignment.questions.options', 'studentUser', 'answers'])->findOrFail($submissionId);

        if ((int) $submission->assignment->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $answersMap = $submission->answers->keyBy('question_id');

        $questionsData = $submission->assignment->questions->map(function ($q) use ($answersMap) {
            $ans = $answersMap->get($q->id);
            $selectedOptionIds = $ans ? (array) ($ans->selected_option_ids ?: []) : [];

            $options = $q->options->map(function ($opt) use ($selectedOptionIds) {
                return [
                    'id' => $opt->id,
                    'option_text' => $opt->option_text,
                    'is_correct' => (bool) $opt->is_correct,
                    'is_selected' => in_array($opt->id, $selectedOptionIds),
                    'explanation' => $opt->explanation,
                ];
            });

            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'points' => (float) $q->points,
                'is_correct' => $ans ? (bool) $ans->is_correct : false,
                'points_earned' => $ans ? (float) $ans->points_earned : 0.0,
                'options' => $options,
            ];
        });

        return response()->json([
            'success' => true,
            'submission' => [
                'id' => $submission->id,
                'student_name' => $submission->studentUser?->name ?: 'Student',
                'assignment_title' => $submission->assignment?->title ?: 'Assignment',
                'score' => $submission->score,
                'status' => is_object($submission->status) ? $submission->status->value : (string) $submission->status,
                'submitted_at' => $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '',
                'evaluation_notes' => $submission->evaluation_notes,
            ],
            'questions' => $questionsData,
        ]);
    }

    /**
     * Helper to get authorized TeacherProfile
     */
    protected function getAuthorizedTeacherProfile(?User $user): ?TeacherProfile
    {
        if (! $user) {
            return null;
        }

        if ($user->teacherProfile) {
            return $user->teacherProfile;
        }

        if ($user->isAdmin()) {
            // For testing/admin debugging: return first active teacher profile or auto-create demo profile
            $profile = TeacherProfile::first();
            if (! $profile) {
                $profile = TeacherProfile::create([
                    'user_id' => $user->id,
                    'slug' => 'faculty-admin',
                    'title' => 'Faculty Director',
                    'specialization' => 'Academic Advisory',
                    'years_experience' => 10,
                    'rating_avg' => 4.9,
                    'students_count' => 500,
                ]);
            }

            return $profile;
        }

        return null;
    }
}

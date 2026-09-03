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
use App\Models\RecurringSchedule;
use App\Models\SessionAuditLog;
use App\Models\StudentEducationalNote;
use App\Models\StudentProfile;
use App\Models\StudentSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\Session\RecurringScheduleService;
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
            $lateCount = $stSessions->where('attendance_status', 'late')->count();
            $totalCount = $stSessions->count();
            $st->attendance_rate = $totalCount > 0 ? round((($attendedCount + ($lateCount * 0.5)) / $totalCount) * 100) : 100;

            $studentEnrollments = CourseEnrollment::where('student_user_id', $st->user_id)
                ->whereIn('course_id', $courseIds)
                ->with('course')
                ->get();

            $st->enrolled_courses = $studentEnrollments->map(fn ($e) => [
                'id' => $e->course_id,
                'title' => $e->course?->title ?: '',
            ]);
            $st->enrolled_courses_count = $studentEnrollments->count();
            $st->enrolled_course_ids = $studentEnrollments->pluck('course_id')->toArray();

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

        $gradeLevels = \App\Models\GradeLevel::orderBy('sort_order')->get();
        $initialStudentId = $request->query('student');

        return view('pages.teacher-portal', [
            'pageTitle' => __('app.teacher.portal_title'),
            'activeNav' => 'portal',
            'activeTab' => $activeTab,
            'initialStudentId' => $initialStudentId,
            'teacherProfile' => $teacherProfile,
            'courses' => $courses,
            'gradeLevels' => $gradeLevels,
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
    /**
     * AJAX Endpoint: Preview Recurring Schedule Dates & Check Conflicts
     */
    public function previewRecurringSchedule(Request $request, RecurringScheduleService $service): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'student_user_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|string',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'recurrence_type' => 'required|in:single,weekly,monthly,multi_month,yearly',
            'days_of_week' => 'nullable|array',
            'monthly_pattern' => 'nullable|array',
        ]);

        $params = array_merge($validated, [
            'teacher_profile_id' => $teacherProfile->id,
        ]);

        try {
            $dates = $service->previewDates($params);
            $hasAnyConflict = collect($dates)->contains('has_conflict', true);

            return response()->json([
                'success' => true,
                'total_sessions' => count($dates),
                'has_conflicts' => $hasAnyConflict,
                'dates' => $dates,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * AJAX Endpoint: Create Recurring Schedule & Generate Session Instances
     */
    public function createRecurringSchedule(Request $request, RecurringScheduleService $service): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'student_user_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|string',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'recurrence_type' => 'required|in:single,weekly,monthly,multi_month,yearly',
            'days_of_week' => 'nullable|array',
            'monthly_pattern' => 'nullable|array',
            'meeting_link' => 'nullable|url|max:500',
            'meeting_platform' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: You do not own this course.'], 403);
        }

        $data = array_merge($validated, [
            'teacher_profile_id' => $teacherProfile->id,
        ]);

        try {
            $schedule = $service->createSchedule($data, auth()->user());

            return response()->json([
                'success' => true,
                'message' => __('Recurring schedule created and sessions generated successfully!'),
                'schedule_id' => $schedule->id,
                'sessions_count' => $schedule->sessions()->count(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * AJAX Endpoint: Create Single Live Session
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
            'lifecycle_state' => 'scheduled',
            'is_free_demo' => (bool) ($validated['is_free_demo'] ?? false),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Live session created successfully!'),
            'session_id' => $liveSession->id,
        ], 201);
    }

    /**
     * AJAX Endpoint: Update Session with Scope (This Only / This & Future / All)
     */
    public function updateSessionOverride(Request $request, int $id, RecurringScheduleService $service): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($id);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'scope' => 'required|in:this_only,this_and_future,all',
            'title' => 'nullable|string|max:255',
            'scheduled_at' => 'nullable|date',
            'start_time' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'meeting_link' => 'nullable|url|max:500',
            'teacher_notes' => 'nullable|string|max:1000',
            'reason' => 'nullable|string|max:255',
        ]);

        $scope = $validated['scope'];
        $reason = $validated['reason'] ?? __('Teacher updated session schedule.');

        try {
            if ($scope === 'this_only') {
                $service->updateSingleSessionOverride($session, $validated, $reason, auth()->user());
                $msg = __('This session has been updated as an individual override.');
            } elseif ($scope === 'this_and_future') {
                $service->updateFutureSessions($session, $validated, auth()->user());
                $msg = __('This session and all future sessions in the series have been updated.');
            } else {
                if ($session->recurringSchedule) {
                    $service->updateEntireSchedule($session->recurringSchedule, $validated, auth()->user());
                } else {
                    $service->updateSingleSessionOverride($session, $validated, $reason, auth()->user());
                }
                $msg = __('The entire recurring schedule has been updated.');
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
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
    public function rescheduleSession(Request $request, int $id, RecurringScheduleService $service): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($id);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'reason' => 'nullable|string|max:255',
        ]);

        $newStart = Carbon::parse($validated['scheduled_at']);
        $duration = (int) ($validated['duration_minutes'] ?? $session->duration_minutes ?? 60);
        $reason = $validated['reason'] ?? __('Teacher rescheduled the session.');

        try {
            $service->rescheduleSession($session, $newStart, $duration, $reason, auth()->user());

            return response()->json([
                'success' => true,
                'message' => __('Session rescheduled successfully! Affected students have been notified.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * AJAX Endpoint: Cancel Session
     */
    public function cancelSession(Request $request, int $id, RecurringScheduleService $service): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::findOrFail($id);

        if ((int) $session->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $reason = $validated['reason'] ?? __('Session cancelled by instructor.');
        $service->cancelSession($session, $reason, auth()->user());

        return response()->json([
            'success' => true,
            'message' => __('Session has been cancelled. Affected students have been notified.'),
        ]);
    }

    /**
     * AJAX Endpoint: Get Calendar Feed for Teacher Portal
     */
    public function getCalendarEvents(Request $request): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json([], 403);
        }

        $start = $request->query('start') ? Carbon::parse($request->query('start')) : now()->startOfMonth()->subDays(7);
        $end = $request->query('end') ? Carbon::parse($request->query('end')) : now()->endOfMonth()->addDays(7);

        $sessions = LiveSession::where('teacher_profile_id', $teacherProfile->id)
            ->whereBetween('scheduled_at', [$start, $end])
            ->with(['course.subject', 'studentUser'])
            ->get();

        $events = $sessions->map(function ($s) {
            $statusColors = [
                'completed' => '#10B981',
                'in_progress' => '#06B6D4',
                'ready' => '#3B82F6',
                'scheduled' => '#0D9488',
                'cancelled' => '#EF4444',
                'cancelled_by_teacher' => '#EF4444',
                'rescheduled' => '#F59E0B',
            ];

            $color = $statusColors[$s->status] ?? '#64748B';

            return [
                'id' => $s->id,
                'title' => $s->title,
                'start' => $s->effective_start_at ? $s->effective_start_at->toIso8601String() : $s->scheduled_at->toIso8601String(),
                'end' => $s->effective_end_at ? $s->effective_end_at->toIso8601String() : $s->end_at?->toIso8601String(),
                'course' => $s->course?->title ?: '',
                'subject' => $s->course?->subject?->name ?: '',
                'student_name' => $s->studentUser?->name ?: __('General Cohort'),
                'status' => $s->status,
                'meeting_link' => $s->meeting_link,
                'is_override' => (bool) $s->is_override,
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        });

        return response()->json($events);
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
            'duration_minutes' => 'nullable|integer|min:5|max:300',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'required_with:questions|string|max:1000',
            'questions.*.points' => 'nullable|numeric|min:0.1',
            'questions.*.correct_index' => 'nullable|integer|min:0|max:10',
            'questions.*.options' => 'nullable|array|min:2',
            'questions.*.options.*' => 'nullable|string|max:500',
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
            'duration_minutes' => (int) ($validated['duration_minutes'] ?? 30),
            'due_at' => Carbon::parse($validated['due_at']),
            'status' => 'published',
            'passing_score' => (float) ($validated['passing_score'] ?? 70.0),
        ]);

        // Process questions if provided
        if (! empty($validated['questions']) && is_array($validated['questions'])) {
            foreach ($validated['questions'] as $qIdx => $qData) {
                if (empty($qData['question_text'])) continue;

                $qPoints = (float) ($qData['points'] ?? 1.0);
                $correctIndex = isset($qData['correct_index']) ? (int) $qData['correct_index'] : 0;

                $question = \App\Models\AssignmentQuestion::create([
                    'assignment_id' => $assignment->id,
                    'question_text' => $qData['question_text'],
                    'question_type' => 'text',
                    'points' => $qPoints,
                    'sort_order' => $qIdx + 1,
                    'is_multiple_choice' => false,
                ]);

                if (! empty($qData['options']) && is_array($qData['options'])) {
                    foreach ($qData['options'] as $optIdx => $optText) {
                        if (trim((string)$optText) === '') continue;

                        \App\Models\AssignmentQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => trim($optText),
                            'sort_order' => $optIdx + 1,
                            'is_correct' => ($optIdx === $correctIndex),
                        ]);
                    }
                }
            }
        }

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

        $primaryStatus = null;
        $hasPresent = false;

        foreach ($validated['attendance'] as $record) {
            $studentUserId = (int) $record['student_user_id'];
            $status = $record['status'];

            // Persist per-student session attendance state
            StudentSession::updateOrCreate(
                [
                    'student_user_id' => $studentUserId,
                    'live_session_id' => $session->id,
                ],
                [
                    'attendance_status' => $status,
                    'session_status' => 'completed',
                    'completed_at' => now(),
                ]
            );

            if (in_array($status, ['present', 'late'], true)) {
                $hasPresent = true;
            }

            if ((int) $session->student_user_id === $studentUserId) {
                $primaryStatus = in_array($status, ['present', 'absent', 'excused'], true) ? $status : 'present';
            }

            if ($status === 'absent') {
                $studentUser = User::find($studentUserId);
                if ($studentUser) {
                    app(\App\Services\Notification\FcmNotificationService::class)->notifyTeacherStudentAbsent($session, $studentUser);
                }
            }
        }

        $sessionAttendanceStatus = $session->student_user_id
            ? ($primaryStatus ?: 'present')
            : ($hasPresent ? 'present' : 'absent');

        $session->update([
            'attendance_status' => $sessionAttendanceStatus,
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Attendance marked and saved successfully!'),
        ]);
    }

    /**
     * AJAX Endpoint: Get Real-Time Attendance Roster for a specific Live Session
     */
    public function getSessionAttendanceRoster(Request $request, int $sessionId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $session = LiveSession::with(['course.subject', 'course.gradeLevel'])->findOrFail($sessionId);

        $isTeacherSession = (int) $session->teacher_profile_id === (int) $teacherProfile->id;
        $isTeacherCourse = $session->course && (int) $session->course->teacher_id === (int) $teacherProfile->id;

        if (! $isTeacherSession && ! $isTeacherCourse && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => __('Unauthorized: You do not own this teaching session.')], 403);
        }

        // Collect registered student user IDs for this specific session / course
        $studentUserIds = collect();

        if ($session->course_id) {
            $enrolledIds = CourseEnrollment::where('course_id', $session->course_id)
                ->pluck('student_user_id')
                ->filter();
            $studentUserIds = $studentUserIds->merge($enrolledIds);
        }

        if ($session->student_user_id) {
            $studentUserIds->push($session->student_user_id);
        }

        // Also check any already recorded student_sessions for this live session
        $existingStudentSessionIds = StudentSession::where('live_session_id', $session->id)
            ->pluck('student_user_id')
            ->filter();
        $studentUserIds = $studentUserIds->merge($existingStudentSessionIds)->unique()->values();

        // If no students enrolled in this course yet
        if ($studentUserIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title ?: 'Live Session',
                    'course_title' => $session->course?->title ?: '',
                    'date' => $session->effective_start_at ? $session->effective_start_at->format('Y-m-d h:i A') : '',
                ],
                'students' => [],
            ]);
        }

        // Fetch existing attendance records for this session
        $existingRecords = StudentSession::where('live_session_id', $session->id)
            ->whereIn('student_user_id', $studentUserIds)
            ->pluck('attendance_status', 'student_user_id')
            ->toArray();

        $students = StudentProfile::whereIn('user_id', $studentUserIds)
            ->with(['user', 'gradeLevel'])
            ->get()
            ->map(function ($st) use ($existingRecords, $session) {
                $status = $existingRecords[$st->user_id] ?? ($session->student_user_id === $st->user_id ? ($session->attendance_status ?: 'present') : 'present');
                return [
                    'id' => $st->user_id,
                    'student_code' => 'STU-' . str_pad((string) $st->user_id, 5, '0', STR_PAD_LEFT),
                    'name' => $st->user?->name ?: 'Student',
                    'school' => $st->school_name ?: 'Elite Academy',
                    'grade' => $st->gradeLevel?->name ?: '',
                    'status' => $status,
                ];
            });

        return response()->json([
            'success' => true,
            'session' => [
                'id' => $session->id,
                'title' => $session->title ?: 'Live Session',
                'course_title' => $session->course?->title ?: '',
                'date' => $session->effective_start_at ? $session->effective_start_at->format('Y-m-d h:i A') : '',
            ],
            'students' => $students,
        ]);
    }

    /**
     * AJAX Endpoint: Get Comprehensive Student Educational Profile Details
     */
    public function getStudentDetails(Request $request, int $studentUserId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $studentProfile = StudentProfile::where('user_id', $studentUserId)
            ->with(['user', 'gradeLevel', 'enrollments.course.subject'])
            ->firstOrFail();

        // Strict IDOR verification: Check if teacher is assigned to this student
        if (! auth()->user()->isAdmin() && ! auth()->user()->can('view', $studentProfile)) {
            return response()->json([
                'success' => false,
                'message' => __('Unauthorized: You do not have permission to access this student educational profile.'),
            ], 403);
        }

        $teacherCourses = Course::where('teacher_id', $teacherProfile->id)
            ->with(['subject', 'gradeLevel'])
            ->get();
        $courseIds = $teacherCourses->pluck('id')->toArray();

        $assignments = Assignment::where('teacher_profile_id', $teacherProfile->id)
            ->orWhereIn('course_id', $courseIds)
            ->pluck('id')
            ->toArray();

        // 1. Enrolled Courses with this Teacher
        $enrolledCourses = CourseEnrollment::where('student_user_id', $studentUserId)
            ->whereIn('course_id', $courseIds)
            ->with(['course.subject', 'course.gradeLevel'])
            ->get()
            ->map(function ($enr) use ($studentUserId) {
                $totalSessions = CourseSession::where('course_id', $enr->course_id)->count();
                $completedSessions = CourseSessionProgress::where('course_enrollment_id', $enr->id)
                    ->where(function ($q) {
                        $q->where('status', 'completed')
                            ->orWhereNotNull('completed_at');
                    })
                    ->count();

                $progressPct = $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0;

                return [
                    'id' => $enr->course_id,
                    'title' => $enr->course?->title ?: 'Course',
                    'subject' => $enr->course?->subject?->name ?: 'General',
                    'grade' => $enr->course?->gradeLevel?->name ?: '',
                    'enrolled_at' => $enr->enrolled_at ? $enr->enrolled_at->format('Y-m-d') : $enr->created_at->format('Y-m-d'),
                    'status' => $enr->status ?: 'active',
                    'sessions_count' => $totalSessions,
                    'completed_sessions' => $completedSessions,
                    'progress_pct' => $progressPct,
                ];
            });

        // 2. Attendance & Sessions Records (Direct Live Sessions + Course Live Sessions)
        $directSessions = LiveSession::where('teacher_profile_id', $teacherProfile->id)
            ->where('student_user_id', $studentUserId)
            ->with('course')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $studentSessionRecords = StudentSession::where('student_user_id', $studentUserId)
            ->whereHas('liveSession', fn ($q) => $q->where('teacher_profile_id', $teacherProfile->id))
            ->with(['liveSession.course'])
            ->get();

        $sessionItems = collect();

        foreach ($directSessions as $s) {
            $sessionItems->push([
                'id' => $s->id,
                'title' => $s->title ?: 'Live Session',
                'course_title' => $s->course?->title ?: 'General',
                'date' => $s->effective_start_at ? $s->effective_start_at->format('Y-m-d h:i A') : 'Scheduled',
                'raw_date' => $s->effective_start_at ? $s->effective_start_at->format('Y-m-d') : '',
                'duration_minutes' => $s->duration_minutes ?: 60,
                'status' => $s->status ?: 'scheduled',
                'attendance_status' => $s->attendance_status ?: 'pending',
                'meeting_link' => $s->meeting_link,
                'timestamp' => $s->effective_start_at?->timestamp ?? 0,
            ]);
        }

        foreach ($studentSessionRecords as $ss) {
            if ($ss->liveSession && ! $sessionItems->contains('id', $ss->liveSession->id)) {
                $ls = $ss->liveSession;
                $sessionItems->push([
                    'id' => $ls->id,
                    'title' => $ls->title ?: 'Live Session',
                    'course_title' => $ls->course?->title ?: 'General',
                    'date' => $ls->effective_start_at ? $ls->effective_start_at->format('Y-m-d h:i A') : 'Scheduled',
                    'raw_date' => $ls->effective_start_at ? $ls->effective_start_at->format('Y-m-d') : '',
                    'duration_minutes' => $ls->duration_minutes ?: 60,
                    'status' => $ss->session_status ?: $ls->status ?: 'scheduled',
                    'attendance_status' => $ss->attendance_status ?: $ls->attendance_status ?: 'pending',
                    'meeting_link' => $ls->meeting_link,
                    'timestamp' => $ls->effective_start_at?->timestamp ?? 0,
                ]);
            }
        }

        $sessionItems = $sessionItems->sortByDesc('timestamp')->values()->map(function ($item) {
            unset($item['timestamp']);
            return $item;
        });

        // Attendance stats
        $totalSessionsCount = $sessionItems->count();
        $attendedCount = $sessionItems->where('attendance_status', 'present')->count();
        $lateCount = $sessionItems->where('attendance_status', 'late')->count();
        $absentCount = $sessionItems->where('attendance_status', 'absent')->count();
        $excusedCount = $sessionItems->where('attendance_status', 'excused')->count();
        $attendanceRate = $totalSessionsCount > 0 ? round((($attendedCount + ($lateCount * 0.5)) / $totalSessionsCount) * 100) : 100;

        // 3. Submissions & Assessments Records
        $submissions = AssignmentSubmission::where('student_user_id', $studentUserId)
            ->whereIn('assignment_id', $assignments)
            ->with(['assignment.course', 'answers'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $submissionItems = $submissions->map(function ($sub) {
            $statusStr = is_object($sub->status) ? $sub->status->value : (string) $sub->status;
            $passingScore = (float) ($sub->assignment?->passing_score ?? 70.0);
            $score = $sub->score !== null ? (float) $sub->score : null;
            $isPassed = $score !== null ? ($score >= $passingScore) : null;

            return [
                'id' => $sub->id,
                'assignment_id' => $sub->assignment_id,
                'assignment_title' => $sub->assignment?->title ?: 'Assignment',
                'course_title' => $sub->assignment?->course?->title ?: '',
                'score' => $score,
                'passing_score' => $passingScore,
                'is_passed' => $isPassed,
                'status' => $statusStr,
                'submitted_at' => $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '',
                'evaluation_notes' => $sub->evaluation_notes,
                'answers_count' => $sub->answers?->count() ?? 0,
            ];
        });

        $gradedSubmissions = $submissionItems->filter(fn ($s) => $s['score'] !== null);
        $avgScore = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('score'), 1) : null;
        $highestScore = $gradedSubmissions->count() > 0 ? $gradedSubmissions->max('score') : null;
        $passedCount = $gradedSubmissions->where('is_passed', true)->count();
        $passRate = $gradedSubmissions->count() > 0 ? round(($passedCount / $gradedSubmissions->count()) * 100) : 100;

        // 4. Educational Notes by this Teacher
        $educationalNotes = StudentEducationalNote::where('teacher_profile_id', $teacherProfile->id)
            ->where('student_user_id', $studentUserId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'category' => $n->category,
                'note' => $n->note,
                'created_at' => $n->created_at->format('Y-m-d h:i A'),
                'created_at_human' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $studentUserId,
                'student_code' => 'STU-' . str_pad((string) $studentUserId, 5, '0', STR_PAD_LEFT),
                'name' => $studentProfile->user?->name ?: 'Student',
                'email' => $studentProfile->user?->email ?: '',
                'phone' => $studentProfile->user?->phone ?: '',
                'school' => $studentProfile->school_name ?: 'Elite Academy',
                'grade' => $studentProfile->gradeLevel?->name ?: 'Secondary Stage',
                'avatar' => $studentProfile->avatar ?: null,
                'status' => $studentProfile->user?->status?->value ?? 'approved',
                'enrolled_date' => $studentProfile->created_at ? $studentProfile->created_at->format('Y-m-d') : '',
            ],
            'metrics' => [
                'attendance_rate' => $attendanceRate,
                'avg_score' => $avgScore,
                'highest_score' => $highestScore,
                'pass_rate' => $passRate,
                'total_sessions' => $totalSessionsCount,
                'attended_sessions' => $attendedCount,
                'absent_sessions' => $absentCount,
                'late_sessions' => $lateCount,
                'excused_sessions' => $excusedCount,
                'total_submissions' => $submissionItems->count(),
                'graded_submissions' => $gradedSubmissions->count(),
                'pending_submissions' => $submissionItems->whereIn('status', ['submitted', 'in_progress'])->count(),
                'enrolled_courses_count' => $enrolledCourses->count(),
            ],
            'courses' => $enrolledCourses,
            'sessions' => $sessionItems,
            'attendance' => $sessionItems,
            'submissions' => $submissionItems,
            'assessments' => $submissionItems,
            'notes' => $educationalNotes,
        ]);
    }

    /**
     * AJAX Endpoint: Store Teacher Educational Note for Student
     */
    public function storeStudentNote(Request $request, int $studentUserId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $studentProfile = StudentProfile::where('user_id', $studentUserId)->firstOrFail();

        if (! auth()->user()->isAdmin() && ! auth()->user()->can('addNote', $studentProfile)) {
            return response()->json([
                'success' => false,
                'message' => __('Unauthorized: Cannot add educational note for unassigned student.'),
            ], 403);
        }

        $validated = $request->validate([
            'note' => 'required|string|max:2000',
            'category' => 'nullable|string|in:academic,homework,participation,behavior,general',
        ]);

        // Security policy: Teachers cannot share phone numbers or contact details in notes
        if (\App\Services\Security\ContentSecurityService::containsPhoneNumber($validated['note'])) {
            return response()->json([
                'success' => false,
                'message' => __('Security policy violation: Sharing phone numbers or contact information in educational notes is strictly prohibited.'),
                'errors' => [
                    'note' => [__('Security policy violation: Sharing phone numbers or contact information in educational notes is strictly prohibited.')],
                ],
            ], 422);
        }

        // Defense-in-depth sanitization
        $sanitizedNote = \App\Services\Security\ContentSecurityService::maskPhoneNumbers($validated['note']);

        $note = StudentEducationalNote::create([
            'teacher_profile_id' => $teacherProfile->id,
            'student_user_id' => $studentUserId,
            'category' => $validated['category'] ?? 'general',
            'note' => $sanitizedNote,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Educational note saved successfully!'),
            'note' => [
                'id' => $note->id,
                'category' => $note->category,
                'note' => $note->note,
                'created_at' => $note->created_at->format('Y-m-d h:i A'),
                'created_at_human' => $note->created_at->diffForHumans(),
            ],
        ], 201);
    }

    /**
     * Direct Route: Show Dedicated Student Profile View
     */
    public function showStudentProfile(Request $request, int $studentUserId)
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return redirect()->route('teacher-portal')->with('error', 'Unauthorized');
        }

        $studentProfile = StudentProfile::where('user_id', $studentUserId)->firstOrFail();

        if (! auth()->user()->isAdmin() && ! auth()->user()->can('view', $studentProfile)) {
            abort(403, __('Unauthorized: You do not have permission to view this student profile.'));
        }

        return redirect()->route('teacher-portal', [
            'tab' => 'students',
            'student' => $studentUserId,
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

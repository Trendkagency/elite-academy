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
use App\Models\ExceptionRequest;
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
use App\Services\Exception\ExceptionRequestService;
use App\Services\Session\RecurringScheduleService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        // 4. All Sessions for management tab (Real-Time Client-Side Pagination)
        $allSessions = LiveSession::where('teacher_profile_id', $teacherId)
            ->with(['studentUser', 'subject', 'course', 'assignments'])
            ->orderBy('scheduled_at', 'desc')
            ->take(250)
            ->get();

        $activeTab = $request->query('tab', 'overview');
        if ($request->has('notif_page')) {
            $activeTab = 'notifications';
        } elseif ($request->has('sessions_page')) {
            $activeTab = 'sessions';
        }

        // 5. Teacher's Assignments
        $assignments = Assignment::where('teacher_profile_id', $teacherId)
            ->orWhereIn('course_id', $courseIds)
            ->with(['course', 'session', 'liveSession', 'submissions'])
            ->orderBy('created_at', 'desc')
            ->get();

        $assignmentIds = $assignments->pluck('id')->filter()->toArray();

        // 6. Assigned Students Roster (Strictly Scoped: CourseEnrollment, LiveSession, StudentSession, AssignmentSubmission)
        $allTeacherCourseIds = Course::where('teacher_id', $teacherId)->pluck('id')->filter()->toArray();

        $enrolledStudentUserIds = CourseEnrollment::whereIn('course_id', $allTeacherCourseIds)
            ->pluck('student_user_id')
            ->filter()
            ->toArray();

        $directSessionStudentUserIds = LiveSession::where('teacher_profile_id', $teacherId)
            ->whereNotNull('student_user_id')
            ->pluck('student_user_id')
            ->filter()
            ->toArray();

        $groupSessionStudentUserIds = StudentSession::whereHas('liveSession', fn ($q) => $q->where('teacher_profile_id', $teacherId))
            ->pluck('student_user_id')
            ->filter()
            ->toArray();

        $assignmentStudentUserIds = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->pluck('student_user_id')
            ->filter()
            ->toArray();

        $allAssignedUserIds = array_values(array_unique(array_merge(
            $enrolledStudentUserIds,
            $directSessionStudentUserIds,
            $groupSessionStudentUserIds,
            $assignmentStudentUserIds
        )));

        if (empty($allAssignedUserIds)) {
            $assignedStudents = collect();
        } else {
            $assignedStudents = StudentProfile::query()
                ->whereIn('user_id', $allAssignedUserIds)
                ->with(['user', 'gradeLevel'])
                ->latest('created_at')
                ->get()
                ->map(function ($st) use ($allTeacherCourseIds, $assignmentIds, $teacherId) {
                    $stSubmissions = AssignmentSubmission::where('student_user_id', $st->user_id)
                        ->whereIn('assignment_id', $assignmentIds)
                        ->get();

                    $gradedSubmissions = $stSubmissions->filter(fn ($s) => ! is_null($s->score));
                    $st->avg_score = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('score'), 1) : null;
                    $st->submissions_count = $stSubmissions->count();

                    $stDirectSessions = LiveSession::where('teacher_profile_id', $teacherId)
                        ->where('student_user_id', $st->user_id)
                        ->get();

                    $stGroupSessions = StudentSession::where('student_user_id', $st->user_id)
                        ->whereHas('liveSession', fn ($q) => $q->where('teacher_profile_id', $teacherId))
                        ->get();

                    $attendedCount = $stDirectSessions->where('attendance_status', 'present')->count() + $stGroupSessions->where('attendance_status', 'present')->count();
                    $lateCount = $stDirectSessions->where('attendance_status', 'late')->count() + $stGroupSessions->where('attendance_status', 'late')->count();
                    $totalCount = $stDirectSessions->count() + $stGroupSessions->count();
                    $st->attendance_rate = $totalCount > 0 ? round((($attendedCount + ($lateCount * 0.5)) / $totalCount) * 100) : 100;

                    $studentEnrollments = CourseEnrollment::where('student_user_id', $st->user_id)
                        ->whereIn('course_id', $allTeacherCourseIds)
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
        }

        // 7. Assignment Submissions Needing Review
        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)
            ->with(['assignment', 'studentUser', 'answers.question'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $pendingSubmissions = $submissions->filter(function ($s) {
            $val = $s->status instanceof SubmissionStatus ? $s->status->value : (string) $s->status;
            return in_array($val, ['submitted', 'in_progress'], true);
        })->values();

        // 8. Notifications Feed (Real-Time Client-Side Pagination)
        $userNotifications = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

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

        // ── Comprehensive Attendance Tracker Dataset & KPIs ──────────────────────
        $attendanceSessionsRaw = LiveSession::where('teacher_profile_id', $teacherId)
            ->with(['course.subject', 'course.gradeLevel', 'studentUser', 'studentSessions.studentUser', 'attendances'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $totalPresentCheckins = 0;
        $totalAbsentRecords = 0;
        $totalTrackedRecords = 0;
        $pendingAttendanceSessionsCount = 0;

        $attendanceSessions = $attendanceSessionsRaw->map(function ($ses) use (&$totalPresentCheckins, &$totalAbsentRecords, &$totalTrackedRecords, &$pendingAttendanceSessionsCount, $allTeacherCourseIds) {
            $studentSessions = $ses->studentSessions ?? collect();
            $meetingAttendances = $ses->attendances ?? collect();

            // Count per status
            $presentCount = $studentSessions->whereIn('attendance_status', ['present', 'late'])->count();
            $absentCount = $studentSessions->where('attendance_status', 'absent')->count();
            $excusedCount = $studentSessions->where('attendance_status', 'excused')->count();

            // If direct 1-on-1 session
            if ($ses->student_user_id && $studentSessions->isEmpty()) {
                if ($ses->attendance_status === 'present') {
                    $presentCount = 1;
                } elseif ($ses->attendance_status === 'absent') {
                    $absentCount = 1;
                }
            }

            // Calculate total cohort learners for this session
            $enrolledCount = 0;
            if ($ses->course_id) {
                $enrolledCount = CourseEnrollment::where('course_id', $ses->course_id)->count();
            } elseif ($ses->student_user_id) {
                $enrolledCount = 1;
            }

            $totalSessionLearners = max($enrolledCount, $studentSessions->count(), ($presentCount + $absentCount + $excusedCount));
            $isRecorded = ($ses->status === 'completed' || $ses->attendance_status !== null || $studentSessions->isNotEmpty());

            $sessionRate = ($presentCount + $absentCount > 0)
                ? round(($presentCount / ($presentCount + $absentCount)) * 100)
                : ($isRecorded && $presentCount > 0 ? 100 : null);

            $ses->present_count = $presentCount;
            $ses->absent_count = $absentCount;
            $ses->excused_count = $excusedCount;
            $ses->total_learners = $totalSessionLearners;
            $ses->is_recorded = $isRecorded;
            $ses->session_rate = $sessionRate;

            $totalPresentCheckins += $presentCount;
            $totalAbsentRecords += $absentCount;
            $totalTrackedRecords += ($presentCount + $absentCount);

            if (! $isRecorded && $ses->scheduled_at && $ses->scheduled_at->isPast() && ! in_array($ses->status, ['cancelled', 'cancelled_by_teacher'], true)) {
                $pendingAttendanceSessionsCount++;
            }

            return $ses;
        });

        $overallAttendanceRate = $totalTrackedRecords > 0
            ? round(($totalPresentCheckins / $totalTrackedRecords) * 100)
            : ($attendanceRate ?: 100);

        $attendanceStats = [
            'total_sessions' => $attendanceSessions->count(),
            'total_present' => $totalPresentCheckins,
            'total_absent' => $totalAbsentRecords,
            'pending_count' => $pendingAttendanceSessionsCount,
            'overall_rate' => $overallAttendanceRate,
        ];

        $gradeLevels = \App\Models\GradeLevel::orderBy('sort_order')->get();
        $initialStudentId = $request->query('student');

        // 9. Recurring Schedules for Schedules Tab
        $recurringSchedules = RecurringSchedule::where('teacher_profile_id', $teacherId)
            ->with(['course', 'sessions' => function ($q) {
                $q->orderBy('scheduled_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // 10. Student Exception Requests & Absence Excuses (for Teacher's Courses / Sessions)
        $teacherExceptions = ExceptionRequest::where(function ($q) use ($allTeacherCourseIds, $teacherId) {
            $q->whereIn('course_id', $allTeacherCourseIds)
              ->orWhereHas('liveSession', fn ($sq) => $sq->where('teacher_profile_id', $teacherId));
        })
        ->with(['studentUser', 'course.subject', 'liveSession', 'reviewer'])
        ->orderBy('created_at', 'desc')
        ->get();

        $pendingExceptionsCount = $teacherExceptions->where('status', 'pending')->count();

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
            'attendanceSessions' => $attendanceSessions,
            'attendanceStats' => $attendanceStats,
            'assignedStudents' => $assignedStudents,
            'assignments' => $assignments,
            'submissions' => $submissions,
            'pendingSubmissions' => $pendingSubmissions,
            'userNotifications' => $userNotifications,
            'unreadNotifCount' => $unreadNotifCount,
            'recurringSchedules' => $recurringSchedules,
            'exceptions' => $teacherExceptions,
            'pendingExceptionsCount' => $pendingExceptionsCount,
            // KPIs
            'todaySessionsCount' => $todaySessionsCount,
            'upcomingSessionsCount' => $upcomingSessionsCount,
            'assignedStudentsCount' => $assignedStudentsCount,
            'pendingAssignmentsCount' => $pendingAssignmentsCount,
            'submittedAssignmentsCount' => $submittedAssignmentsCount,
            'attendanceRate' => $overallAttendanceRate,
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

        // Pre-sanitize student_user_id and meeting links
        $studentInput = $request->input('student_user_id');
        if (empty($studentInput) || $studentInput === '__group__' || $studentInput === 'null' || $studentInput === '0') {
            $request->merge(['student_user_id' => null]);
        } else {
            $request->merge(['student_user_id' => (int) $studentInput]);
        }

        $linkInput = trim((string) $request->input('meeting_link'));
        if ($linkInput === '' || $linkInput === 'https://...' || $linkInput === 'http://...') {
            $request->merge(['meeting_link' => null]);
        } elseif (! str_starts_with($linkInput, 'http://') && ! str_starts_with($linkInput, 'https://')) {
            $request->merge(['meeting_link' => 'https://' . $linkInput]);
        }

        if ($request->has('day_meeting_links') && is_array($request->input('day_meeting_links'))) {
            $cleanedDayLinks = [];
            foreach ($request->input('day_meeting_links') as $k => $val) {
                $v = trim((string) $val);
                if ($v === '' || $v === 'https://...' || $v === 'http://...') {
                    continue;
                }
                if (! str_starts_with($v, 'http://') && ! str_starts_with($v, 'https://')) {
                    $v = 'https://' . $v;
                }
                $cleanedDayLinks[$k] = $v;
            }
            $request->merge(['day_meeting_links' => $cleanedDayLinks]);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'student_user_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|string',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'recurrence_type' => 'required|in:single,weekly,monthly,multi_month,yearly',
            'days_of_week' => 'nullable|array',
            'day_start_times' => 'nullable|array',
            'day_durations' => 'nullable|array',
            'day_meeting_links' => 'nullable|array',
            'monthly_pattern' => 'nullable|array',
            'meeting_link' => 'nullable|url|max:500',
        ], [
            'course_id.required' => __('Please select a course first to preview the schedule.'),
            'course_id.exists' => __('The selected course was not found or is invalid.'),
            'start_date.required' => __('Please select a start date.'),
            'end_date.required' => __('Please select an end date.'),
            'end_date.after_or_equal' => __('End date cannot be earlier than start date.'),
            'start_time.required' => __('Please specify a start time.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: You do not own this course.'], 403);
        }

        $studentUserId = ! empty($validated['student_user_id']) ? (int) $validated['student_user_id'] : null;
        if ($studentUserId) {
            $isEnrolled = \App\Models\CourseEnrollment::where('student_user_id', $studentUserId)
                ->where('course_id', $course->id)
                ->exists();

            if (! $isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => __('Selected student is not enrolled in this course.'),
                ], 422);
            }
        }

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

        // Pre-sanitize student_user_id and meeting links
        $studentInput = $request->input('student_user_id');
        if (empty($studentInput) || $studentInput === '__group__' || $studentInput === 'null' || $studentInput === '0') {
            $request->merge(['student_user_id' => null]);
        } else {
            $request->merge(['student_user_id' => (int) $studentInput]);
        }

        $linkInput = trim((string) $request->input('meeting_link'));
        if ($linkInput === '' || $linkInput === 'https://...' || $linkInput === 'http://...') {
            $request->merge(['meeting_link' => null]);
        } elseif (! str_starts_with($linkInput, 'http://') && ! str_starts_with($linkInput, 'https://')) {
            $request->merge(['meeting_link' => 'https://' . $linkInput]);
        }

        if ($request->has('day_meeting_links') && is_array($request->input('day_meeting_links'))) {
            $cleanedDayLinks = [];
            foreach ($request->input('day_meeting_links') as $k => $val) {
                $v = trim((string) $val);
                if ($v === '' || $v === 'https://...' || $v === 'http://...') {
                    continue;
                }
                if (! str_starts_with($v, 'http://') && ! str_starts_with($v, 'https://')) {
                    $v = 'https://' . $v;
                }
                $cleanedDayLinks[$k] = $v;
            }
            $request->merge(['day_meeting_links' => $cleanedDayLinks]);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'student_user_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|string',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'recurrence_type' => 'required|in:single,weekly,monthly,multi_month,yearly',
            'days_of_week' => 'nullable|array',
            'day_start_times' => 'nullable|array',
            'day_durations' => 'nullable|array',
            'day_meeting_links' => 'nullable|array',
            'monthly_pattern' => 'nullable|array',
            'meeting_link' => 'nullable|url|max:500',
            'meeting_platform' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ], [
            'title.required' => __('Please provide a schedule title.'),
            'course_id.required' => __('Please select a course first.'),
            'course_id.exists' => __('The selected course was not found or is invalid.'),
            'start_date.required' => __('Please select a start date.'),
            'end_date.required' => __('Please select an end date.'),
            'end_date.after_or_equal' => __('End date cannot be earlier than start date.'),
            'start_time.required' => __('Please specify a start time.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: You do not own this course.'], 403);
        }

        $studentUserId = ! empty($validated['student_user_id']) ? (int) $validated['student_user_id'] : null;
        if ($studentUserId) {
            $isEnrolled = \App\Models\CourseEnrollment::where('student_user_id', $studentUserId)
                ->where('course_id', $course->id)
                ->exists();

            if (! $isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => __('Selected student is not enrolled in this course.'),
                ], 422);
            }
        }

        $data = array_merge($validated, [
            'teacher_profile_id' => $teacherProfile->id,
        ]);

        try {
            $schedule = $service->createSchedule($data, auth()->user());

            // Dispatch instant real-time notification to student if 1-to-1
            if ($studentUserId) {
                try {
                    $fcmService = app(\App\Services\Notification\FcmNotificationService::class);
                    $targetStudent = User::find($studentUserId);
                    if ($targetStudent) {
                        $fcmService->sendNotification(
                            $targetStudent,
                            'session_scheduled',
                            __('New 1-to-1 Recurring Schedule Created'),
                            __('Teacher :teacher created a recurring schedule ":title" with :count sessions.', [
                                'teacher' => $teacherProfile->user?->name ?: __('Instructor'),
                                'title' => $schedule->title,
                                'count' => $schedule->sessions()->count(),
                            ]),
                            route('student-portal', ['tab' => 'sessions'])
                        );
                    }
                } catch (\Exception $e) {
                    // Non-blocking notification
                }
            }

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
     * AJAX Endpoint: Update Recurring Schedule Rule
     */
    public function updateRecurringSchedule(Request $request, int $id, RecurringScheduleService $service): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $schedule = RecurringSchedule::findOrFail($id);
        if ((int) $schedule->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'days_of_week' => 'nullable|array',
            'start_time' => 'required|string',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'meeting_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $service->updateEntireSchedule($schedule, $validated, auth()->user());

        return response()->json([
            'success' => true,
            'message' => __('Recurring schedule updated successfully!'),
        ]);
    }

    /**
     * AJAX Endpoint: Delete Recurring Schedule & Uncompleted Sessions
     */
    public function deleteRecurringSchedule(Request $request, int $id, RecurringScheduleService $service): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $schedule = RecurringSchedule::findOrFail($id);
        if ((int) $schedule->teacher_profile_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $service->deleteSchedule($schedule, auth()->user());

        return response()->json([
            'success' => true,
            'message' => __('Recurring schedule and uncompleted sessions deleted successfully!'),
        ]);
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
            'student_user_id' => 'nullable|exists:users,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'meeting_platform' => 'nullable|string|max:50',
            'meeting_link' => 'nullable|url|max:500',
            'is_free_demo' => 'nullable|boolean',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: You do not own this course.'], 403);
        }

        $studentUserId = ! empty($validated['student_user_id']) ? (int) $validated['student_user_id'] : null;

        // If 1-to-1 session, strictly validate that selected student is enrolled in the selected course
        if ($studentUserId) {
            $isEnrolled = CourseEnrollment::where('student_user_id', $studentUserId)
                ->where('course_id', $course->id)
                ->exists();

            if (! $isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => __('The selected student is not enrolled in this course.'),
                    'errors' => [
                        'course_id' => [__('The selected student is not enrolled in this course.')],
                    ],
                ], 422);
            }
        }

        $scheduledAt = Carbon::parse($validated['scheduled_at']);
        $duration = (int) ($validated['duration_minutes'] ?? 60);
        $endAt = $scheduledAt->copy()->addMinutes($duration);
        $subjectId = $course->subject_id ?: ($teacherProfile->subjects()->first()?->id ?? null);

        $liveSession = DB::transaction(function () use ($validated, $studentUserId, $teacherProfile, $course, $subjectId, $scheduledAt, $endAt, $duration) {
            $session = LiveSession::create([
                'title' => $validated['title'],
                'student_user_id' => $studentUserId,
                'teacher_profile_id' => $teacherProfile->id,
                'course_id' => $course->id,
                'subject_id' => $subjectId,
                'scheduled_at' => $scheduledAt,
                'start_at' => $scheduledAt,
                'end_at' => $endAt,
                'duration_minutes' => $duration,
                'meeting_platform' => $validated['meeting_platform'] ?? 'agora',
                'meeting_link' => $validated['meeting_link'] ?? null,
                'status' => 'scheduled',
                'lifecycle_state' => 'scheduled',
                'is_free_demo' => (bool) ($validated['is_free_demo'] ?? false),
            ]);

            if ($studentUserId) {
                \App\Models\StudentSession::updateOrCreate([
                    'student_user_id' => $studentUserId,
                    'live_session_id' => $session->id,
                ], [
                    'session_status' => 'scheduled',
                ]);
            }

            return $session;
        });

        // Dispatch instant real-time notification to student(s)
        try {
            $fcmService = app(\App\Services\Notification\FcmNotificationService::class);
            if ($studentUserId) {
                $targetStudent = User::find($studentUserId);
                if ($targetStudent) {
                    $fcmService->sendNotification(
                        $targetStudent,
                        'session_scheduled',
                        __('New 1-to-1 Live Session Scheduled'),
                        __('Teacher :teacher scheduled a session ":title" on :date.', [
                            'teacher' => $teacherProfile->user?->name ?: __('Instructor'),
                            'title' => $liveSession->title,
                            'date' => $scheduledAt->format('Y-m-d h:i A'),
                        ]),
                        route('student-portal', ['tab' => 'sessions'])
                    );
                }
            } elseif ($course) {
                $enrolledStudentIds = CourseEnrollment::where('course_id', $course->id)->pluck('student_user_id')->toArray();
                $enrolledStudents = User::whereIn('id', $enrolledStudentIds)->get();
                foreach ($enrolledStudents as $sUser) {
                    $fcmService->sendNotification(
                        $sUser,
                        'session_scheduled',
                        __('New Live Session Scheduled'),
                        __('A new live session ":title" was scheduled for course :course.', [
                            'title' => $liveSession->title,
                            'course' => $course->title,
                        ]),
                        route('student-portal', ['tab' => 'sessions'])
                    );
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[Session RealTime Notification] ' . $e->getMessage());
        }

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
            'student_user_id' => 'nullable',
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
            'course_id' => 'nullable|required_without:student_user_id|exists:courses,id',
            'student_user_id' => 'nullable|required_without:course_id|exists:users,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:300',
            'meeting_link' => 'nullable|url|max:500',
            'is_free_demo' => 'nullable|boolean',
        ]);

        $course = null;
        if (! empty($validated['course_id'])) {
            $course = Course::findOrFail($validated['course_id']);
            if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized course ownership'], 403);
            }
        }

        $studentUserId = ! empty($validated['student_user_id']) ? (int) $validated['student_user_id'] : null;

        // If 1-to-1 session, strictly validate that selected student is enrolled in the selected course
        if ($studentUserId && $course) {
            $isEnrolled = CourseEnrollment::where('student_user_id', $studentUserId)
                ->where('course_id', $course->id)
                ->exists();

            if (! $isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => __('The selected student is not enrolled in this course.'),
                    'errors' => [
                        'course_id' => [__('The selected student is not enrolled in this course.')],
                    ],
                ], 422);
            }
        }

        $scheduledAt = Carbon::parse($validated['scheduled_at']);
        $duration = (int) ($validated['duration_minutes'] ?? $session->duration_minutes ?? 60);
        $endAt = $scheduledAt->copy()->addMinutes($duration);

        DB::transaction(function () use ($session, $validated, $course, $studentUserId, $teacherProfile, $scheduledAt, $endAt, $duration) {
            $session->update([
                'title' => $validated['title'],
                'course_id' => $course?->id,
                'student_user_id' => $studentUserId,
                'subject_id' => $course ? $course->subject_id : ($session->subject_id ?? $teacherProfile->subjects()->first()?->id),
                'scheduled_at' => $scheduledAt,
                'start_at' => $scheduledAt,
                'end_at' => $endAt,
                'duration_minutes' => $duration,
                'meeting_link' => $validated['meeting_link'] ?? null,
                'is_free_demo' => (bool) ($validated['is_free_demo'] ?? false),
            ]);

            if ($studentUserId) {
                \App\Models\StudentSession::updateOrCreate([
                    'student_user_id' => $studentUserId,
                    'live_session_id' => $session->id,
                ], [
                    'session_status' => 'scheduled',
                ]);
            }
        });

        // Dispatch instant real-time notification to student
        if ($studentUserId) {
            try {
                $targetStudent = User::find($studentUserId);
                if ($targetStudent) {
                    app(\App\Services\Notification\FcmNotificationService::class)->sendNotification(
                        $targetStudent,
                        'session_updated',
                        __('Session Details Updated'),
                        __('Teacher updated session ":title" scheduled on :date.', [
                            'title' => $session->title,
                            'date' => $scheduledAt->format('Y-m-d h:i A'),
                        ]),
                        route('student-portal', ['tab' => 'sessions'])
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[Session Update Notification] ' . $e->getMessage());
            }
        }

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
     * AJAX Endpoint: Delete Live Session
     */
    public function deleteSession(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $session = LiveSession::findOrFail($id);

        $isTeacherOwner = (int) $session->teacher_profile_id === (int) $teacherProfile->id
            || ($session->course && (int) $session->course->teacher_id === (int) $teacherProfile->id);

        if (! auth()->user()->isAdmin() && ! $isTeacherOwner) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $deletedStudentUserId = $session->student_user_id;
        $deletedSessionTitle = $session->title;

        DB::transaction(function () use ($session) {
            \App\Models\StudentSession::where('live_session_id', $session->id)->delete();
            \App\Models\MeetingAttendance::where('live_session_id', $session->id)->delete();
            $session->delete();
        });

        if ($deletedStudentUserId) {
            try {
                $targetStudent = User::find($deletedStudentUserId);
                if ($targetStudent) {
                    app(\App\Services\Notification\FcmNotificationService::class)->sendNotification(
                        $targetStudent,
                        'session_cancelled',
                        __('Live Session Removed'),
                        __('The session ":title" has been removed by the instructor.', ['title' => $deletedSessionTitle]),
                        route('student-portal', ['tab' => 'sessions'])
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[Session Delete Notification] ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('Live session deleted successfully!'),
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

        try {
            $start = $request->query('start') ? Carbon::parse($request->query('start')) : now()->startOfMonth()->subDays(7);
        } catch (\Exception $e) {
            $start = now()->startOfMonth()->subDays(7);
        }

        try {
            $end = $request->query('end') ? Carbon::parse($request->query('end')) : now()->endOfMonth()->addDays(7);
        } catch (\Exception $e) {
            $end = now()->endOfMonth()->addDays(7);
        }

        $sessions = LiveSession::where('teacher_profile_id', $teacherProfile->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('scheduled_at', [$start, $end])
                    ->orWhereBetween('start_at', [$start, $end]);
            })
            ->with(['course.subject', 'course.gradeLevel', 'studentUser', 'recurringSchedule'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $events = $sessions->map(function ($s) {
            $statusColors = [
                'completed' => '#10B981',
                'in_progress' => '#06B6D4',
                'ready' => '#3B82F6',
                'scheduled' => '#6366F1',
                'cancelled' => '#EF4444',
                'cancelled_by_teacher' => '#EF4444',
                'rescheduled' => '#F59E0B',
            ];

            $color = $statusColors[$s->status] ?? '#64748B';
            $startTime = $s->effective_start_at ?: $s->scheduled_at;
            $duration = (int) ($s->duration_minutes ?? 60);
            $endTime = $s->effective_end_at ?: ($s->end_at ?: ($startTime ? $startTime->copy()->addMinutes($duration) : null));

            return [
                'id' => $s->id,
                'title' => $s->title ?? __('Session'),
                'start' => $startTime ? $startTime->toIso8601String() : null,
                'end' => $endTime ? $endTime->toIso8601String() : null,
                'date_str' => $startTime ? $startTime->format('Y-m-d') : '',
                'start_time_str' => $startTime ? $startTime->format('H:i') : '10:00',
                'end_time_str' => $endTime ? $endTime->format('H:i') : '11:00',
                'time_formatted' => $startTime && $endTime ? ($startTime->format('H:i') . ' - ' . $endTime->format('H:i')) : '',
                'duration_minutes' => $duration,
                'course' => $s->course?->title ?: __('N/A'),
                'course_id' => $s->course_id,
                'subject' => $s->course?->subject?->name ?: '',
                'grade_level' => $s->course?->gradeLevel?->name ?: '',
                'student_name' => $s->studentUser?->name ?: __('General Cohort'),
                'student_user_id' => $s->student_user_id,
                'status' => $s->status ?? 'scheduled',
                'meeting_link' => $s->meeting_link,
                'is_override' => (bool) $s->is_override,
                'is_recurring' => ! empty($s->recurring_schedule_id),
                'recurring_schedule_id' => $s->recurring_schedule_id,
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
            'questions.*.image' => 'nullable|image|max:5120',
            'questions.*.option_images' => 'nullable|array',
            'questions.*.option_images.*' => 'nullable|image|max:5120',
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

                // Handle Question Diagram Image
                $questionImagePath = null;
                if ($request->hasFile("questions.{$qIdx}.image")) {
                    $questionImagePath = $request->file("questions.{$qIdx}.image")->store('assignment-questions', 'public');
                }

                $question = \App\Models\AssignmentQuestion::create([
                    'assignment_id' => $assignment->id,
                    'question_text' => $qData['question_text'],
                    'image_path' => $questionImagePath,
                    'question_type' => $questionImagePath ? 'both' : 'text',
                    'points' => $qPoints,
                    'sort_order' => $qIdx + 1,
                    'is_multiple_choice' => false,
                ]);

                if (! empty($qData['options']) && is_array($qData['options'])) {
                    foreach ($qData['options'] as $optIdx => $optText) {
                        $hasImage = $request->hasFile("questions.{$qIdx}.option_images.{$optIdx}");
                        $text = trim((string) $optText);

                        // Save if either text or image exists
                        if ($text === '' && ! $hasImage) {
                            continue;
                        }

                        // Handle Option Choice Image
                        $optImagePath = null;
                        if ($hasImage) {
                            $optImagePath = $request->file("questions.{$qIdx}.option_images.{$optIdx}")->store('assignment-options', 'public');
                        }

                        \App\Models\AssignmentQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $text !== '' ? $text : __('Option') . ' ' . chr(65 + $optIdx),
                            'image_path' => $optImagePath,
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
     * AJAX Endpoint: Update Assignment & Interactive Questions
     */
    public function updateAssignment(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $assignment = Assignment::with(['questions.options'])->findOrFail($id);

        $isTeacherOwner = (int) $assignment->teacher_profile_id === (int) $teacherProfile->id
            || ($assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id)
            || ($assignment->liveSession && (int) $assignment->liveSession->teacher_profile_id === (int) $teacherProfile->id);

        if (! auth()->user()->isAdmin() && ! $isTeacherOwner) {
            return response()->json(['success' => false, 'message' => __('Unauthorized access to assignment.')], 403);
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
            'questions.*.id' => 'nullable|integer',
            'questions.*.question_text' => 'required_with:questions|string|max:1000',
            'questions.*.points' => 'nullable|numeric|min:0.1',
            'questions.*.correct_index' => 'nullable|integer|min:0|max:10',
            'questions.*.options' => 'nullable|array|min:2',
            'questions.*.options.*' => 'nullable|string|max:500',
            'questions.*.existing_image' => 'nullable|string',
            'questions.*.remove_image' => 'nullable|string',
            'questions.*.image' => 'nullable|image|max:5120',
            'questions.*.existing_option_images' => 'nullable|array',
            'questions.*.existing_option_images.*' => 'nullable|string',
            'questions.*.remove_option_images' => 'nullable|array',
            'questions.*.remove_option_images.*' => 'nullable|string',
            'questions.*.option_images' => 'nullable|array',
            'questions.*.option_images.*' => 'nullable|image|max:5120',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ((int) $course->teacher_id !== (int) $teacherProfile->id && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized course ownership'], 403);
        }

        DB::transaction(function () use ($assignment, $validated, $course, $request) {
            $assignment->update([
                'course_id' => $course->id,
                'live_session_id' => $validated['live_session_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'duration_minutes' => (int) ($validated['duration_minutes'] ?? 30),
                'due_at' => Carbon::parse($validated['due_at']),
                'passing_score' => (float) ($validated['passing_score'] ?? 70.0),
            ]);

            $submittedQuestions = $validated['questions'] ?? [];
            $keepQuestionIds = [];

            foreach ($submittedQuestions as $qIdx => $qData) {
                if (empty($qData['question_text'])) continue;

                $qPoints = (float) ($qData['points'] ?? 1.0);
                $correctIndex = isset($qData['correct_index']) ? (int) $qData['correct_index'] : 0;
                $qId = !empty($qData['id']) ? (int) $qData['id'] : null;

                // Handle question image upload / removal / keep
                $existingImage = $qData['existing_image'] ?? null;
                $removeImage = !empty($qData['remove_image']) && $qData['remove_image'] === '1';
                $questionImagePath = $existingImage;

                if ($removeImage && $existingImage) {
                    Storage::disk('public')->delete($existingImage);
                    $questionImagePath = null;
                }

                if ($request->hasFile("questions.{$qIdx}.image")) {
                    if ($existingImage) {
                        Storage::disk('public')->delete($existingImage);
                    }
                    $questionImagePath = $request->file("questions.{$qIdx}.image")->store('assignment-questions', 'public');
                }

                if ($qId) {
                    $question = \App\Models\AssignmentQuestion::where('assignment_id', $assignment->id)->find($qId);
                    if ($question) {
                        $question->update([
                            'question_text' => $qData['question_text'],
                            'image_path' => $questionImagePath,
                            'question_type' => $questionImagePath ? 'both' : 'text',
                            'points' => $qPoints,
                            'sort_order' => $qIdx + 1,
                        ]);
                    } else {
                        $question = \App\Models\AssignmentQuestion::create([
                            'assignment_id' => $assignment->id,
                            'question_text' => $qData['question_text'],
                            'image_path' => $questionImagePath,
                            'question_type' => $questionImagePath ? 'both' : 'text',
                            'points' => $qPoints,
                            'sort_order' => $qIdx + 1,
                            'is_multiple_choice' => false,
                        ]);
                    }
                } else {
                    $question = \App\Models\AssignmentQuestion::create([
                        'assignment_id' => $assignment->id,
                        'question_text' => $qData['question_text'],
                        'image_path' => $questionImagePath,
                        'question_type' => $questionImagePath ? 'both' : 'text',
                        'points' => $qPoints,
                        'sort_order' => $qIdx + 1,
                        'is_multiple_choice' => false,
                    ]);
                }

                $keepQuestionIds[] = $question->id;

                // Update options: delete old and re-create fresh with correct sort, flag, and image
                $question->options()->delete();
                if (! empty($qData['options']) && is_array($qData['options'])) {
                    foreach ($qData['options'] as $optIdx => $optText) {
                        $existingOptImg = $qData['existing_option_images'][$optIdx] ?? null;
                        $removeOptImg = !empty($qData['remove_option_images'][$optIdx]) && $qData['remove_option_images'][$optIdx] === '1';
                        $hasNewImage = $request->hasFile("questions.{$qIdx}.option_images.{$optIdx}");
                        $optImagePath = $existingOptImg;
                        $text = trim((string) $optText);

                        if ($removeOptImg && $existingOptImg) {
                            Storage::disk('public')->delete($existingOptImg);
                            $optImagePath = null;
                        }

                        if ($hasNewImage) {
                            if ($existingOptImg) {
                                Storage::disk('public')->delete($existingOptImg);
                            }
                            $optImagePath = $request->file("questions.{$qIdx}.option_images.{$optIdx}")->store('assignment-options', 'public');
                        }

                        // Save if either text or image exists
                        if ($text === '' && ! $optImagePath) {
                            continue;
                        }

                        \App\Models\AssignmentQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $text !== '' ? $text : __('Option') . ' ' . chr(65 + $optIdx),
                            'image_path' => $optImagePath,
                            'sort_order' => $optIdx + 1,
                            'is_correct' => ($optIdx === $correctIndex),
                        ]);
                    }
                }
            }

            // Delete questions removed during edit
            $deletedQuestions = \App\Models\AssignmentQuestion::where('assignment_id', $assignment->id)
                ->whereNotIn('id', $keepQuestionIds)
                ->get();

            foreach ($deletedQuestions as $delQ) {
                if ($delQ->image_path) {
                    Storage::disk('public')->delete($delQ->image_path);
                }
                foreach ($delQ->options as $delOpt) {
                    if ($delOpt->image_path) {
                        Storage::disk('public')->delete($delOpt->image_path);
                    }
                }
                $delQ->options()->delete();
                $delQ->delete();
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('Assignment updated successfully!'),
            'assignment_id' => $assignment->id,
        ]);
    }

    /**
     * AJAX Endpoint: Delete Assignment & All Associated Questions
     */
    public function deleteAssignment(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $assignment = Assignment::findOrFail($id);

        $isTeacherOwner = (int) $assignment->teacher_profile_id === (int) $teacherProfile->id
            || ($assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id)
            || ($assignment->liveSession && (int) $assignment->liveSession->teacher_profile_id === (int) $teacherProfile->id);

        if (! auth()->user()->isAdmin() && ! $isTeacherOwner) {
            return response()->json(['success' => false, 'message' => __('Unauthorized access to assignment.')], 403);
        }

        DB::transaction(function () use ($assignment) {
            // Delete submissions and answers
            foreach ($assignment->submissions as $sub) {
                $sub->answers()->delete();
                $sub->delete();
            }

            // Delete questions and options
            foreach ($assignment->questions as $q) {
                $q->options()->delete();
                $q->delete();
            }

            $assignment->delete();
        });

        return response()->json([
            'success' => true,
            'message' => __('Assignment deleted successfully!'),
        ]);
    }

    /**
     * AJAX Endpoint: Grade / Review Assignment Submission
     */
    public function reviewSubmission(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $submission = AssignmentSubmission::with(['assignment.session', 'assignment.course', 'assignment.liveSession', 'enrollment'])->findOrFail($id);

        $assignment = $submission->assignment;
        $isTeacherOwner = $assignment && (
            (int) $assignment->teacher_profile_id === (int) $teacherProfile->id
            || ($assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id)
            || ($assignment->liveSession && (int) $assignment->liveSession->teacher_profile_id === (int) $teacherProfile->id)
        );

        if (! auth()->user()->isAdmin() && ! $isTeacherOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'evaluation_notes' => 'nullable|string|max:1000',
        ]);

        $score = (float) $validated['score'];
        $assignment = $submission->assignment;
        $passingScore = (float) ($assignment->passing_score ?? $assignment->passing_grade ?? 70.0);
        $isPassed = $score >= $passingScore;

        $submission->update([
            'score' => $score,
            'grade' => $score,
            'percentage' => $score,
            'passing_score' => $passingScore,
            'evaluation_notes' => $validated['evaluation_notes'] ?? null,
            'teacher_notes' => $validated['evaluation_notes'] ?? null,
            'status' => SubmissionStatus::REVIEWED->value,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        if (! empty($validated['evaluation_notes'])) {
            \App\Models\StudentEducationalNote::updateOrCreate(
                [
                    'student_user_id' => $submission->student_user_id,
                    'teacher_profile_id' => $teacherProfile->id,
                    'note' => $validated['evaluation_notes'],
                ],
                [
                    'category' => 'homework',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Sync relational StudentSession state if tied to a live session
        if ($assignment->live_session_id) {
            \App\Models\StudentSession::updateOrCreate(
                [
                    'student_user_id' => $submission->student_user_id,
                    'live_session_id' => $assignment->live_session_id,
                ],
                [
                    'assignment_status' => $isPassed ? 'passed' : 'failed',
                    'assignment_score' => $score,
                    'session_status' => $isPassed ? 'completed' : 'active',
                    'completed_at' => $isPassed ? now() : null,
                ]
            );
        }

        // If passed and assigned to a curriculum course session, unlock next session
        if ($isPassed && $submission->enrollment && $assignment->session) {
            try {
                app(\App\Actions\Course\UnlockNextSessionAction::class)->execute(
                    $submission->enrollment,
                    $assignment->session
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[TeacherPortal] Unlock next session exception: ' . $e->getMessage());
            }
        }

        // Notify Student
        app(\App\Services\Notification\FcmNotificationService::class)->notifyStudentSubmissionGraded($submission);

        return response()->json([
            'success' => true,
            'message' => __('Submission graded and evaluation feedback sent to student!'),
            'is_passed' => $isPassed,
            'score' => $score,
            'grade' => $score,
        ]);
    }

    /**
     * AJAX Endpoint: Mark Session Attendance
     */
    public function markAttendance(Request $request, int $sessionId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        $session = LiveSession::with('course')->findOrFail($sessionId);
        $isTeacherSession = (int) $session->teacher_profile_id === (int) $teacherProfile->id;
        $isTeacherCourse = $session->course && (int) $session->course->teacher_id === (int) $teacherProfile->id;

        if (! $isTeacherSession && ! $isTeacherCourse && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'attendance' => 'nullable|array',
            'attendance.*.student_user_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
        ]);

        $attendanceList = $validated['attendance'] ?? [];
        $primaryStatus = null;
        $hasPresent = false;
        $presentCount = 0;
        $lateCount = 0;
        $absentCount = 0;
        $excusedCount = 0;

        foreach ($attendanceList as $record) {
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

            // Sync with MeetingAttendance
            try {
                \App\Models\MeetingAttendance::updateOrCreate(
                    [
                        'live_session_id' => $session->id,
                        'student_user_id' => $studentUserId,
                    ],
                    [
                        'status' => $status,
                        'joined_at' => now(),
                        'last_seen_at' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // Keep resilient
            }

            if ($status === 'present') {
                $hasPresent = true;
                $presentCount++;
            } elseif ($status === 'late') {
                $hasPresent = true;
                $lateCount++;
            } elseif ($status === 'absent') {
                $absentCount++;
                $studentUser = User::find($studentUserId);
                if ($studentUser) {
                    try {
                        app(\App\Services\Notification\FcmNotificationService::class)->notifyTeacherStudentAbsent($session, $studentUser);
                    } catch (\Throwable $e) {}
                }
            } elseif ($status === 'excused') {
                $excusedCount++;
            }

            if ((int) $session->student_user_id === $studentUserId) {
                $primaryStatus = in_array($status, ['present', 'absent', 'excused'], true) ? $status : 'present';
            }
        }

        $sessionAttendanceStatus = $session->student_user_id
            ? ($primaryStatus ?: 'present')
            : ($hasPresent ? 'present' : ($absentCount > 0 ? 'absent' : 'present'));

        $session->update([
            'attendance_status' => $sessionAttendanceStatus,
            'status' => 'completed',
            'lifecycle_state' => 'completed',
        ]);

        $totalRecorded = count($attendanceList);
        $rate = ($presentCount + $lateCount + $absentCount > 0)
            ? round((($presentCount + $lateCount) / ($presentCount + $lateCount + $absentCount)) * 100)
            : 100;

        return response()->json([
            'success' => true,
            'message' => __('Attendance marked and saved successfully!'),
            'session_id' => $session->id,
            'stats' => [
                'total' => $totalRecorded,
                'present' => $presentCount,
                'late' => $lateCount,
                'absent' => $absentCount,
                'excused' => $excusedCount,
                'attended_total' => ($presentCount + $lateCount),
                'rate' => $rate,
                'status_text' => $sessionAttendanceStatus,
                'is_recorded' => true,
            ],
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

        $session = LiveSession::with(['course.subject', 'course.gradeLevel', 'studentUser', 'recurringSchedule'])->findOrFail($sessionId);

        $isTeacherSession = (int) $session->teacher_profile_id === (int) $teacherProfile->id;
        $isTeacherCourse = $session->course && (int) $session->course->teacher_id === (int) $teacherProfile->id;

        if (! $isTeacherSession && ! $isTeacherCourse && ! auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => __('Unauthorized: You do not own this teaching session.')], 403);
        }

        // Collect registered student user IDs for this specific session / course
        $studentUserIds = collect();

        if ($session->student_user_id) {
            // Strictly 1-to-1 Session: Roster MUST contain solely the assigned student
            $studentUserIds->push((int) $session->student_user_id);
        } elseif ($session->recurringSchedule?->student_user_id) {
            // 1-to-1 Recurring Schedule instance
            $studentUserIds->push((int) $session->recurringSchedule->student_user_id);
        } else {
            // Group Session: All enrolled students in the course
            if ($session->course_id) {
                $enrolledIds = CourseEnrollment::where('course_id', $session->course_id)
                    ->pluck('student_user_id')
                    ->filter();
                $studentUserIds = $studentUserIds->merge($enrolledIds);
            }

            // Also check any already recorded student_sessions for this live session
            $existingStudentSessionIds = StudentSession::where('live_session_id', $session->id)
                ->pluck('student_user_id')
                ->filter();
            $studentUserIds = $studentUserIds->merge($existingStudentSessionIds);

            // Check meeting attendances
            $existingMeetingAttendanceIds = \App\Models\MeetingAttendance::where('live_session_id', $session->id)
                ->pluck('student_user_id')
                ->filter();
            $studentUserIds = $studentUserIds->merge($existingMeetingAttendanceIds)->unique()->values();

            // Fallback: If no students explicitly enrolled in this course yet, include teacher's active students
            if ($studentUserIds->isEmpty()) {
                $allTeacherCourseIds = Course::where('teacher_id', $teacherProfile->id)->pluck('id')->filter()->toArray();
                $fallbackIds = CourseEnrollment::whereIn('course_id', $allTeacherCourseIds)->pluck('student_user_id')->filter();
                if ($fallbackIds->isNotEmpty()) {
                    $studentUserIds = $fallbackIds->unique()->values();
                } else {
                    // If still empty, check teacher's overall students
                    $directIds = LiveSession::where('teacher_profile_id', $teacherProfile->id)->whereNotNull('student_user_id')->pluck('student_user_id')->filter();
                    $studentUserIds = $directIds->unique()->values();
                }
            }
        }

        // If still empty
        if ($studentUserIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title ?: 'Live Session',
                    'course_title' => $session->course?->title ?: '',
                    'subject_name' => $session->course?->subject?->name ?: ($session->subject?->name ?: ''),
                    'date' => $session->effective_start_at ? $session->effective_start_at->format('Y-m-d h:i A') : '',
                    'duration' => $session->duration_minutes ?: 60,
                    'status' => $session->status,
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
            ->latest('created_at')
            ->get()
            ->map(function ($st) use ($existingRecords, $session) {
                $defaultStatus = $session->status === 'completed' ? 'present' : 'present';
                $status = $existingRecords[$st->user_id] ?? ($session->student_user_id === $st->user_id ? ($session->attendance_status ?: 'present') : $defaultStatus);
                
                return [
                    'id' => $st->user_id,
                    'student_code' => 'STU-' . str_pad((string) $st->user_id, 5, '0', STR_PAD_LEFT),
                    'name' => $st->user?->name ?: 'Student',
                    'email' => $st->user?->email ?: '',
                    'school' => $st->school_name ?: 'Elite Academy',
                    'grade' => $st->gradeLevel?->name ?: '',
                    'status' => in_array($status, ['present', 'late', 'excused', 'absent'], true) ? $status : 'present',
                ];
            });

        return response()->json([
            'success' => true,
            'session' => [
                'id' => $session->id,
                'title' => $session->title ?: 'Live Session',
                'course_title' => $session->course?->title ?: '',
                'subject_name' => $session->course?->subject?->name ?: ($session->subject?->name ?: ''),
                'date' => $session->effective_start_at ? $session->effective_start_at->format('Y-m-d h:i A') : '',
                'duration' => $session->duration_minutes ?: 60,
                'status' => $session->status,
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
        $submission = AssignmentSubmission::with(['assignment.questions.options', 'assignment.course', 'assignment.liveSession', 'studentUser', 'answers'])->findOrFail($submissionId);

        $assignment = $submission->assignment;
        $isTeacherOwner = $assignment && (
            (int) $assignment->teacher_profile_id === (int) $teacherProfile->id
            || ($assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id)
            || ($assignment->liveSession && (int) $assignment->liveSession->teacher_profile_id === (int) $teacherProfile->id)
        );

        if (! auth()->user()->isAdmin() && ! $isTeacherOwner) {
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
     * AJAX Endpoint: Get Full Assignment Details, Questions & Submissions for Teacher
     */
    public function getAssignmentDetails(Request $request, int $id): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $assignment = Assignment::with([
            'course.subject',
            'liveSession',
            'questions.options',
            'submissions.studentUser',
            'submissions.answers',
        ])->findOrFail($id);

        $isTeacherOwner = (int) $assignment->teacher_profile_id === (int) $teacherProfile->id
            || ($assignment->course && (int) $assignment->course->teacher_id === (int) $teacherProfile->id)
            || ($assignment->liveSession && (int) $assignment->liveSession->teacher_profile_id === (int) $teacherProfile->id);

        if (! auth()->user()->isAdmin() && ! $isTeacherOwner) {
            return response()->json(['success' => false, 'message' => __('Unauthorized access to assignment.')], 403);
        }

        $submissions = $assignment->submissions->map(function ($sub) use ($assignment) {
            $statusVal = $sub->status instanceof \App\Enums\SubmissionStatus ? $sub->status->value : (is_object($sub->status) ? ($sub->status->value ?? '') : (string) $sub->status);
            $passingScore = (float) ($assignment->passing_score ?: 70.0);
            $score = $sub->score !== null ? (float) $sub->score : null;
            $isPassed = $score !== null ? ($score >= $passingScore) : null;

            return [
                'id' => $sub->id,
                'student_name' => $sub->studentUser?->name ?: __('Student'),
                'student_email' => $sub->studentUser?->email ?: '',
                'score' => $score,
                'status' => $statusVal,
                'is_passed' => $isPassed,
                'submitted_at' => $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : ($sub->created_at ? $sub->created_at->format('Y-m-d H:i') : ''),
                'evaluation_notes' => $sub->evaluation_notes,
                'answers_count' => $sub->answers->count(),
            ];
        });

        $gradedSubmissions = $submissions->filter(fn ($s) => $s['score'] !== null);
        $avgScore = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('score'), 1) : null;
        $passedCount = $submissions->where('is_passed', true)->count();
        $passRate = $gradedSubmissions->count() > 0 ? round(($passedCount / $gradedSubmissions->count()) * 100) : 0;

        $questionsData = $assignment->questions->map(function ($q, $idx) {
            return [
                'id' => $q->id,
                'number' => $idx + 1,
                'question_text' => $q->question_text,
                'points' => (float) $q->points,
                'image_path' => $q->image_path,
                'image_url' => $q->image_path ? asset('storage/' . $q->image_path) : null,
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'option_text' => $opt->option_text,
                        'is_correct' => (bool) $opt->is_correct,
                        'explanation' => $opt->explanation,
                        'image_path' => $opt->image_path,
                        'image_url' => $opt->image_path ? asset('storage/' . $opt->image_path) : null,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description ?: '',
                'course_title' => $assignment->course?->title ?: __('Course'),
                'subject_name' => $assignment->course?->subject?->name ?: '',
                'live_session_title' => $assignment->liveSession?->title ?: '',
                'due_at' => $assignment->effective_due_at ? $assignment->effective_due_at->format('Y-m-d H:i') : null,
                'due_at_human' => $assignment->effective_due_at ? $assignment->effective_due_at->diffForHumans() : __('No deadline'),
                'duration_minutes' => $assignment->duration_minutes ?: 30,
                'passing_score' => (float) ($assignment->passing_score ?: 70.0),
                'total_questions' => $assignment->questions->count(),
                'status' => $assignment->status ?: 'published',
            ],
            'stats' => [
                'total_submissions' => $submissions->count(),
                'graded_submissions' => $gradedSubmissions->count(),
                'pending_submissions' => $submissions->whereIn('status', ['submitted', 'in_progress'])->count(),
                'avg_score' => $avgScore,
                'pass_rate' => $passRate,
                'passed_count' => $passedCount,
            ],
            'questions' => $questionsData,
            'submissions' => $submissions,
        ]);
    }

    /**
     * AJAX Endpoint: Get Students Enrolled in a Course (for Schedules Tab)
     */
    public function getStudentsByCourse(Request $request, $course_id = null): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $courseId = (int) ($course_id 
            ?: $request->route('course_id') 
            ?: $request->route('course') 
            ?: $request->input('course_id'));

        if (! $courseId) {
            return response()->json(['success' => false, 'message' => 'course_id required'], 422);
        }

        $course = Course::where('id', $courseId)
            ->where(function ($q) use ($teacherProfile) {
                $q->where('teacher_id', $teacherProfile->id)
                  ->orWhere(fn ($q2) => $q2->whereNotNull('id')->when(auth()->user()?->isAdmin(), fn ($q3) => $q3));
            })
            ->first();

        if (! $course && ! auth()->user()?->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Course not found or unauthorized'], 403);
        }

        $students = \App\Models\CourseEnrollment::where('course_id', $courseId)
            ->with(['studentUser'])
            ->get()
            ->map(function ($enrollment) {
                $user = $enrollment->studentUser;
                if (! $user) return null;
                return [
                    'id'   => $user->id,
                    'name' => $user->name,
                ];
            })
            ->filter()
            ->unique('id')
            ->values();

        return response()->json(['success' => true, 'students' => $students]);
    }

    /**
     * AJAX Endpoint: Get Courses Enrolled by a Specific Student (Filtered by Teacher)
     */
    public function getStudentCourses(Request $request, int $studentUserId): JsonResponse
    {
        $teacherProfile = $this->getAuthorizedTeacherProfile(auth()->user());
        if (! $teacherProfile) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $student = User::where('id', $studentUserId)->first();
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found', 'courses' => []], 404);
        }

        $courses = Course::query()
            ->where(function ($q) use ($teacherProfile) {
                $q->where('teacher_id', $teacherProfile->id)
                  ->when(auth()->user()?->isAdmin(), fn ($q2) => $q2->orWhereNotNull('id'));
            })
            ->where('is_active', true)
            ->whereHas('enrollments', function ($eq) use ($studentUserId) {
                $eq->where('student_user_id', $studentUserId);
            })
            ->with('subject')
            ->orderBy('title', 'asc')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'subject_name' => $course->subject?->name ?: '',
                ];
            });

        return response()->json([
            'success' => true,
            'courses' => $courses,
        ]);
    }

    /**
     * AJAX Endpoint: Approve Student Exception / Absence Excuse (Teacher action)
     * - The session is NOT decreased from student package (refunded if previously deducted).
     */
    public function approveException(Request $request, int $id, ExceptionRequestService $service): JsonResponse
    {
        $user = auth()->user();
        $exception = ExceptionRequest::with(['course', 'liveSession', 'studentUser'])->findOrFail($id);

        if (! $user->can('update', $exception)) {
            return response()->json(['success' => false, 'message' => __('Unauthorized to review this exception request.')], 403);
        }

        $notes = $request->input('admin_notes', $request->input('notes'));
        $service->approve($exception, $user, $notes);

        return response()->json([
            'success' => true,
            'message' => __('Exception approved successfully! Session credit was preserved for student.'),
            'status' => 'approved',
            'exception_id' => $exception->id,
        ]);
    }

    /**
     * AJAX Endpoint: Reject Student Exception / Absence Excuse (Teacher action)
     * - The session IS decreased from student package balance.
     */
    public function rejectException(Request $request, int $id, ExceptionRequestService $service): JsonResponse
    {
        $user = auth()->user();
        $exception = ExceptionRequest::with(['course', 'liveSession', 'studentUser'])->findOrFail($id);

        if (! $user->can('update', $exception)) {
            return response()->json(['success' => false, 'message' => __('Unauthorized to review this exception request.')], 403);
        }

        $notes = $request->input('admin_notes', $request->input('notes'));
        $service->reject($exception, $user, $notes);

        return response()->json([
            'success' => true,
            'message' => __('Exception rejected. 1 session credit has been deducted from the student package balance.'),
            'status' => 'rejected',
            'exception_id' => $exception->id,
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

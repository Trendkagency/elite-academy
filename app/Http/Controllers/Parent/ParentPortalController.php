<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParentPortalController extends Controller
{
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if ($user && $user->status !== \App\Enums\AccountStatus::APPROVED) {
            auth()->logout();
            return redirect()->route('login')->with('error', __('app.auth.account_pending'));
        }

        $parentProfile = $user ? ParentProfile::where('user_id', $user->id)->first() : null;

        $linkedStudents = [];
        if ($user) {
            $linkedStudentUserIds = DB::table('parent_student')
                ->where('parent_user_id', $user->id)
                ->pluck('student_user_id');

            $linkedStudents = StudentProfile::whereIn('user_id', $linkedStudentUserIds)
                ->with(['user', 'gradeLevel'])
                ->latest('created_at')
                ->get();
        }

        return view('pages.parent-portal', [
            'pageTitle' => 'Parent Portal — Multi-Child Academic Monitoring',
            'activeNav' => 'portal',
            'linkedStudents' => $linkedStudents,
        ]);
    }

    public function studentProgress(int $studentUserId): JsonResponse
    {
        $user = auth()->user();

        // Strict Privacy Rule: Parent can ONLY view their own linked children!
        if (! $user->isAdmin()) {
            $isLinked = DB::table('parent_student')
                ->where('parent_user_id', $user->id)
                ->where('student_user_id', $studentUserId)
                ->exists();

            if (! $isLinked) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unauthorized Access: You can only view performance data for your own linked children.'),
                ], 403);
            }
        }

        $studentProfile = StudentProfile::where('user_id', $studentUserId)
            ->with(['user', 'gradeLevel'])
            ->first();

        if (! $studentProfile) {
            return response()->json([
                'success' => false,
                'message' => __('Student profile not found.'),
            ], 404);
        }

        // 1. Enrollments & Enrolled Course IDs with full details
        $enrollments = CourseEnrollment::where('student_user_id', $studentUserId)
            ->with(['course.subject', 'course.teacher.user', 'progress'])
            ->get();

        $enrolledCourseIds = $enrollments->pluck('course_id')->filter()->toArray();

        $coursesDetail = $enrollments->map(function ($enr) {
            $course = $enr->course;
            $completedCount = $enr->progress ? $enr->progress->where('is_completed', true)->count() : 0;
            $totalCount = $course ? ($course->recorded_sessions_count ?? $course->sessions()->count()) : 0;
            $progressPct = $enr->progress_percent !== null 
                ? (int) $enr->progress_percent 
                : ($totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0);

            return [
                'course_id' => $course?->id,
                'title' => $course?->title ?? __('Educational Course'),
                'subject' => $course?->subject?->name ?? __('General Subject'),
                'teacher' => $course?->teacher?->user?->name ?? __('Academic Instructor'),
                'progress_pct' => min(100, max(0, $progressPct)),
                'completed_sessions' => $completedCount,
                'total_sessions' => $totalCount,
                'enrolled_at' => $enr->created_at ? $enr->created_at->format('Y-m-d') : '',
            ];
        });

        // 2. Upcoming Sessions (for enrolled courses or direct student sessions)
        $upcomingSessions = LiveSession::where(function ($q) use ($studentUserId, $enrolledCourseIds) {
                $q->where('student_user_id', $studentUserId);
                if (! empty($enrolledCourseIds)) {
                    $q->orWhereIn('course_id', $enrolledCourseIds);
                }
            })
            ->where('scheduled_at', '>=', now())
            ->with(['teacherProfile.user', 'subject', 'course'])
            ->orderBy('scheduled_at', 'asc')
            ->limit(10)
            ->get();

        // 3. Real Attendance Logs & Computation (Merged from StudentSession, Direct LiveSession, and MeetingAttendance)
        $studentSessions = \App\Models\StudentSession::where('student_user_id', $studentUserId)
            ->with(['liveSession.course.subject', 'liveSession.course.teacher.user', 'liveSession.teacherProfile.user', 'liveSession.subject'])
            ->orderBy('created_at', 'desc')
            ->get();

        $meetingAttendances = \App\Models\MeetingAttendance::where('student_user_id', $studentUserId)
            ->with(['liveSession.course.subject', 'liveSession.course.teacher.user', 'liveSession.teacherProfile.user', 'liveSession.subject'])
            ->orderBy('joined_at', 'desc')
            ->get();

        $directSessions = LiveSession::where('student_user_id', $studentUserId)
            ->whereNotNull('attendance_status')
            ->with(['course.subject', 'course.teacher.user', 'teacherProfile.user', 'subject'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $attendanceLogsMap = collect();

        foreach ($studentSessions as $ss) {
            $sess = $ss->liveSession;
            if (! $sess) continue;
            $rawStatus = $ss->attendance_status;
            $status = in_array($rawStatus, ['present', 'late', 'absent', 'excused'], true) ? $rawStatus : 'present';
            $attendanceLogsMap->put($sess->id, [
                'session_id' => $sess->id,
                'session_title' => $sess->title ?: ($sess->course?->title ?: __('Live Class Session')),
                'subject' => $sess->subject?->name ?: ($sess->course?->subject?->name ?: __('Curriculum')),
                'teacher' => $sess->teacherProfile?->user?->name ?: ($sess->course?->teacher?->user?->name ?: __('Instructor')),
                'joined_at' => $ss->completed_at ? $ss->completed_at->format('Y-m-d h:i A') : ($ss->created_at ? $ss->created_at->format('Y-m-d h:i A') : __('Recorded')),
                'duration_minutes' => $sess->duration_minutes ?: 60,
                'status' => $status,
            ]);
        }

        foreach ($directSessions as $ds) {
            if (! $attendanceLogsMap->has($ds->id)) {
                $rawStatus = $ds->attendance_status;
                $status = in_array($rawStatus, ['present', 'late', 'absent', 'excused'], true) ? $rawStatus : 'present';
                $attendanceLogsMap->put($ds->id, [
                    'session_id' => $ds->id,
                    'session_title' => $ds->title ?: ($ds->course?->title ?: __('Live Class Session')),
                    'subject' => $ds->subject?->name ?: ($ds->course?->subject?->name ?: __('Curriculum')),
                    'teacher' => $ds->teacherProfile?->user?->name ?: ($ds->course?->teacher?->user?->name ?: __('Instructor')),
                    'joined_at' => $ds->scheduled_at ? $ds->scheduled_at->format('Y-m-d h:i A') : __('Recorded'),
                    'duration_minutes' => $ds->duration_minutes ?: 60,
                    'status' => $status,
                ]);
            }
        }

        foreach ($meetingAttendances as $ma) {
            $sess = $ma->liveSession;
            if ($sess && ! $attendanceLogsMap->has($sess->id)) {
                $rawStatus = $ma->status;
                $status = in_array($rawStatus, ['attended', 'present', 'completed'], true) ? 'present' : ($rawStatus === 'absent' ? 'absent' : 'present');
                $attendanceLogsMap->put($sess->id, [
                    'session_id' => $sess->id,
                    'session_title' => $sess->title ?: ($sess->course?->title ?: __('Live Class Session')),
                    'subject' => $sess->subject?->name ?: ($sess->course?->subject?->name ?: __('Curriculum')),
                    'teacher' => $sess->teacherProfile?->user?->name ?: ($sess->course?->teacher?->user?->name ?: __('Instructor')),
                    'joined_at' => $ma->joined_at ? $ma->joined_at->format('Y-m-d h:i A') : ($ma->created_at ? $ma->created_at->format('Y-m-d h:i A') : __('Verified')),
                    'duration_minutes' => $ma->duration_seconds ? round($ma->duration_seconds / 60) : ($sess->duration_minutes ?: 60),
                    'status' => $status,
                ]);
            }
        }

        $allAttendanceLogs = $attendanceLogsMap->values();
        $totalSessionsCount = $allAttendanceLogs->count();
        $totalAttended = $allAttendanceLogs->where('status', 'present')->count();
        $lateCount = $allAttendanceLogs->where('status', 'late')->count();
        $absencesCount = $allAttendanceLogs->where('status', 'absent')->count();
        $excusedCount = $allAttendanceLogs->where('status', 'excused')->count();

        $effectiveAttended = $totalAttended + ($lateCount * 0.5);
        $attendanceRate = $totalSessionsCount > 0
            ? round(($effectiveAttended / $totalSessionsCount) * 100) . '%'
            : '100%';

        // 4. Homework Submissions & Graded Evaluation History
        $submissions = AssignmentSubmission::where('student_user_id', $studentUserId)
            ->with(['assignment.session', 'assignment.course.subject', 'assignment.teacherProfile.user'])
            ->orderBy('submitted_at', 'desc')
            ->limit(20)
            ->get();

        $gradedSubmissions = $submissions->whereNotNull('grade');
        $averageGrade = $gradedSubmissions->count() > 0 
            ? round($gradedSubmissions->avg('grade'), 1) 
            : 100.0;

        // 5. Active Package & Credits
        $package = StudentPackage::where('student_user_id', $studentUserId)
            ->with('packageTemplate')
            ->orderBy('created_at', 'desc')
            ->first();

        // 6. Real Student Notifications & Academic Alerts
        $notifications = [];
        $rawNotifications = \App\Models\UserNotification::where('user_id', $studentUserId)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        foreach ($rawNotifications as $n) {
            $type = strtoupper((string) $n->type);
            $icon = 'fa-solid fa-bell';
            $badgeColor = 'teal';
            $category = __('Academic Alert');

            if (str_contains($type, 'ASSIGNMENT_GRADED') || str_contains($type, 'SUBMISSION')) {
                $icon = 'fa-solid fa-clipboard-check';
                $badgeColor = 'emerald';
                $category = __('Graded Assignment');
            } elseif (str_contains($type, 'ASSIGNMENT_ADDED') || str_contains($type, 'ASSIGNMENT')) {
                $icon = 'fa-solid fa-file-pen';
                $badgeColor = 'teal';
                $category = __('Homework Assignment');
            } elseif (str_contains($type, 'DEADLINE')) {
                $icon = 'fa-solid fa-hourglass-half';
                $badgeColor = 'amber';
                $category = __('Submission Deadline');
            } elseif (str_contains($type, 'SESSION_OPENED') || str_contains($type, 'SESSION_STARTED')) {
                $icon = 'fa-solid fa-video';
                $badgeColor = 'emerald';
                $category = __('Live Session Active');
            } elseif (str_contains($type, 'SESSION')) {
                $icon = 'fa-solid fa-calendar-days';
                $badgeColor = 'blue';
                $category = __('Live Schedule');
            } elseif (str_contains($type, 'APPROVAL')) {
                $icon = 'fa-solid fa-shield-check';
                $badgeColor = 'indigo';
                $category = __('Admin Approval');
            } elseif (str_contains($type, 'NOTE')) {
                $icon = 'fa-solid fa-comment-dots';
                $badgeColor = 'purple';
                $category = __('Teacher Note');
            } elseif (str_contains($type, 'ABSENT') || str_contains($type, 'ATTENDANCE')) {
                $icon = 'fa-solid fa-triangle-exclamation';
                $badgeColor = 'rose';
                $category = __('Attendance Check');
            }

            $notifications[] = [
                'id' => $n->id,
                'type' => $n->type,
                'category' => $category,
                'icon' => $icon,
                'color' => $badgeColor,
                'title' => $n->title,
                'message' => $n->body,
                'is_read' => (bool) $n->is_read,
                'time' => $n->created_at ? $n->created_at->diffForHumans() : __('Recently'),
                'date' => $n->created_at ? $n->created_at->format('Y-m-d h:i A') : '',
            ];
        }

        // If no user_notifications rows exist, synthesize real-time events from actual student records
        if (empty($notifications)) {
            // 1. Check recent graded submissions
            foreach ($submissions->take(3) as $sub) {
                $gradePct = $sub->grade !== null ? $sub->grade . '%' : null;
                if ($gradePct !== null) {
                    $notifications[] = [
                        'id' => 'sub-' . $sub->id,
                        'type' => 'ASSIGNMENT_GRADED',
                        'category' => __('Graded Assignment'),
                        'icon' => 'fa-solid fa-clipboard-check',
                        'color' => 'emerald',
                        'title' => __('Assignment Evaluation Completed'),
                        'message' => __('Assignment ":title" has been graded. Grade: :grade.', ['title' => $sub->assignment?->title ?: __('Assignment'), 'grade' => $gradePct]),
                        'is_read' => true,
                        'time' => $sub->submitted_at ? $sub->submitted_at->diffForHumans() : __('Recently'),
                        'date' => $sub->submitted_at ? $sub->submitted_at->format('Y-m-d h:i A') : '',
                    ];
                }
            }

            // 2. Check teacher educational notes
            $studentNotes = \App\Models\StudentEducationalNote::where('student_user_id', $studentUserId)
                ->with('teacherProfile.user')
                ->latest()
                ->take(2)
                ->get();

            foreach ($studentNotes as $sn) {
                $notifications[] = [
                    'id' => 'note-' . $sn->id,
                    'type' => 'EDUCATIONAL_NOTE',
                    'category' => __('Teacher Note'),
                    'icon' => 'fa-solid fa-comment-dots',
                    'color' => 'purple',
                    'title' => __('Teacher Academic Feedback'),
                    'message' => $sn->note,
                    'is_read' => true,
                    'time' => $sn->created_at ? $sn->created_at->diffForHumans() : __('Recently'),
                    'date' => $sn->created_at ? $sn->created_at->format('Y-m-d h:i A') : '',
                ];
            }

            // 3. Check upcoming sessions
            foreach ($upcomingSessions->take(2) as $upSess) {
                $notifications[] = [
                    'id' => 'sess-' . $upSess->id,
                    'type' => 'SESSION_REMINDER',
                    'category' => __('Upcoming Live Class'),
                    'icon' => 'fa-solid fa-calendar-days',
                    'color' => 'blue',
                    'title' => __('Upcoming Live Session Reminder'),
                    'message' => __('Live class ":title" (:subject) is scheduled for :time.', ['title' => $upSess->title, 'subject' => $upSess->subject?->name ?: __('Class'), 'time' => $upSess->scheduled_at ? $upSess->scheduled_at->format('Y-m-d h:i A') : '']),
                    'is_read' => true,
                    'time' => $upSess->scheduled_at ? $upSess->scheduled_at->diffForHumans() : __('Upcoming'),
                    'date' => $upSess->scheduled_at ? $upSess->scheduled_at->format('Y-m-d h:i A') : '',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'is_read_only' => true,
            'enrollments_count' => $enrollments->count(),
            'submissions_count' => $submissions->count(),
            'average_grade' => $averageGrade,
            'student' => [
                'id' => $studentUserId,
                'name' => $studentProfile->user->name ?? 'Student',
                'email' => $studentProfile->user->email ?? '',
                'phone' => $studentProfile->user->phone ?? '',
                'grade' => $studentProfile->gradeLevel->name ?? __('Third Year Secondary'),
                'school' => $studentProfile->school_name ?? __('Elite STEM Academy'),
            ],
            'courses' => $coursesDetail,
            'package' => [
                'name' => $package?->packageTemplate?->name ?: __('Monthly Excellence Package (12 Sessions)'),
                'remaining_sessions' => $package ? $package->remaining_sessions : 8,
                'total_sessions' => $package ? $package->total_sessions : 12,
                'used_sessions' => $package ? $package->used_sessions : 4,
                'status' => $package ? $package->status : 'active',
            ],
            'attendance' => [
                'rate' => $attendanceRate,
                'attended_count' => $totalAttended,
                'late_count' => $lateCount,
                'absences_count' => $absencesCount,
                'excused_count' => $excusedCount,
                'total_sessions_count' => $totalSessionsCount,
                'has_records' => $totalSessionsCount > 0,
                'logs' => $allAttendanceLogs,
            ],
            'upcoming_sessions' => $upcomingSessions->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title ?: ($s->course ? $s->course->title : __('Live Stream Session')),
                'teacher_name' => $s->teacherProfile?->user?->name ?: ($s->course?->teacher?->user?->name ?: __('Academic Instructor')),
                'subject_name' => $s->subject?->name ?: ($s->course?->subject?->name ?: __('Curriculum')),
                'scheduled_at' => $s->scheduled_at ? $s->scheduled_at->format('Y-m-d h:i A') : __('Today 06:00 PM'),
                'scheduled_diff' => $s->scheduled_at ? $s->scheduled_at->diffForHumans() : '',
                'is_today' => $s->scheduled_at ? $s->scheduled_at->isToday() : false,
            ]),
            'submissions' => $submissions->map(fn ($s) => [
                'assignment_title' => $s->assignment->title ?? __('Homework Assignment'),
                'course_title' => $s->assignment?->course?->title ?? ($s->assignment?->subject?->name ?? __('Academic Module')),
                'teacher_name' => $s->assignment?->teacherProfile?->user?->name ?? __('Subject Instructor'),
                'status' => is_object($s->status) ? $s->status->value : $s->status,
                'grade' => $s->grade !== null ? $s->grade . '%' : __('Under Evaluation'),
                'grade_num' => $s->grade,
                'passing_score' => $s->assignment?->passing_score ?? 70,
                'is_passed' => $s->grade !== null && $s->grade >= ($s->assignment?->passing_score ?? 70),
                'teacher_notes' => $s->teacher_notes ?: __('Good effort, keep up the regular practice!'),
                'submitted_at' => $s->submitted_at ? $s->submitted_at->format('Y-m-d H:i') : ($s->created_at ? $s->created_at->format('Y-m-d H:i') : __('Submitted')),
            ]),
            'notifications' => $notifications,
        ]);
    }

    public function linkChildByPhone(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'phone_or_email' => 'required|string|min:3',
        ]);

        $user = auth()->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => __('Authentication required.'),
            ], 401);
        }

        $parentProfile = ParentProfile::firstOrCreate(['user_id' => $user->id]);

        $query = trim($request->input('phone_or_email'));

        // Search for student user by phone, email, or exact match
        $studentUser = \App\Models\User::whereHas('studentProfile')
            ->where(function ($q) use ($query) {
                $q->where('phone', $query)
                  ->orWhere('email', strtolower($query))
                  ->orWhere('phone', 'LIKE', "%{$query}%");
            })->first();

        if (! $studentUser) {
            return response()->json([
                'success' => false,
                'message' => __('No student account found matching this phone number or email. Please verify the student account exists.'),
            ], 404);
        }

        // Check if already linked
        $alreadyLinked = DB::table('parent_student')
            ->where('parent_user_id', $user->id)
            ->where('student_user_id', $studentUser->id)
            ->exists();

        if ($alreadyLinked) {
            return response()->json([
                'success' => true,
                'already_linked' => true,
                'message' => __('This student is already linked to your parent account.'),
                'student_id' => $studentUser->id,
            ]);
        }

        // Link student to parent
        DB::table('parent_student')->insert([
            'parent_user_id' => $user->id,
            'student_user_id' => $studentUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update student profile parent link
        $studentProfile = StudentProfile::where('user_id', $studentUser->id)->first();
        if ($studentProfile) {
            $studentProfile->update(['parent_user_id' => $user->id]);
        }

        // Notify admins about parent-child linking
        try {
            app(\App\Services\Notification\FcmNotificationService::class)->notifyAdminParentChildLinked($user, $studentUser);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[FCM] notifyAdminParentChildLinked failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => __('Student successfully linked to your parent account!'),
            'student' => [
                'id' => $studentUser->id,
                'name' => $studentUser->name,
                'email' => $studentUser->email,
                'grade' => $studentProfile?->gradeLevel?->name ?? __('Secondary Level'),
                'school' => $studentProfile?->school_name ?? __('Elite STEM Academy'),
            ],
        ]);
    }
}

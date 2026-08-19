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
    public function index()
    {
        $user = auth()->user();

        if ($user && $user->status !== \App\Enums\AccountStatus::APPROVED) {
            auth()->logout();
            return redirect()->route('login')->with('error', __('app.auth.account_pending'));
        }

        $studentProfile = $user ? StudentProfile::where('user_id', $user->id)->with(['gradeLevel', 'subjects'])->first() : null;

        $package = $user ? StudentPackage::where('student_user_id', $user->id)
            ->with('packageTemplate')
            ->orderBy('created_at', 'desc')
            ->first() : null;

        $hasActivePackage = $package && $package->status === 'active' && $package->remaining_sessions > 0 && (! $package->expires_at || $package->expires_at->isFuture());

        $upcomingSessions = $user ? LiveSession::where(function ($q) use ($user) {
                $q->where('student_user_id', $user->id)
                  ->orWhereNull('student_user_id');
            })
            ->where(function ($q) {
                $q->whereNull('course_id')
                  ->orWhereHas('course', function ($cQuery) {
                      $cQuery->where('is_active', true);
                  });
            })
            ->with(['teacherProfile.user', 'subject', 'course'])
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->filter(function ($session) use ($user, $hasActivePackage) {
                if ($session->course && ! $session->course->is_active) {
                    return false;
                }
                // If session is NOT a free demo, it strictly REQUIRES an active paid package!
                if (! $session->is_free_demo_session) {
                    return $hasActivePackage;
                }
                // If it IS a free demo session, it is visible for free trial
                return true;
            })
            ->values() : collect();

        $enrollments = $user ? CourseEnrollment::where('student_user_id', $user->id)
            ->with([
                'course.subject',
                'course.teacher.user',
                'course.sessions.assignments',
                'course.liveSessions.teacherProfile.user',
                'course.gradeLevel',
                'progress'
            ])
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

        $enrolledCoursesDataMap = [];
        foreach ($enrollments as $enr) {
            $c = $enr->course;
            if (!$c) continue;

            $recList = [];
            if ($c->sessions) {
                foreach ($c->sessions as $idx => $cs) {
                    $assigns = [];
                    if ($cs->assignments) {
                        foreach ($cs->assignments as $a) {
                            $assigns[] = [
                                'id' => $a->id,
                                'title' => $a->title,
                                'points' => (float) $a->total_points,
                                'due' => $a->due_at ? $a->due_at->format('Y-m-d H:i') : null,
                                'url' => route('student.assignment.take', ['id' => $a->id]),
                            ];
                        }
                    }
                    $recList[] = [
                        'id' => $cs->id,
                        'index' => $idx + 1,
                        'title' => $cs->title ?: ('Lesson ' . ($idx + 1)),
                        'description' => $cs->description ?: '',
                        'video_url' => $cs->video_url ?: null,
                        'duration' => $cs->duration_minutes ?: 45,
                        'is_free_demo' => (bool) $cs->is_free_demo,
                        'assignments' => $assigns,
                    ];
                }
            }

            $liveList = [];
            if ($c->liveSessions) {
                foreach ($c->liveSessions as $idx => $ls) {
                    $state = $ls->evaluateState($user);
                    $liveList[] = [
                        'id' => $ls->id,
                        'index' => $idx + 1,
                        'title' => $ls->title ?: ('Live Stream ' . ($idx + 1)),
                        'start_at' => $ls->effective_start_at ? $ls->effective_start_at->format('Y-m-d h:i A') : 'Scheduled',
                        'teacher' => $ls->teacherProfile?->user?->name ?: 'Dr. Teacher',
                        'meeting_link' => $ls->meeting_link ?: '',
                        'state_label' => $state->label(),
                        'can_join' => $state->canJoin(),
                        'is_live' => $state === \App\Enums\LiveSessionState::LIVE,
                    ];
                }
            }

            $enrolledCoursesDataMap[$c->id] = [
                'id' => $c->id,
                'title' => $c->title,
                'subject' => $c->subject?->name ?: 'Science',
                'teacher' => $c->teacher?->user?->name ?: 'Dr. Teacher',
                'grade' => $c->gradeLevel?->name ?: 'High School',
                'description' => $c->description ?: (app()->getLocale() === 'ar' ? 'مقرر تعليمي تفاعلي شامل للمرحلة الثانوية مع تطبيقات عملية.' : 'Comprehensive interactive curriculum with hands-on labs.'),
                'recorded_sessions' => $recList,
                'live_sessions' => $liveList,
            ];
        }

        // ── Attendance & Absence stats (for dashboard KPI card) ────────────────
        $attendedSessions  = $upcomingSessions->where('status', 'completed')->count();
        $totalSessionCount = $upcomingSessions->count();
        $attendanceRate    = $totalSessionCount > 0 ? round(($attendedSessions / $totalSessionCount) * 100) : 0;
        $approvedExcuses   = $exceptions->where('status', 'approved')->count();

        // ── Homework / assignment avg score (for dashboard KPI card) ───────────
        $gradedSubmissions = $submissions
            ->whereIn('status', ['reviewed', 'submitted', 'completed'])
            ->filter(fn ($s) => !is_null($s->score));
        $avgScore = $gradedSubmissions->count() > 0 ? round($gradedSubmissions->avg('score')) : null;

        // ── Per-enrollment card display data (avoids @php in foreach) ─────────
        $enrollmentCards = $enrollments->map(function ($enr) {
            $c = $enr->course;
            if (!$c) return null;

            $recCount    = $c->sessions    ? $c->sessions->count()    : 0;
            $liveCount   = $c->liveSessions ? $c->liveSessions->count() : 0;
            $totalSess   = $recCount + $liveCount;
            $unlocked    = $enr->progress  ? $enr->progress->count()  : 0;
            $progressPct = $totalSess > 0 ? min(100, round(($unlocked / max(1, $totalSess)) * 100)) : 0;

            return [
                'enrollment'  => $enr,
                'course'      => $c,
                'teacher'     => $c->teacher?->user?->name ?: 'Dr. Teacher',
                'subject'     => $c->subject?->name         ?: 'Science',
                'recCount'    => $recCount,
                'liveCount'   => $liveCount,
                'progressPct' => $progressPct,
            ];
        })->filter()->values();

        // ── Notification pagination vars (for AJAX pagination controls) ────────
        $notifCurrentPage  = $userNotifications instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $userNotifications->currentPage() : 1;
        $notifLastPage     = $userNotifications instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $userNotifications->lastPage() : 1;
        $totalAlertsCount  = $userNotifications instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $userNotifications->total() : count($userNotifications);

        return view('pages.student-portal', [
            'pageTitle'              => 'Student Portal — Learner Dashboard',
            'activeNav'              => 'portal',
            'studentProfile'         => $studentProfile,
            'studentSubjects'        => $studentProfile?->subjects ?: collect(),
            'package'                => $package,
            'hasActivePackage'       => $hasActivePackage,
            'upcomingSessions'       => $upcomingSessions,
            'enrollments'            => $enrollments,
            'enrollmentCards'        => $enrollmentCards,
            'enrolledCoursesDataMap' => $enrolledCoursesDataMap,
            'submissions'            => $submissions,
            'availableAssignments'   => $availableAssignments,
            'exceptions'             => $exceptions,
            'userNotifications'      => $userNotifications,
            // KPI cards
            'attendedSessions'       => $attendedSessions,
            'totalSessionCount'      => $totalSessionCount,
            'attendanceRate'         => $attendanceRate,
            'approvedExcuses'        => $approvedExcuses,
            'avgScore'               => $avgScore,
            // Notification pagination
            'notifCurrentPage'       => $notifCurrentPage,
            'notifLastPage'          => $notifLastPage,
            'totalAlertsCount'       => $totalAlertsCount,
        ]);
    }
}

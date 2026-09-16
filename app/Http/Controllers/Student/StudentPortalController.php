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

        $enrollments = $user ? CourseEnrollment::where('student_user_id', $user->id)
            ->with([
                'course.subject',
                'course.teacher.user',
                'course.sessions.assignments',
                'course.liveSessions.teacherProfile.user',
                'course.gradeLevel',
                'progress'
            ])
            ->latest('created_at')
            ->get() : collect();

        $enrolledCourseIds = $enrollments->pluck('course_id')->filter()->toArray();

        // Also resolve any course IDs and session IDs linked via direct 1:1 sessions or student_sessions
        $assignedSessionCourseIds = $user ? \App\Models\LiveSession::where('student_user_id', $user->id)
            ->whereNotNull('course_id')
            ->pluck('course_id')
            ->toArray() : [];

        $studentSessionCourseIds = $user ? \Illuminate\Support\Facades\DB::table('student_sessions')
            ->join('live_sessions', 'student_sessions.live_session_id', '=', 'live_sessions.id')
            ->where('student_sessions.student_user_id', $user->id)
            ->whereNotNull('live_sessions.course_id')
            ->pluck('live_sessions.course_id')
            ->toArray() : [];

        $allStudentCourseIds = array_values(array_unique(array_filter(array_merge(
            $enrolledCourseIds,
            $assignedSessionCourseIds,
            $studentSessionCourseIds
        ))));

        $allStudentSessionIds = $user ? \Illuminate\Support\Facades\DB::table('student_sessions')
            ->where('student_user_id', $user->id)
            ->pluck('live_session_id')
            ->toArray() : [];

        $directLiveSessionIds = $user ? \App\Models\LiveSession::where('student_user_id', $user->id)
            ->pluck('id')
            ->toArray() : [];

        $visibleSessionIds = $user ? \App\Models\LiveSession::visibleToStudent($user->id, $allStudentCourseIds)
            ->pluck('id')
            ->toArray() : [];

        $allSessionIds = array_values(array_unique(array_filter(array_merge(
            $directLiveSessionIds,
            $allStudentSessionIds,
            $visibleSessionIds
        ))));

        $upcomingSessions = $user ? LiveSession::visibleToStudent($user->id, $allStudentCourseIds)
            ->where(function ($q) {
                $q->whereNull('course_id')
                  ->orWhereHas('course', function ($cQuery) {
                      $cQuery->where('is_active', true);
                  });
            })
            ->with(['teacherProfile.user', 'subject', 'course', 'attendances'])
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
            ->unique('id')
            ->values() : collect();

        // Categorize sessions for professional tabs & clean pagination
        $now = now();
        $startingSoonSessions = collect();
        $upcomingScheduledSessions = collect();
        $endedSessionsHistory = collect();

        foreach ($upcomingSessions as $session) {
            $state = $session->evaluateState($user, $now);
            $startAt = $session->effective_start_at;
            $endAt = $session->effective_end_at;
            $isCancelled = in_array($session->status, ['cancelled', 'cancelled_by_teacher'], true);
            $isCompleted = $session->status === 'completed' || $state === \App\Enums\LiveSessionState::ENDED;
            $isPast = ($endAt && $endAt->isPast()) || ($startAt && $startAt->isPast() && (!$endAt || $endAt->isPast()));

            // 1. Ended & Past Session History
            if ($isCompleted || $isCancelled || ($isPast && $state !== \App\Enums\LiveSessionState::LIVE)) {
                $endedSessionsHistory->push($session);
                continue;
            }

            // 2. Starting Soon & Live (Live right now, or starting today, or within the next 24 hours)
            // isLive depends ONLY on evaluateState which enforces the 30-min window.
            $isLive = ($state === \App\Enums\LiveSessionState::LIVE);
            $isSoon = $startAt && ($startAt->isToday() || ($startAt->isFuture() && $startAt->diffInHours($now) <= 24));

            if ($isLive || $isSoon) {
                $startingSoonSessions->push($session);
                continue;
            }

            // 3. Upcoming Scheduled Sessions (Future dates beyond 24h)
            $upcomingScheduledSessions->push($session);
        }

        // Sort Starting Soon: LIVE sessions first, then earliest startAt
        $startingSoonSessions = $startingSoonSessions->sortBy(function ($s) use ($user, $now) {
            $isLive = ($s->evaluateState($user, $now) === \App\Enums\LiveSessionState::LIVE);
            $ts = $s->effective_start_at ? $s->effective_start_at->timestamp : PHP_INT_MAX;
            return $isLive ? 0 : $ts;
        })->values();

        // Sort Upcoming Scheduled by chronological order
        $upcomingScheduledSessions = $upcomingScheduledSessions->sortBy(function ($s) {
            return $s->effective_start_at ? $s->effective_start_at->timestamp : PHP_INT_MAX;
        })->values();

        // Sort Ended History: most recently ended/held first
        $endedSessionsHistory = $endedSessionsHistory->sortByDesc(function ($s) {
            return $s->effective_end_at ? $s->effective_end_at->timestamp : ($s->effective_start_at ? $s->effective_start_at->timestamp : 0);
        })->values();

        $submissions = $user ? AssignmentSubmission::where('student_user_id', $user->id)
            ->with([
                'assignment.course.subject',
                'assignment.course.teacher.user',
                'assignment.session',
                'assignment.liveSession.subject',
                'assignment.liveSession.teacherProfile.user',
                'answers'
            ])
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        // Completed Submissions (Evaluated / Finalized)
        $completedSubmissions = $submissions->filter(function ($s) {
            return in_array($s->status, [\App\Enums\SubmissionStatus::COMPLETED, \App\Enums\SubmissionStatus::SUBMITTED, \App\Enums\SubmissionStatus::REVIEWED])
                || ! is_null($s->submitted_at);
        });

        // In Progress Submissions (Drafts started by student)
        $inProgressSubmissions = $submissions->filter(function ($s) {
            return $s->status === \App\Enums\SubmissionStatus::IN_PROGRESS && is_null($s->submitted_at);
        })->keyBy('assignment_id');

        $completedAssignmentIds = $completedSubmissions->pluck('assignment_id')->filter()->toArray();

        $allStudentAssignments = $user ? \App\Models\Assignment::with([
                'questions.options',
                'course.subject',
                'course.teacher.user',
                'session',
                'liveSession.subject',
                'liveSession.teacherProfile.user'
            ])
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) use ($allStudentCourseIds, $allSessionIds) {
                $hasCourses = ! empty($allStudentCourseIds);
                $hasSessions = ! empty($allSessionIds);

                if ($hasCourses && $hasSessions) {
                    $q->where(function ($inner) use ($allStudentCourseIds, $allSessionIds) {
                        $inner->whereIn('course_id', $allStudentCourseIds)
                              ->orWhereIn('live_session_id', $allSessionIds);
                    });
                } elseif ($hasCourses) {
                    $q->whereIn('course_id', $allStudentCourseIds);
                } elseif ($hasSessions) {
                    $q->whereIn('live_session_id', $allSessionIds);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $availableAssignments = $allStudentAssignments->filter(function ($a) use ($completedAssignmentIds) {
            return ! in_array($a->id, $completedAssignmentIds, true);
        })->values();

        $filterCourses = $enrollments->map(fn($e) => $e->course)->filter();
        if ($filterCourses->isEmpty() && ! empty($allStudentCourseIds)) {
            $filterCourses = \App\Models\Course::whereIn('id', $allStudentCourseIds)->get();
        }
        $filterCourses = $filterCourses->unique('id')->values();

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
                    if ($ls->isAssignedToOtherStudent((int) $user->id)) {
                        continue;
                    }
                    $state = $ls->evaluateState($user);
                    $liveList[] = [
                        'id' => $ls->id,
                        'index' => $idx + 1,
                        'title' => $ls->studentFacingTitle('Live Stream ' . ($idx + 1)),
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
        $teacherNotes = $user ? \App\Models\StudentEducationalNote::where('student_user_id', $user->id)
            ->with(['teacherProfile.user', 'teacherProfile.subjects'])
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $totalAlertsCount  = $userNotifications instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $userNotifications->total() : count($userNotifications);

        return view('pages.student-portal', [
            'pageTitle'              => 'Student Portal — Learner Dashboard',
            'activeNav'              => 'portal',
            'studentProfile'         => $studentProfile,
            'studentSubjects'        => $studentProfile?->subjects ?: collect(),
            'package'                => $package,
            'hasActivePackage'       => $hasActivePackage,
            'upcomingSessions'          => $upcomingSessions,
            'startingSoonSessions'      => $startingSoonSessions,
            'upcomingScheduledSessions' => $upcomingScheduledSessions,
            'endedSessionsHistory'      => $endedSessionsHistory,
            'enrollments'            => $enrollments,
            'enrollmentCards'        => $enrollmentCards,
            'enrolledCoursesDataMap' => $enrolledCoursesDataMap,
            'submissions'            => $completedSubmissions,
            'completedSubmissions'   => $completedSubmissions,
            'inProgressSubmissions'  => $inProgressSubmissions,
            'submissionsMap'         => $submissions->keyBy('assignment_id'),
            'availableAssignments'   => $availableAssignments,
            'allStudentAssignments'  => $allStudentAssignments,
            'filterCourses'          => $filterCourses,
            'exceptions'             => $exceptions,
            'teacherNotes'           => $teacherNotes,
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

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\ExceptionRequest;
use App\Models\FileUpload;
use App\Models\LiveSession;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
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

        $sessionData = $this->getStudentSessionsData($user, now());

        $package = $sessionData['package'];
        $hasActivePackage = $sessionData['hasActivePackage'];
        $enrollments = $sessionData['enrollments'];
        $allStudentCourseIds = $sessionData['allStudentCourseIds'];
        $allSessionIds = $sessionData['allSessionIds'];
        $upcomingSessions = $sessionData['upcomingSessions'];
        $todaySessions = $sessionData['todaySessions'];
        $allSessionsForCalendar = $sessionData['allSessionsForCalendar'];
        $startingSoonSessions = $sessionData['startingSoonSessions'];
        $upcomingScheduledSessions = $sessionData['upcomingScheduledSessions'];
        $endedSessionsHistory = $sessionData['endedSessionsHistory'];
        $liveCount = $sessionData['liveCount'];
        $sessionsHash = $sessionData['sessionsHash'];

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

        // Educational Files for Student (Files from teachers for enrolled courses/sessions + files uploaded by student)
        $studentFiles = $user ? FileUpload::query()
            ->where(function ($q) use ($user, $allStudentCourseIds, $allSessionIds) {
                // Uploaded by student
                $q->where('user_id', $user->id)
                    // Or specifically targeted to this student
                    ->orWhere('student_user_id', $user->id);

                if (! empty($allStudentCourseIds)) {
                    $q->orWhereIn('course_id', $allStudentCourseIds);
                }

                if (! empty($allSessionIds)) {
                    $q->orWhereIn('live_session_id', $allSessionIds);
                }
            })
            ->with(['uploader', 'course', 'teacherProfile.user'])
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        $teacherFiles = $studentFiles->filter(fn ($f) => (int) $f->user_id !== (int) $user->id)->values();
        $myUploadedFiles = $studentFiles->filter(fn ($f) => (int) $f->user_id === (int) $user->id)->values();

        $allStudentAssignments = $user ? Assignment::query()
            ->where('status', 'published')
            ->where(function ($q) use ($allStudentCourseIds, $allSessionIds) {
                if (! empty($allStudentCourseIds)) {
                    $q->whereIn('course_id', $allStudentCourseIds);
                }
                if (! empty($allSessionIds)) {
                    $q->orWhereIn('live_session_id', $allSessionIds);
                }
            })
            ->with(['course.subject', 'course.teacher.user', 'liveSession.teacherProfile.user', 'submissions' => function ($sq) use ($user) {
                $sq->where('student_user_id', $user->id);
            }, 'questions'])
            ->orderBy('due_at', 'asc')
            ->get() : collect();

        $availableAssignments = $allStudentAssignments->filter(function ($a) use ($completedAssignmentIds) {
            return ! in_array($a->id, $completedAssignmentIds);
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

        $subjectCards = $this->prepareStudentSubjectsData($user, $studentProfile, $upcomingSessions, $enrollments, $package);

        return view('pages.student-portal', [
            'pageTitle'              => 'Student Portal — Learner Dashboard',
            'activeNav'              => 'portal',
            'studentProfile'         => $studentProfile,
            'studentSubjects'        => $studentProfile?->subjects ?: collect(),
            'subjectCards'           => $subjectCards,
            'package'                => $package,
            'hasActivePackage'       => $hasActivePackage,
            'upcomingSessions'          => $upcomingSessions,
            'todaySessions'             => $todaySessions,
            'allSessionsForCalendar'    => $allSessionsForCalendar,
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
            'studentFiles'           => $studentFiles,
            'teacherFiles'           => $teacherFiles,
            'myUploadedFiles'        => $myUploadedFiles,
            'filterCourses'          => $filterCourses,
            'exceptions'             => $exceptions,
            'teacherNotes'           => $teacherNotes,
            'userNotifications'      => $userNotifications,
            'liveCount'              => $liveCount,
            'sessionsHash'           => $sessionsHash,
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

    /**
     * Reusable logic to calculate, filter, and categorize student sessions with high-precision hash.
     */
    public function getStudentSessionsData(?\App\Models\User $user, ?\Carbon\Carbon $now = null): array
    {
        $now = $now ?: now();

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
        $assignedSessionCourseIds = $user ? LiveSession::where('student_user_id', $user->id)
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

        $directLiveSessionIds = $user ? LiveSession::where('student_user_id', $user->id)
            ->pluck('id')
            ->toArray() : [];

        $visibleSessionIds = $user ? LiveSession::visibleToStudent($user->id, $allStudentCourseIds)
            ->pluck('id')
            ->toArray() : [];

        $allSessionIds = array_values(array_unique(array_filter(array_merge(
            $directLiveSessionIds,
            $allStudentSessionIds,
            $visibleSessionIds
        ))));

        // Resolve student's approved exceptions / absence excuses
        $approvedExceptionSessionIds = [];
        $approvedExceptionCourseIds = [];
        $hasApprovedGlobalException = false;

        if ($user) {
            $approvedExceptions = ExceptionRequest::where('student_user_id', $user->id)
                ->where('status', 'approved')
                ->get();

            $approvedExceptionSessionIds = $approvedExceptions->whereNotNull('live_session_id')->pluck('live_session_id')->all();
            $approvedExceptionCourseIds = $approvedExceptions->whereNotNull('course_id')->pluck('course_id')->all();
            $hasApprovedGlobalException = $approvedExceptions->contains(fn($e) => (bool)$e->is_global || $e->scope === 'global');
        }

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
            ->filter(function ($session) use ($user, $hasActivePackage, $approvedExceptionSessionIds, $approvedExceptionCourseIds, $hasApprovedGlobalException) {
                // If student has an approved excuse for this session, course, or global, they must NOT see it!
                if ($hasApprovedGlobalException) {
                    return false;
                }
                if (in_array($session->id, $approvedExceptionSessionIds, true)) {
                    return false;
                }
                if ($session->course_id && in_array($session->course_id, $approvedExceptionCourseIds, true)) {
                    return false;
                }

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

        $liveCount = $startingSoonSessions->filter(function ($s) use ($user, $now) {
            return $s->evaluateState($user, $now) === \App\Enums\LiveSessionState::LIVE;
        })->count();

        // Compute high-precision fingerprint signature
        $fingerprintItems = [];
        foreach ($upcomingSessions as $s) {
            $evalState = $s->evaluateState($user, $now);
            $updatedTs = $s->updated_at ? $s->updated_at->timestamp : 0;
            $startTs = $s->effective_start_at ? $s->effective_start_at->timestamp : 0;
            $endTs = $s->effective_end_at ? $s->effective_end_at->timestamp : 0;
            $fingerprintItems[] = "{$s->id}:{$s->status}:{$evalState->value}:{$startTs}:{$endTs}:{$updatedTs}:{$s->meeting_link}:{$s->title}";
        }

        $packageFlag = $hasActivePackage ? '1' : '0';
        $sessionsHash = md5(implode('|', $fingerprintItems) . "|p:{$packageFlag}|s:" . $startingSoonSessions->count() . "|u:" . $upcomingScheduledSessions->count() . "|h:" . $endedSessionsHistory->count());

        // Filter Today's sessions: currently LIVE, or starting today
        $todaySessions = $upcomingSessions->filter(function ($s) use ($user, $now) {
            $state = $s->evaluateState($user, $now);
            if ($state === \App\Enums\LiveSessionState::LIVE) {
                return true;
            }
            return $s->effective_start_at && $s->effective_start_at->isToday();
        })->sortBy(function ($s) use ($user, $now) {
            $isLive = ($s->evaluateState($user, $now) === \App\Enums\LiveSessionState::LIVE);
            $ts = $s->effective_start_at ? $s->effective_start_at->timestamp : PHP_INT_MAX;
            return $isLive ? 0 : $ts;
        })->values();

        // Format all sessions for interactive calendar and easy session management
        $allSessionsForCalendar = $upcomingSessions->map(function ($s) use ($user, $now) {
            $evalState = $s->evaluateState($user, $now);
            $startAt = $s->effective_start_at;
            $endAt = $s->effective_end_at;
            $isLive = ($evalState === \App\Enums\LiveSessionState::LIVE);
            $canJoin = $evalState->canJoin();
            $isToday = $startAt ? $startAt->isToday() : false;
            $isPast = ($endAt && $endAt->isPast()) || ($s->status === 'completed');

            return [
                'id' => $s->id,
                'title' => $s->studentFacingTitle($s->title ?: 'Live Session'),
                'subject' => $s->subject?->name ?: 'Subject',
                'course' => $s->course?->title ?: '',
                'teacher' => $s->teacherProfile?->user?->name ?: 'Teacher',
                'date' => $startAt ? $startAt->format('Y-m-d') : '',
                'time' => $startAt ? $startAt->format('h:i A') : '',
                'iso_date' => $startAt ? $startAt->toIso8601String() : '',
                'duration' => $s->duration_minutes ?: 60,
                'status' => $isLive ? 'live' : ($isPast ? 'completed' : 'scheduled'),
                'status_label' => $evalState->label(),
                'can_join' => $canJoin,
                'is_live' => $isLive,
                'is_today' => $isToday,
                'is_past' => $isPast,
                'join_url' => route('student.meeting.show', ['id' => $s->id]),
            ];
        })->values();

        return [
            'package' => $package,
            'hasActivePackage' => $hasActivePackage,
            'enrollments' => $enrollments,
            'allStudentCourseIds' => $allStudentCourseIds,
            'allSessionIds' => $allSessionIds,
            'upcomingSessions' => $upcomingSessions,
            'todaySessions' => $todaySessions,
            'allSessionsForCalendar' => $allSessionsForCalendar,
            'startingSoonSessions' => $startingSoonSessions,
            'upcomingScheduledSessions' => $upcomingScheduledSessions,
            'endedSessionsHistory' => $endedSessionsHistory,
            'liveCount' => $liveCount,
            'sessionsHash' => $sessionsHash,
        ];
    }

    /**
     * Real-Time AJAX Feed for Student Portal Interactive Sessions Hub.
     */
    public function sessionsFeed(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $clientHash = $request->query('hash');
        $now = now();
        $sessionData = $this->getStudentSessionsData($user, $now);

        if ($clientHash && $clientHash === $sessionData['sessionsHash']) {
            return response()->json([
                'success' => true,
                'has_changes' => false,
                'hash' => $sessionData['sessionsHash'],
                'live_count' => $sessionData['liveCount'],
            ]);
        }

        $isAr = app()->getLocale() === 'ar';
        $userAuth = $user;

        $soonHtml = view('pages.student.sections.partials.session_pane_soon', [
            'startingSoonSessions' => $sessionData['startingSoonSessions'],
            'isAr' => $isAr,
            'userAuth' => $userAuth,
            'now' => $now,
        ])->render();

        $upcomingHtml = view('pages.student.sections.partials.session_pane_upcoming', [
            'upcomingScheduledSessions' => $sessionData['upcomingScheduledSessions'],
            'isAr' => $isAr,
            'userAuth' => $userAuth,
            'now' => $now,
        ])->render();

        $historyHtml = view('pages.student.sections.partials.session_pane_history', [
            'endedSessionsHistory' => $sessionData['endedSessionsHistory'],
            'isAr' => $isAr,
            'userAuth' => $userAuth,
            'now' => $now,
        ])->render();

        $activeLive = $sessionData['todaySessions']->first(fn($s) => $s->evaluateState($user, $now) === \App\Enums\LiveSessionState::LIVE);
        $liveSessionPayload = null;
        if ($activeLive) {
            $liveSessionPayload = [
                'id' => $activeLive->id,
                'title' => $activeLive->studentFacingTitle($isAr ? 'حصة تفاعلية' : 'Live Class'),
                'teacher' => $activeLive->teacherProfile?->user?->name ?: 'Teacher',
                'duration' => $activeLive->duration_minutes ?: 60,
                'join_url' => route('student.meeting.show', ['id' => $activeLive->id]),
                'meeting_link' => $activeLive->meeting_link ?: '',
            ];
        }

        return response()->json([
            'success' => true,
            'has_changes' => true,
            'hash' => $sessionData['sessionsHash'],
            'counts' => [
                'soon' => count($sessionData['startingSoonSessions']),
                'upcoming' => count($sessionData['upcomingScheduledSessions']),
                'history' => count($sessionData['endedSessionsHistory']),
                'live' => $sessionData['liveCount'],
            ],
            'panes' => [
                'soon' => $soonHtml,
                'upcoming' => $upcomingHtml,
                'history' => $historyHtml,
            ],
            'calendar_sessions' => $sessionData['allSessionsForCalendar'],
            'live_session' => $liveSessionPayload,
            'package' => [
                'has_package' => (bool) $sessionData['hasActivePackage'],
                'total' => (int) ($sessionData['package']?->total_sessions ?? 0),
                'used' => (int) ($sessionData['package']?->used_sessions ?? 0),
                'remaining' => (int) ($sessionData['package']?->remaining_sessions ?? 0),
                'template_name' => $sessionData['package']?->packageTemplate?->name ?: ($sessionData['package']?->course?->title ?: ($isAr ? 'الباقة التعليمية' : 'Learning Package')),
            ],
        ]);
    }

    /**
     * Compute comprehensive subject-level progress and remaining sessions for the student.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\StudentProfile|null $studentProfile
     * @param \Illuminate\Support\Collection $upcomingSessions
     * @param \Illuminate\Support\Collection $enrollments
     * @param \App\Models\StudentPackage|null $package
     * @return \Illuminate\Support\Collection
     */
    private function prepareStudentSubjectsData($user, $studentProfile, $upcomingSessions, $enrollments, $package): \Illuminate\Support\Collection
    {
        if (! $user) {
            return collect();
        }

        // 1. Gather all unique Subject models related to this student
        $subjectsMap = collect();

        // From student profile subjects
        if ($studentProfile && $studentProfile->subjects) {
            foreach ($studentProfile->subjects as $subj) {
                if ($subj && ! $subjectsMap->has($subj->id)) {
                    $subjectsMap->put($subj->id, $subj);
                }
            }
        }

        // From enrolled courses
        foreach ($enrollments as $enr) {
            $subj = $enr->course?->subject;
            if ($subj && ! $subjectsMap->has($subj->id)) {
                $subjectsMap->put($subj->id, $subj);
            }
        }

        // From assigned live sessions
        foreach ($upcomingSessions as $sess) {
            $subj = $sess->subject;
            if ($subj && ! $subjectsMap->has($subj->id)) {
                $subjectsMap->put($subj->id, $subj);
            }
        }

        // Fallback: if student has enrolled courses that have no explicit subject relation, create a virtual subject entry per course
        if ($subjectsMap->isEmpty() && $enrollments->isNotEmpty()) {
            foreach ($enrollments as $enr) {
                if ($enr->course) {
                    $virtualSubj = (object) [
                        'id' => 'course_' . $enr->course->id,
                        'name' => $enr->course->title,
                        'category' => (object) ['name' => __('app.academic_curriculum')],
                        'description' => $enr->course->description,
                        'is_virtual' => true,
                        'course' => $enr->course,
                    ];
                    $subjectsMap->put('course_' . $enr->course->id, $virtualSubj);
                }
            }
        }

        // Color palettes for sleek dark/light aesthetics
        $palettes = [
            [
                'name' => 'teal',
                'badge' => 'bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 border-teal-200 dark:border-teal-800',
                'pill' => 'bg-teal-600 text-white',
                'progress' => 'from-teal-500 to-emerald-400',
                'icon_bg' => 'bg-teal-500/10 dark:bg-teal-500/20 text-teal-600 dark:text-teal-400 border-teal-500/20',
                'border' => 'border-teal-500/30 dark:border-teal-500/20',
                'accent' => 'text-teal-600 dark:text-teal-400',
                'light_bg' => 'bg-teal-50/50 dark:bg-teal-950/20',
            ],
            [
                'name' => 'indigo',
                'badge' => 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                'pill' => 'bg-indigo-600 text-white',
                'progress' => 'from-indigo-500 to-blue-400',
                'icon_bg' => 'bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                'border' => 'border-indigo-500/30 dark:border-indigo-500/20',
                'accent' => 'text-indigo-600 dark:text-indigo-400',
                'light_bg' => 'bg-indigo-50/50 dark:bg-indigo-950/20',
            ],
            [
                'name' => 'amber',
                'badge' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                'pill' => 'bg-amber-600 text-white',
                'progress' => 'from-amber-500 to-orange-400',
                'icon_bg' => 'bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-500/20',
                'border' => 'border-amber-500/30 dark:border-amber-500/20',
                'accent' => 'text-amber-600 dark:text-amber-400',
                'light_bg' => 'bg-amber-50/50 dark:bg-amber-950/20',
            ],
            [
                'name' => 'rose',
                'badge' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                'pill' => 'bg-rose-600 text-white',
                'progress' => 'from-rose-500 to-pink-400',
                'icon_bg' => 'bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-500/20',
                'border' => 'border-rose-500/30 dark:border-rose-500/20',
                'accent' => 'text-rose-600 dark:text-rose-400',
                'light_bg' => 'bg-rose-50/50 dark:bg-rose-950/20',
            ],
            [
                'name' => 'purple',
                'badge' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                'pill' => 'bg-purple-600 text-white',
                'progress' => 'from-purple-500 to-indigo-400',
                'icon_bg' => 'bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 border-purple-500/20',
                'border' => 'border-purple-500/30 dark:border-purple-500/20',
                'accent' => 'text-purple-600 dark:text-purple-400',
                'light_bg' => 'bg-purple-50/50 dark:bg-purple-950/20',
            ],
        ];

        // Preload package deductions by subject to accurately count deducted sessions per subject
        $packageDeductionsBySubject = [];
        if ($package) {
            $deductTransactions = \App\Models\PackageTransaction::where('student_package_id', $package->id)
                ->where('type', 'session_deduct')
                ->get();

            foreach ($deductTransactions as $tr) {
                if ($tr->live_session_id) {
                    $ls = $upcomingSessions->firstWhere('id', $tr->live_session_id);
                    if ($ls) {
                        $sId = $ls->subject_id ?: ($ls->course?->subject_id);
                        if ($sId) {
                            $packageDeductionsBySubject[$sId] = ($packageDeductionsBySubject[$sId] ?? 0) + 1;
                        }
                    }
                } else {
                    foreach ($subjectsMap as $sId => $sObj) {
                        if (!empty($sObj->name) && str_contains($tr->reason ?? '', $sObj->name)) {
                            $packageDeductionsBySubject[$sId] = ($packageDeductionsBySubject[$sId] ?? 0) + 1;
                            break;
                        }
                    }
                }
            }
        }

        $subjectCards = collect();
        $paletteIndex = 0;

        foreach ($subjectsMap as $subjId => $subj) {
            $palette = $palettes[$paletteIndex % count($palettes)];
            $paletteIndex++;

            // Sessions for this subject
            $subjLiveSessions = $upcomingSessions->filter(function ($s) use ($subjId, $subj) {
                return (string) $s->subject_id === (string) $subjId
                    || (string) ($s->course?->subject_id ?? '') === (string) $subjId
                    || (!empty($subj->name) && (
                        ($s->subject && $s->subject->name === $subj->name) ||
                        (str_contains(mb_strtolower($s->title ?? ''), mb_strtolower($subj->name)))
                    ));
            });

            // Enrolled courses for this subject
            $subjCourses = $enrollments->map(fn($e) => $e->course)->filter(function ($c) use ($subjId, $subj) {
                return $c && (
                    (string) $c->subject_id === (string) $subjId
                    || (!empty($subj->name) && $c->subject?->name === $subj->name)
                );
            })->unique('id');

            // Find teachers
            $teachers = collect();
            foreach ($subjLiveSessions as $ls) {
                $tName = $ls->teacherProfile?->user?->name;
                if ($tName) {
                    $teachers->put($tName, [
                        'name' => $tName,
                        'avatar' => mb_substr($tName, 0, 1),
                        'role' => __('app.course_instructor'),
                    ]);
                }
            }
            foreach ($subjCourses as $sc) {
                $tName = $sc->teacher?->user?->name;
                if ($tName && ! $teachers->has($tName)) {
                    $teachers->put($tName, [
                        'name' => $tName,
                        'avatar' => mb_substr($tName, 0, 1),
                        'role' => __('app.course_instructor'),
                    ]);
                }
            }

            // Fallback teacher lookup from database if no session/course teacher attached yet
            if ($teachers->isEmpty()) {
                $foundTeacher = \App\Models\TeacherProfile::whereHas('subjects', function ($q) use ($subjId, $subj) {
                    $q->where('subjects.id', $subjId);
                    if (!empty($subj->name)) {
                        $q->orWhere('subjects.name', $subj->name);
                    }
                })->with('user')->first();

                if ($foundTeacher && $foundTeacher->user) {
                    $tName = $foundTeacher->user->name;
                    $teachers->put($tName, [
                        'name' => $tName,
                        'avatar' => mb_substr($tName, 0, 1),
                        'role' => __('app.course_instructor'),
                    ]);
                }
            }

            $primaryTeacher = $teachers->first() ?: [
                'name' => __('Academic Teacher'),
                'avatar' => 'T',
                'role' => __('Elite Faculty'),
            ];

            // Attended / completed sessions for this subject
            $attendedLiveCount = $subjLiveSessions->filter(function ($s) {
                return in_array($s->status, ['completed', 'attended'], true)
                    || ($s->effective_end_at && $s->effective_end_at->isPast());
            })->count();

            $completedLessonProgress = $enrollments->filter(function ($e) use ($subjId, $subj) {
                $c = $e->course;
                return $c && (
                    (string) $c->subject_id === (string) $subjId
                    || (!empty($subj->name) && $c->subject?->name === $subj->name)
                );
            })->sum(function ($e) {
                return $e->progress ? $e->progress->count() : 0;
            });

            $subjectDeductions = $packageDeductionsBySubject[$subjId] ?? 0;
            $attendedCount = max($attendedLiveCount, $completedLessonProgress, $subjectDeductions);

            // Upcoming live sessions for this subject
            $upcomingCount = $subjLiveSessions->filter(function ($s) {
                return ! in_array($s->status, ['completed', 'cancelled', 'cancelled_by_teacher'], true)
                    && (! $s->effective_end_at || $s->effective_end_at->isFuture());
            })->count();

            // Next scheduled live session
            $nextSession = $subjLiveSessions->filter(function ($s) use ($user) {
                $state = $s->evaluateState($user);
                return $state !== \App\Enums\LiveSessionState::ENDED
                    && ! in_array($s->status, ['completed', 'cancelled', 'cancelled_by_teacher'], true)
                    && (! $s->effective_end_at || $s->effective_end_at->isFuture());
            })->sortBy(function ($s) {
                return $s->effective_start_at ? $s->effective_start_at->timestamp : PHP_INT_MAX;
            })->first();

            // If no 1-on-1 session is scheduled, search for upcoming group/course session for this subject
            if (! $nextSession) {
                $nextSession = \App\Models\LiveSession::where(function ($q) use ($subjId, $subj, $subjCourses) {
                        $q->where('subject_id', $subjId);
                        if (! empty($subj->name)) {
                            $q->orWhereHas('subject', fn($sq) => $sq->where('name', $subj->name));
                        }
                        if ($subjCourses->isNotEmpty()) {
                            $q->orWhereIn('course_id', $subjCourses->pluck('id'));
                        }
                    })
                    ->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])
                    ->where(function ($q) {
                        $q->whereNull('end_at')->orWhere('end_at', '>', now());
                    })
                    ->where(function ($q) use ($user) {
                        $q->whereNull('student_user_id')
                          ->orWhere('student_user_id', $user->id);
                    })
                    ->orderBy('scheduled_at', 'asc')
                    ->first();
            }

            // Accurate per-subject curriculum and sessions calculation
            $courseRecordedSessions = $subjCourses->sum(fn($c) => $c->sessions ? $c->sessions->count() : 0);
            $totalCourseLiveSessions = $subjCourses->sum(fn($c) => $c->liveSessions ? $c->liveSessions->count() : 0);
            $curriculumTotal = $courseRecordedSessions + $totalCourseLiveSessions;

            if ($subjectsMap->count() === 1) {
                // If student has only 1 enrolled subject, package wallet directly funds this subject
                if ($package && $package->total_sessions > 0) {
                    $totalSessions = max($curriculumTotal, (int) $package->total_sessions);
                    $usedSessions = (int) $package->used_sessions;
                    $remainingSessions = (int) $package->remaining_sessions;
                } else {
                    $totalSessions = max(1, $curriculumTotal, $attendedCount + $upcomingCount);
                    $usedSessions = $attendedCount;
                    $remainingSessions = max(0, $totalSessions - $usedSessions);
                }
            } else {
                // Multi-subject portal: use one-time stored package distribution (never re-distributed)
                $allocatedFromPackage = null;
                if ($package) {
                    if (! $package->is_distributed || empty($package->subject_distribution)) {
                        $package->distributeSessionsOnce();
                        $package->refresh();
                    }

                    $dist = $package->subject_distribution ?? [];
                    $sKey = (string) $subjId;

                    if (isset($dist[$sKey])) {
                        $allocatedFromPackage = (int) $dist[$sKey];
                    } elseif (isset($dist['course_' . $subjId])) {
                        $allocatedFromPackage = (int) $dist['course_' . $subjId];
                    } elseif ($subjCourses->isNotEmpty()) {
                        foreach ($subjCourses as $sc) {
                            if (isset($dist['course_' . $sc->id])) {
                                $allocatedFromPackage = (int) $dist['course_' . $sc->id];
                                break;
                            }
                        }
                    }
                }

                if ($allocatedFromPackage !== null && $allocatedFromPackage > 0) {
                    $totalSessions = max($allocatedFromPackage, $curriculumTotal, $attendedCount + $upcomingCount);
                } elseif ($curriculumTotal > 0) {
                    $totalSessions = max($curriculumTotal, $attendedCount + $upcomingCount);
                } elseif ($package && $package->total_sessions > 0) {
                    $fairShare = (int) max(4, (int) round($package->total_sessions / max(1, $subjectsMap->count())));
                    $totalSessions = max($fairShare, $attendedCount + $upcomingCount);
                } else {
                    $totalSessions = max(4, $attendedCount + $upcomingCount);
                }

                $usedSessions = $attendedCount;
                $remainingSessions = max(0, $totalSessions - $usedSessions);
            }

            $progressPct = $totalSessions > 0
                ? min(100, round(($usedSessions / max(1, $totalSessions)) * 100))
                : 0;

            // Pick icon
            $nameLower = mb_strtolower($subj->name ?? '');
            $icon = 'fa-solid fa-book';
            if (str_contains($nameLower, 'رياض') || str_contains($nameLower, 'جبر') || str_contains($nameLower, 'هندس') || str_contains($nameLower, 'math')) {
                $icon = 'fa-solid fa-square-root-variable';
            } elseif (str_contains($nameLower, 'فيز') || str_contains($nameLower, 'physic')) {
                $icon = 'fa-solid fa-atom';
            } elseif (str_contains($nameLower, 'كيم') || str_contains($nameLower, 'chem')) {
                $icon = 'fa-solid fa-flask';
            } elseif (str_contains($nameLower, 'أحي') || str_contains($nameLower, 'bio')) {
                $icon = 'fa-solid fa-dna';
            } elseif (str_contains($nameLower, 'عرب') || str_contains($nameLower, 'arab')) {
                $icon = 'fa-solid fa-book-quran';
            } elseif (str_contains($nameLower, 'إنجل') || str_contains($nameLower, 'انج') || str_contains($nameLower, 'eng')) {
                $icon = 'fa-solid fa-language';
            } elseif (str_contains($nameLower, 'حاس') || str_contains($nameLower, 'برمج') || str_contains($nameLower, 'cs')) {
                $icon = 'fa-solid fa-laptop-code';
            }

            $subjectCards->push([
                'id' => $subj->id,
                'name' => $subj->name,
                'category_name' => $subj->category?->name ?: __('Academic Subject'),
                'icon' => $icon,
                'palette' => $palette,
                'teacher' => $primaryTeacher,
                'all_teachers' => $teachers->values(),
                'total_sessions' => $totalSessions,
                'used_sessions' => $usedSessions,
                'remaining_sessions' => $remainingSessions,
                'upcoming_count' => $upcomingCount,
                'attended_count' => $attendedCount,
                'progress_pct' => $progressPct,
                'next_session' => $nextSession,
                'courses_count' => $subjCourses->count(),
                'courses' => $subjCourses->values(),
            ]);
        }

        return $subjectCards;
    }
}


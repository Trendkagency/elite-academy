<?php

namespace App\Services\Session;

use App\Models\CourseEnrollment;
use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\MeetingAttendance;
use App\Models\PackageTransaction;
use App\Models\StudentPackage;
use App\Models\StudentSession;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionAttendanceDeductionService
{
    public function __construct(
        protected LiveSessionService $liveSessionService
    ) {}

    /**
     * Process full attendance and package deductions when a teacher starts or attends a live session.
     *
     * Rules:
     * 1. Retrieve all registered students for the session (1-on-1 or group course).
     * 2. Free demo sessions do NOT consume package balances.
     * 3. For each student:
     *    - Check if an ExceptionRequest / Absence Excuse exists for this session or course.
     *    - If Exception is APPROVED:
     *        * DO NOT deduct from student package balance.
     *        * Mark attendance status as 'excused'.
     *        * If previously deducted by mistake, refund the session credit.
     *    - If Exception is NOT approved (rejected, or no exception submitted):
     *        * DEDUCT 1 session from student's active package (respecting idempotency).
     *        * Update attendance status accordingly.
     *
     * @param LiveSession $session The session being started/attended
     * @param User $teacher The teacher starting/attending the session
     * @return array Complete report of deductions, excuses, and student statuses
     */
    public function processTeacherSessionStart(LiveSession $session, User $teacher): array
    {
        // 1. Update session status to 'in_progress' / 'live' if currently scheduled
        if (in_array($session->status, ['scheduled', 'pending'], true)) {
            $session->update([
                'status' => 'in_progress',
                'lifecycle_state' => 'live',
                'start_at' => $session->start_at ?: now(),
            ]);
        }

        // 2. Fetch all student user IDs for this session
        $studentUserIds = $this->getStudentIdsForSession($session);

        if ($studentUserIds->isEmpty()) {
            return [
                'success' => true,
                'session_id' => $session->id,
                'total_students' => 0,
                'deducted_count' => 0,
                'excused_count' => 0,
                'already_deducted_count' => 0,
                'no_package_count' => 0,
                'is_free_demo' => $this->liveSessionService->isSessionFreeDemo($session),
                'message' => 'No enrolled students found for this session.',
                'students' => [],
            ];
        }

        // 3. Free Demo Check
        $isFreeDemo = $this->liveSessionService->isSessionFreeDemo($session);
        if ($isFreeDemo) {
            return [
                'success' => true,
                'session_id' => $session->id,
                'total_students' => $studentUserIds->count(),
                'deducted_count' => 0,
                'excused_count' => 0,
                'already_deducted_count' => 0,
                'no_package_count' => 0,
                'is_free_demo' => true,
                'message' => 'Free Demo Session: No package deductions applied.',
                'students' => $studentUserIds->map(fn ($id) => [
                    'student_user_id' => $id,
                    'action' => 'free_demo',
                    'deducted' => false,
                    'reason' => 'Free Demo Session — Credits Preserved',
                ])->all(),
            ];
        }

        $results = [];
        $deductedCount = 0;
        $excusedCount = 0;
        $alreadyDeductedCount = 0;
        $noPackageCount = 0;

        // 4. Process each student within transaction
        foreach ($studentUserIds as $studentId) {
            $studentResult = $this->processStudentDeduction($session, (int) $studentId, $teacher);
            $results[] = $studentResult;

            match ($studentResult['action']) {
                'deducted' => $deductedCount++,
                'excused' => $excusedCount++,
                'already_deducted' => $alreadyDeductedCount++,
                'no_package' => $noPackageCount++,
                default => null,
            };
        }

        Log::info("[SessionAttendanceDeduction] Teacher #{$teacher->id} processed Session #{$session->id}: {$deductedCount} deducted, {$excusedCount} excused, {$alreadyDeductedCount} already deducted, {$noPackageCount} without package.");

        return [
            'success' => true,
            'session_id' => $session->id,
            'total_students' => $studentUserIds->count(),
            'deducted_count' => $deductedCount,
            'excused_count' => $excusedCount,
            'already_deducted_count' => $alreadyDeductedCount,
            'no_package_count' => $noPackageCount,
            'is_free_demo' => false,
            'message' => "Session processed successfully: {$deductedCount} deducted, {$excusedCount} excused.",
            'students' => $results,
        ];
    }

    /**
     * Process individual student deduction logic with exception validation.
     */
    public function processStudentDeduction(LiveSession $session, int $studentUserId, ?User $actor = null): array
    {
        return DB::transaction(function () use ($session, $studentUserId, $actor) {
            $student = User::find($studentUserId);
            if (! $student) {
                return [
                    'student_user_id' => $studentUserId,
                    'student_name' => 'Unknown',
                    'action' => 'not_found',
                    'deducted' => false,
                    'message' => 'Student user record not found.',
                ];
            }

            $profile = \App\Models\StudentProfile::where('user_id', $studentUserId)->with('gradeLevel')->first();
            $studentCode = $profile?->student_code ?: ('STU-' . str_pad((string) $student->id, 5, '0', STR_PAD_LEFT));
            $gradeName = $profile?->gradeLevel?->name ?: '';
            $schoolName = $profile?->school_name ?: 'Elite Academy';

            // 1. Check for Exception / Absence Excuse for this session
            $exception = $this->getStudentExceptionForSession($studentUserId, $session);

            // CASE A: Approved Exception -> DO NOT DEDUCT
            if ($exception && $exception->status === 'approved') {
                // If it was somehow previously deducted for this session, refund it!
                $this->refundIfPreviouslyDeducted($session, $studentUserId, $actor, "Approved Exception #{$exception->id} - Session Restored");

                // Update StudentSession attendance record to 'excused'
                StudentSession::updateOrCreate(
                    [
                        'student_user_id' => $studentUserId,
                        'live_session_id' => $session->id,
                    ],
                    [
                        'attendance_status' => 'excused',
                        'session_status' => 'completed',
                        'completed_at' => now(),
                    ]
                );

                return [
                    'student_user_id' => $studentUserId,
                    'student_name' => $student->name,
                    'student_code' => $studentCode,
                    'grade' => $gradeName,
                    'school' => $schoolName,
                    'action' => 'excused',
                    'status' => 'excused',
                    'deducted' => false,
                    'exception_id' => $exception->id,
                    'exception_status' => 'approved',
                    'reason' => $exception->reason ?: 'Approved Medical / Absence Excuse',
                    'message' => 'Student is excused. Package balance was NOT deducted.',
                ];
            }

            // CASE B: Unexcused (Rejected or No Exception) -> DEDUCT SESSION
            // 2. Check if already deducted to ensure idempotency
            $alreadyDeducted = PackageTransaction::whereHas('studentPackage', function ($q) use ($studentUserId) {
                $q->where('student_user_id', $studentUserId);
            })
            ->where('live_session_id', $session->id)
            ->where('type', 'session_deduct')
            ->exists();

            if ($alreadyDeducted) {
                return [
                    'student_user_id' => $studentUserId,
                    'student_name' => $student->name,
                    'student_code' => $studentCode,
                    'grade' => $gradeName,
                    'school' => $schoolName,
                    'action' => 'already_deducted',
                    'status' => 'present',
                    'deducted' => false,
                    'exception_id' => $exception?->id,
                    'exception_status' => $exception?->status,
                    'reason' => $exception?->reason,
                    'message' => 'Session was already deducted for this student.',
                ];
            }

            // 3. Find active package for this student
            $package = $this->findActivePackageForStudent($studentUserId, $session);

            if (! $package) {
                return [
                    'student_user_id' => $studentUserId,
                    'student_name' => $student->name,
                    'student_code' => $studentCode,
                    'grade' => $gradeName,
                    'school' => $schoolName,
                    'action' => 'no_package',
                    'status' => 'no_package',
                    'deducted' => false,
                    'exception_id' => $exception?->id,
                    'exception_status' => $exception?->status,
                    'reason' => $exception?->reason,
                    'message' => 'No active package with available session credits found.',
                ];
            }

            // 4. Perform the deduction
            $reason = ($exception && $exception->status === 'rejected')
                ? "Exception Request #{$exception->id} Rejected - Live Session #{$session->id} Deducted"
                : "Attendance for Live Session #{$session->id} (" . ($session->title ?: 'Live Stream') . ')';

            $performedBy = $actor ? $actor->id : auth()->id();
            $balanceBefore = $package->remaining_sessions;

            $package->remaining_sessions--;
            $package->used_sessions++;
            if ($package->remaining_sessions <= 0) {
                $package->status = 'exhausted';
            }
            $package->save();

            PackageTransaction::create([
                'student_package_id' => $package->id,
                'live_session_id' => $session->id,
                'type' => 'session_deduct',
                'sessions_delta' => -1,
                'balance_before' => $balanceBefore,
                'balance_after' => $package->remaining_sessions,
                'reason' => $reason,
                'performed_by' => $performedBy,
            ]);

            // Ensure StudentSession record exists
            StudentSession::updateOrCreate(
                [
                    'student_user_id' => $studentUserId,
                    'live_session_id' => $session->id,
                ],
                [
                    'attendance_status' => 'present',
                ]
            );

            return [
                'student_user_id' => $studentUserId,
                'student_name' => $student->name,
                'student_code' => $studentCode,
                'grade' => $gradeName,
                'school' => $schoolName,
                'action' => 'deducted',
                'status' => 'present',
                'deducted' => true,
                'remaining_sessions' => $package->remaining_sessions,
                'package_id' => $package->id,
                'balance_before' => $balanceBefore,
                'balance_after' => $package->remaining_sessions,
                'exception_id' => $exception?->id,
                'exception_status' => $exception?->status,
                'reason' => $reason,
                'message' => 'Session credit successfully deducted from student package.',
            ];
        });
    }

    /**
     * Retrieve all student user IDs belonging to a session.
     */
    public function getStudentIdsForSession(LiveSession $session): Collection
    {
        $ids = collect();

        // 1. Direct 1-to-1 Session
        if ($session->student_user_id) {
            $ids->push((int) $session->student_user_id);
        }

        // 2. 1-to-1 Recurring Schedule instance
        if ($session->recurringSchedule?->student_user_id) {
            $ids->push((int) $session->recurringSchedule->student_user_id);
        }

        // 3. Course Enrolled Students (Group Sessions)
        if ($session->course_id) {
            $enrolled = CourseEnrollment::where('course_id', $session->course_id)
                ->pluck('student_user_id')
                ->filter();
            $ids = $ids->merge($enrolled);
        }

        // 4. Existing StudentSession records
        $existing = StudentSession::where('live_session_id', $session->id)
            ->pluck('student_user_id')
            ->filter();
        $ids = $ids->merge($existing);

        // 5. Existing MeetingAttendance records
        $attended = MeetingAttendance::where('live_session_id', $session->id)
            ->pluck('student_user_id')
            ->filter();
        $ids = $ids->merge($attended);

        return $ids->map(fn ($id) => (int) $id)->unique()->values();
    }

    /**
     * Find matching ExceptionRequest for student and session.
     */
    public function getStudentExceptionForSession(int $studentUserId, LiveSession $session): ?ExceptionRequest
    {
        return ExceptionRequest::where('student_user_id', $studentUserId)
            ->where(function ($query) use ($session) {
                $query->where('live_session_id', $session->id);
                if ($session->course_id) {
                    $query->orWhere(function ($q) use ($session) {
                        $q->where('scope', 'course')->where('course_id', $session->course_id);
                    });
                }
            })
            ->latest('id')
            ->first();
    }

    /**
     * Find active package with available sessions, prioritizing course match.
     */
    public function findActivePackageForStudent(int $studentUserId, LiveSession $session): ?StudentPackage
    {
        $now = now();

        // 1. Try course-specific active package
        if ($session->course_id) {
            $package = StudentPackage::where('student_user_id', $studentUserId)
                ->where('course_id', $session->course_id)
                ->where('status', 'active')
                ->where('remaining_sessions', '>', 0)
                ->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
                })
                ->lockForUpdate()
                ->first();

            if ($package) {
                return $package;
            }
        }

        // 2. Fallback to any active package with remaining balance
        return StudentPackage::where('student_user_id', $studentUserId)
            ->where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            })
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();
    }

    /**
     * Refund session if previously deducted.
     */
    public function refundIfPreviouslyDeducted(LiveSession $session, int $studentUserId, ?User $actor = null, string $reason = 'Session Refund'): bool
    {
        $transaction = PackageTransaction::whereHas('studentPackage', fn ($q) => $q->where('student_user_id', $studentUserId))
            ->where('live_session_id', $session->id)
            ->where('type', 'session_deduct')
            ->latest('id')
            ->first();

        if ($transaction && $transaction->studentPackage) {
            $package = $transaction->studentPackage;
            return $package->refundSession($session->id, $reason);
        }

        return false;
    }
}

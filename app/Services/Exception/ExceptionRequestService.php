<?php

namespace App\Services\Exception;

use App\Models\ExceptionRequest;
use App\Models\PackageTransaction;
use App\Models\StudentPackage;
use App\Models\User;
use App\Services\Notification\FcmNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExceptionRequestService
{
    public function __construct(
        protected FcmNotificationService $fcmService
    ) {}

    /**
     * Approve an exception request:
     * - Status becomes approved.
     * - The session is NOT decreased from the student.
     * - If it was previously rejected and deducted, the session credit is refunded.
     */
    public function approve(ExceptionRequest $request, User $reviewer, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($request, $reviewer, $notes) {
            $wasRejected = $request->status === 'rejected';

            $request->update([
                'status' => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'admin_notes' => $notes ?: $request->admin_notes,
            ]);

            // If it was previously rejected and caused a package deduction, refund the session
            if ($wasRejected) {
                $this->refundDeductedSession($request, $reviewer);
            }

            try {
                $this->fcmService->notifyExceptionStatus($request);
                $student = $request->studentUser ?: User::find($request->student_user_id);
                if ($student) {
                    $scopeName = $request->is_global || $request->scope === 'global' ? 'Global Exception' : 'Course Exception';
                    $this->fcmService->notifyAdminApproval($student, $scopeName, $request->reason ?: 'Request approved');
                }
            } catch (\Throwable $e) {
                Log::warning("[ExceptionRequestService] Failed to send approval notification: {$e->getMessage()}");
            }

            return true;
        });
    }

    /**
     * Reject an exception request:
     * - Status becomes rejected.
     * - The session IS decreased (deducted) from the student's active package.
     */
    public function reject(ExceptionRequest $request, User $reviewer, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($request, $reviewer, $notes) {
            $wasApproved = $request->status === 'approved';

            $request->update([
                'status' => 'rejected',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'admin_notes' => $notes ?: $request->admin_notes,
            ]);

            // Deduct session from student package if not already deducted
            $this->deductStudentPackageSession($request, $reviewer);

            try {
                $this->fcmService->notifyExceptionStatus($request);
            } catch (\Throwable $e) {
                Log::warning("[ExceptionRequestService] Failed to send rejection notification: {$e->getMessage()}");
            }

            return true;
        });
    }

    /**
     * Deduct 1 session from student's active package upon rejection
     */
    protected function deductStudentPackageSession(ExceptionRequest $request, User $reviewer): bool
    {
        $studentId = $request->student_user_id;

        // Check if a deduction for this exception request or live session already exists
        $alreadyDeducted = PackageTransaction::whereHas('studentPackage', fn ($q) => $q->where('student_user_id', $studentId))
            ->where(function ($q) use ($request) {
                if ($request->live_session_id) {
                    $q->where('live_session_id', $request->live_session_id);
                }
                $q->orWhere('reason', 'like', "%Exception Request #{$request->id}%");
            })
            ->where('type', 'session_deduct')
            ->exists();

        if ($alreadyDeducted) {
            return false;
        }

        // Find student package: prefer package matching the course, or active package
        $package = null;
        if ($request->course_id) {
            $package = StudentPackage::where('student_user_id', $studentId)
                ->where('course_id', $request->course_id)
                ->where('status', 'active')
                ->where('remaining_sessions', '>', 0)
                ->first();
        }

        if (! $package) {
            $package = StudentPackage::where('student_user_id', $studentId)
                ->where('status', 'active')
                ->where('remaining_sessions', '>', 0)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if ($package) {
            $reason = "Exception Request #{$request->id} Rejected - Unexcused Absence";
            return $package->deductSession($request->live_session_id, $reason);
        }

        Log::info("[ExceptionRequestService] Student #{$studentId} has no active package with remaining sessions to deduct for Exception #{$request->id}.");
        return false;
    }

    /**
     * Refund 1 session to student's package if previously deducted for this exception
     */
    protected function refundDeductedSession(ExceptionRequest $request, User $reviewer): bool
    {
        $studentId = $request->student_user_id;

        // Find the transaction that deducted for this exception
        $transaction = PackageTransaction::whereHas('studentPackage', fn ($q) => $q->where('student_user_id', $studentId))
            ->where(function ($q) use ($request) {
                if ($request->live_session_id) {
                    $q->where('live_session_id', $request->live_session_id);
                }
                $q->orWhere('reason', 'like', "%Exception Request #{$request->id}%");
            })
            ->where('type', 'session_deduct')
            ->latest('created_at')
            ->first();

        if ($transaction && $transaction->studentPackage) {
            $package = $transaction->studentPackage;
            $reason = "Exception Request #{$request->id} Approved - Session Restored";
            return $package->refundSession($request->live_session_id, $reason);
        }

        return false;
    }
}

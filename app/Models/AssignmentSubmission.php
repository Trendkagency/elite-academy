<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentSubmission extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'assignment_id',
        'homework_assignment_id',
        'live_session_id',
        'student_user_id',
        'course_enrollment_id',
        'started_at',
        'submitted_at',
        'status',
        'grade',
        'score',
        'total_points',
        'percentage',
        'passing_score',
        'attempt_number',
        'current_step_index',
        'teacher_notes',
        'evaluation_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'status' => SubmissionStatus::class,
        'grade' => 'float',
        'score' => 'float',
        'total_points' => 'float',
        'percentage' => 'float',
        'passing_score' => 'float',
        'attempt_number' => 'integer',
        'current_step_index' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Only notify teacher when a student has actually SUBMITTED (not when they merely start IN_PROGRESS).
        static::created(function (AssignmentSubmission $submission) {
            $statusVal = $submission->status instanceof SubmissionStatus ? $submission->status->value : (string) $submission->status;
            if (in_array($statusVal, ['submitted', 'completed'], true)) {
                $service = app(\App\Services\Notification\FcmNotificationService::class);
                $service->notifyTeacherAssignmentSubmitted($submission);
                try { $service->notifyAdminAssignmentSubmitted($submission); } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::error('[FCM] notifyAdminAssignmentSubmitted failed: ' . $e->getMessage()); }
            }
        });

        // When status transitions to submitted/completed (e.g. via AssignmentEvaluationService), notify.
        // Guard against double-notification: only fires when status CHANGES, not on initial create.
        static::updated(function (AssignmentSubmission $submission) {
            if ($submission->wasChanged('status')) {
                $statusVal = $submission->status instanceof SubmissionStatus ? $submission->status->value : (string) $submission->status;
                if (in_array($statusVal, ['submitted', 'completed'], true)) {
                    $service = app(\App\Services\Notification\FcmNotificationService::class);
                    $service->notifyTeacherAssignmentSubmitted($submission);
                    try { $service->notifyAdminAssignmentSubmitted($submission); } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::error('[FCM] notifyAdminAssignmentSubmitted failed: ' . $e->getMessage()); }
                }
            }
        });
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id');
    }

    public function courseEnrollment(): BelongsTo
    {
        return $this->enrollment();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssignmentSubmissionAnswer::class, 'submission_id');
    }

    public function isPassed(): bool
    {
        $effectiveScore = $this->percentage ?? $this->grade ?? $this->score;
        $passing = $this->passing_score ?? $this->assignment?->passing_score ?? $this->assignment?->passing_grade ?? 70.0;

        return $effectiveScore !== null && (float) $effectiveScore >= (float) $passing;
    }
}

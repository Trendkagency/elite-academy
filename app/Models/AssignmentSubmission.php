<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentSubmission extends Model
{
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
        return $this->percentage !== null && $this->passing_score !== null && $this->percentage >= $this->passing_score;
    }
}

<?php

namespace App\Models;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPackage extends Model
{
    use SoftDeletes;

    protected $table = 'student_packages';

    protected $fillable = [
        'student_user_id',
        'course_id',
        'package_template_id',
        'total_sessions',
        'used_sessions',
        'remaining_sessions',
        'subject_distribution',
        'is_distributed',
        'status',
        'activated_at',
        'expires_at',
    ];

    protected $casts = [
        'total_sessions' => 'integer',
        'used_sessions' => 'integer',
        'remaining_sessions' => 'integer',
        'subject_distribution' => 'array',
        'is_distributed' => 'boolean',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Booted model events: automatically distribute sessions once when a package is created.
     */
    protected static function booted(): void
    {
        static::created(function (StudentPackage $package) {
            $package->distributeSessionsOnce();
        });
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function packageTemplate(): BelongsTo
    {
        return $this->belongsTo(PackageTemplate::class, 'package_template_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PackageTransaction::class, 'student_package_id');
    }

    public function deductSession(?int $liveSessionId = null, string $reason = 'Session Attendance'): bool
    {
        if ($this->remaining_sessions <= 0 || $this->status !== 'active') {
            return false;
        }

        $balanceBefore = $this->remaining_sessions;
        $this->remaining_sessions--;
        $this->used_sessions++;

        if ($this->remaining_sessions <= 0) {
            $this->status = 'exhausted';
        }

        $this->save();

        PackageTransaction::create([
            'student_package_id' => $this->id,
            'live_session_id' => $liveSessionId,
            'type' => 'session_deduct',
            'sessions_delta' => -1,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->remaining_sessions,
            'reason' => $reason,
            'performed_by' => auth()->id(),
        ]);

        return true;
    }

    public function refundSession(?int $liveSessionId = null, string $reason = 'Session Refund'): bool
    {
        $balanceBefore = $this->remaining_sessions;
        $this->remaining_sessions++;
        if ($this->used_sessions > 0) {
            $this->used_sessions--;
        }

        if ($this->status === 'exhausted' && $this->remaining_sessions > 0) {
            $this->status = 'active';
        }

        $this->save();

        PackageTransaction::create([
            'student_package_id' => $this->id,
            'live_session_id'    => $liveSessionId,
            'type'               => 'session_refund',
            'sessions_delta'     => 1,
            'balance_before'     => $balanceBefore,
            'balance_after'      => $this->remaining_sessions,
            'reason'             => $reason,
            'performed_by'       => auth()->id(),
            'created_at'         => now(),
        ]);

        return true;
    }

    /**
     * Renew the package: reset session credits, re-activate status, and optionally extend expiry.
     */
    public function renewPackage(
        int $newTotalSessions,
        ?int $packageTemplateId = null,
        ?\Carbon\Carbon $newExpiresAt = null,
        string $reason = 'Package Renewal'
    ): bool {
        $balanceBefore = $this->remaining_sessions;

        $this->total_sessions      = $newTotalSessions;
        $this->remaining_sessions  = $newTotalSessions;
        $this->used_sessions       = 0;
        $this->status              = 'active';
        $this->activated_at        = now();

        if ($packageTemplateId) {
            $this->package_template_id = $packageTemplateId;
        }
        if ($newExpiresAt !== null) {
            $this->expires_at = $newExpiresAt;
        }

        $this->save();

        // Re-distribute sessions for the renewed package credits once
        $this->distributeSessionsOnce(force: true);

        PackageTransaction::create([
            'student_package_id' => $this->id,
            'live_session_id'    => null,
            'type'               => 'renewal',
            'sessions_delta'     => $newTotalSessions,
            'balance_before'     => $balanceBefore,
            'balance_after'      => $newTotalSessions,
            'reason'             => $reason,
            'performed_by'       => auth()->id(),
            'created_at'         => now(),
        ]);

        return true;
    }

    /**
     * Distribute total session credits across student's subjects/courses ONCE ONLY.
     * Prevents re-distribution or repeated allocation on subsequent operations.
     *
     * @param array|null $customDistribution Optional pre-defined distribution [subject_id => count]
     * @param bool $force Force re-distribution (used ONLY during package renewal)
     * @return bool
     */
    public function distributeSessionsOnce(?array $customDistribution = null, bool $force = false): bool
    {
        // If already distributed and not forced, strictly do NOT repeat distribution!
        if ($this->is_distributed && !empty($this->subject_distribution) && !$force) {
            // If already distributed with real subjects, never re-distribute
            if (! (count($this->subject_distribution) === 1 && isset($this->subject_distribution['general']))) {
                return false;
            }
        }

        $total = (int) $this->total_sessions;
        if ($total <= 0) {
            return false;
        }

        // 1. If custom distribution passed, validate and store
        if (!empty($customDistribution) && is_array($customDistribution)) {
            $this->subject_distribution = $customDistribution;
            $this->is_distributed = true;
            $this->saveQuietly();
            return true;
        }

        // 2. If package is restricted to a specific course
        if ($this->course_id) {
            $course = Course::find($this->course_id);
            $key = $course?->subject_id ? (string) $course->subject_id : 'course_' . $this->course_id;
            $this->subject_distribution = [$key => $total];
            $this->is_distributed = true;
            $this->saveQuietly();
            return true;
        }

        // 3. General package: find enrolled subjects for student
        $studentUserId = $this->student_user_id;
        $subjectKeys = collect();

        // Enrolled courses
        $enrollments = CourseEnrollment::where('student_user_id', $studentUserId)
            ->with('course.subject')
            ->get();

        foreach ($enrollments as $enr) {
            if ($enr->course) {
                if ($enr->course->subject_id) {
                    $subjectKeys->push((string) $enr->course->subject_id);
                } else {
                    $subjectKeys->push('course_' . $enr->course->id);
                }
            }
        }

        // Also check any live sessions already assigned directly to student with subjects
        if ($subjectKeys->isEmpty()) {
            $sessionSubjects = LiveSession::where('student_user_id', $studentUserId)
                ->whereNotNull('subject_id')
                ->pluck('subject_id')
                ->map(fn($id) => (string) $id);
            $subjectKeys = $subjectKeys->merge($sessionSubjects);
        }

        $uniqueKeys = $subjectKeys->unique()->values();
        $count = $uniqueKeys->count();

        $distribution = [];
        if ($count > 0) {
            $baseShare = intdiv($total, $count);
            $remainder = $total % $count;

            foreach ($uniqueKeys as $index => $key) {
                $distribution[$key] = $baseShare + ($index < $remainder ? 1 : 0);
            }
        } else {
            // No enrolled subjects yet: mark as general
            $distribution = ['general' => $total];
        }

        $this->subject_distribution = $distribution;
        $this->is_distributed = true;
        $this->saveQuietly();

        return true;
    }
}


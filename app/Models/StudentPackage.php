<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentPackage extends Model
{
    protected $table = 'student_packages';

    protected $fillable = [
        'student_user_id',
        'course_id',
        'package_template_id',
        'total_sessions',
        'used_sessions',
        'remaining_sessions',
        'status',
        'activated_at',
        'expires_at',
    ];

    protected $casts = [
        'total_sessions' => 'integer',
        'used_sessions' => 'integer',
        'remaining_sessions' => 'integer',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

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
            'live_session_id' => $liveSessionId,
            'type' => 'session_refund',
            'sessions_delta' => 1,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->remaining_sessions,
            'reason' => $reason,
            'performed_by' => auth()->id(),
        ]);

        return true;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'live_session_id',
        'attendance_status',
        'assignment_status',
        'assignment_score',
        'session_status',
        'completed_at',
    ];

    protected $casts = [
        'assignment_score' => 'float',
        'completed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::updated(function (StudentSession $session) {
            if ($session->wasChanged('attendance_status') && $session->attendance_status === 'absent') {
                $liveSession = $session->liveSession ?: LiveSession::find($session->live_session_id);
                $student     = $session->studentUser ?: User::find($session->student_user_id);
                if ($liveSession && $student) {
                    app(\App\Services\Notification\FcmNotificationService::class)->notifyTeacherStudentAbsent($liveSession, $student);
                }
            }
        });
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }
}

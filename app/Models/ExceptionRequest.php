<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExceptionRequest extends Model
{
    protected $table = 'exception_requests';

    protected $fillable = [
        'student_user_id',
        'live_session_id',
        'homework_assignment_id',
        'course_id',
        'is_global',
        'scope',
        'reason',
        'attachment_path',
        'status',
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::updated(function (ExceptionRequest $request) {
            if ($request->wasChanged('status') && $request->status === 'approved') {
                $student = $request->studentUser ?: User::find($request->student_user_id);
                if ($student) {
                    $service = app(\App\Services\Notification\FcmNotificationService::class);
                    $scopeName = $request->is_global || $request->scope === 'global' ? 'Global Exception' : 'Course Exception';
                    $service->notifyAdminApproval($student, $scopeName, $request->reason ?: 'Request approved');
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

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}

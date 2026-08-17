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

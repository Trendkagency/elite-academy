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
        'reason',
        'attachment_path',
        'status',
    ];

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }
}

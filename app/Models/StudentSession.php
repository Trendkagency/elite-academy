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

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }
}

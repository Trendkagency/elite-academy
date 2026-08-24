<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendance extends Model
{
    use HasFactory;

    protected $table = 'meeting_attendances';

    protected $fillable = [
        'live_session_id',
        'student_user_id',
        'joined_at',
        'left_at',
        'last_seen_at',
        'duration_seconds',
        'status',
        'ip_address',
        'user_agent',
        'provider_slug',
        'provider_meeting_id',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}

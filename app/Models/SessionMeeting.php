<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionMeeting extends Model
{
    use HasFactory;

    protected $table = 'session_meetings';

    protected $fillable = [
        'live_session_id',
        'meeting_provider_id',
        'provider_slug',
        'provider_meeting_id',
        'passcode',
        'join_url',
        'host_url',
        'encrypted_configuration',
        'status',
    ];

    protected $casts = [
        'encrypted_configuration' => 'encrypted:array',
    ];

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(MeetingProvider::class, 'meeting_provider_id');
    }
}

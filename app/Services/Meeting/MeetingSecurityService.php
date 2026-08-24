<?php

namespace App\Services\Meeting;

use App\Models\LiveSession;
use App\Models\MeetingSecurityEvent;
use App\Models\User;

class MeetingSecurityService
{
    /**
     * Log security event.
     */
    public function logEvent(LiveSession $session, User $user, string $eventType, array $metadata = [], ?string $ipAddress = null, ?string $userAgent = null): MeetingSecurityEvent
    {
        return MeetingSecurityEvent::create([
            'live_session_id' => $session->id,
            'user_id' => $user->id,
            'event_type' => $eventType,
            'metadata' => $metadata,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}

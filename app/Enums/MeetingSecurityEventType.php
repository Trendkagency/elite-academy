<?php

namespace App\Enums;

enum MeetingSecurityEventType: string
{
    case MEETING_JOIN = 'MEETING_JOIN';
    case MEETING_LEAVE = 'MEETING_LEAVE';
    case MEETING_RECONNECT = 'MEETING_RECONNECT';
    case MEETING_ACCESS_DENIED = 'MEETING_ACCESS_DENIED';
    case MEETING_ACCESS_EXPIRED = 'MEETING_ACCESS_EXPIRED';
    case TOKEN_GENERATED = 'TOKEN_GENERATED';
    case TOKEN_REJECTED = 'TOKEN_REJECTED';
    case TAB_HIDDEN = 'TAB_HIDDEN';
    case WINDOW_BLURRED = 'WINDOW_BLURRED';
    case FULLSCREEN_EXIT = 'FULLSCREEN_EXIT';
    case SUSPICIOUS_ACTIVITY = 'SUSPICIOUS_ACTIVITY';

    public function label(): string
    {
        return match ($this) {
            self::MEETING_JOIN => 'Joined Live Session',
            self::MEETING_LEAVE => 'Left Live Session',
            self::MEETING_RECONNECT => 'Reconnected to Meeting',
            self::MEETING_ACCESS_DENIED => 'Meeting Access Denied',
            self::MEETING_ACCESS_EXPIRED => 'Meeting Access Expired',
            self::TOKEN_GENERATED => 'Access Token Generated',
            self::TOKEN_REJECTED => 'Invalid Access Token Rejected',
            self::TAB_HIDDEN => 'Meeting Tab Hidden',
            self::WINDOW_BLURRED => 'Browser Window Blurred',
            self::FULLSCREEN_EXIT => 'Fullscreen Mode Exited',
            self::SUSPICIOUS_ACTIVITY => 'Suspicious Behavior Logged',
        };
    }
}

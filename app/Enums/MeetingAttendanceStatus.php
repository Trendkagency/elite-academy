<?php

namespace App\Enums;

enum MeetingAttendanceStatus: string
{
    case JOINED = 'joined';
    case ACTIVE = 'active';
    case LEFT = 'left';
    case DISCONNECTED = 'disconnected';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::JOINED => 'Joined Session',
            self::ACTIVE => 'Active in Meeting',
            self::LEFT => 'Left Session',
            self::DISCONNECTED => 'Disconnected Unexpectedly',
            self::EXPIRED => 'Session Expired',
        };
    }
}

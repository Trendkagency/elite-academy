<?php

namespace App\Enums;

enum MeetingProviderSlug: string
{
    case ZOOM = 'zoom';
    case GOOGLE_MEET = 'google_meet';
    case TEAMS = 'teams';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::ZOOM => 'Zoom Meeting',
            self::GOOGLE_MEET => 'Google Meet',
            self::TEAMS => 'Microsoft Teams',
            self::CUSTOM => 'Custom Embedded Player',
        };
    }
}

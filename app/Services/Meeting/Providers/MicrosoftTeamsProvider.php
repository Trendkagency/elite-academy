<?php

namespace App\Services\Meeting\Providers;

use App\Models\LiveSession;
use App\Models\User;
use App\Services\Meeting\Contracts\MeetingProviderInterface;

class MicrosoftTeamsProvider implements MeetingProviderInterface
{
    public function getName(): string
    {
        return 'Microsoft Teams';
    }

    public function getSlug(): string
    {
        return 'teams';
    }

    public function supportsEmbedding(): bool
    {
        return false;
    }

    public function generateAccessPayload(LiveSession $session, User $user): array
    {
        $sessionMeeting = $session->sessionMeeting;
        $joinUrl = $sessionMeeting?->join_url ?: ($session->meeting_link ?: 'https://teams.microsoft.com/l/meetup-join/test');

        return [
            'provider' => $this->getSlug(),
            'supports_embedding' => false,
            'embed_type' => 'external_launch',
            'stream_url' => $joinUrl,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ];
    }

    public function createMeeting(LiveSession $session, array $options = []): array
    {
        $meetingId = 'TEAMS-' . rand(10000000, 99999999);
        $joinUrl = "https://teams.microsoft.com/l/meetup-join/{$meetingId}";

        return [
            'provider_meeting_id' => $meetingId,
            'passcode' => null,
            'join_url' => $joinUrl,
            'host_url' => $joinUrl,
        ];
    }
}

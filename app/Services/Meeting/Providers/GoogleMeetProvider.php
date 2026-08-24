<?php

namespace App\Services\Meeting\Providers;

use App\Models\LiveSession;
use App\Models\User;
use App\Services\Meeting\Contracts\MeetingProviderInterface;

class GoogleMeetProvider implements MeetingProviderInterface
{
    public function getName(): string
    {
        return 'Google Meet';
    }

    public function getSlug(): string
    {
        return 'google_meet';
    }

    public function supportsEmbedding(): bool
    {
        return false;
    }

    public function generateAccessPayload(LiveSession $session, User $user): array
    {
        $sessionMeeting = $session->sessionMeeting;
        $joinUrl = $sessionMeeting?->join_url ?: ($session->meeting_link ?: 'https://meet.google.com/test-live-stream');

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
        $code = strtolower(substr(md5(uniqid()), 0, 3) . '-' . substr(md5(uniqid()), 3, 4) . '-' . substr(md5(uniqid()), 7, 3));
        $joinUrl = "https://meet.google.com/{$code}";

        return [
            'provider_meeting_id' => $code,
            'passcode' => null,
            'join_url' => $joinUrl,
            'host_url' => $joinUrl,
        ];
    }
}

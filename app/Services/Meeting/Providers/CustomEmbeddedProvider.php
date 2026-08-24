<?php

namespace App\Services\Meeting\Providers;

use App\Models\LiveSession;
use App\Models\User;
use App\Services\Meeting\Contracts\MeetingProviderInterface;

class CustomEmbeddedProvider implements MeetingProviderInterface
{
    public function getName(): string
    {
        return 'Custom Embedded Player';
    }

    public function getSlug(): string
    {
        return 'custom';
    }

    public function supportsEmbedding(): bool
    {
        return true;
    }

    public function generateAccessPayload(LiveSession $session, User $user): array
    {
        $sessionMeeting = $session->sessionMeeting;
        $joinUrl = $sessionMeeting?->join_url ?: ($session->meeting_link ?: 'https://stream.elite-academy.com/live/' . $session->id);

        return [
            'provider' => $this->getSlug(),
            'supports_embedding' => true,
            'embed_type' => 'webrtc_custom',
            'stream_url' => $joinUrl,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ];
    }

    public function createMeeting(LiveSession $session, array $options = []): array
    {
        $meetingId = 'EMBED-' . $session->id . '-' . time();
        $joinUrl = "https://stream.elite-academy.com/live/{$session->id}";

        return [
            'provider_meeting_id' => $meetingId,
            'passcode' => null,
            'join_url' => $joinUrl,
            'host_url' => $joinUrl,
        ];
    }
}

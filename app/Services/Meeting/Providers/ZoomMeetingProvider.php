<?php

namespace App\Services\Meeting\Providers;

use App\Models\LiveSession;
use App\Models\User;
use App\Services\Meeting\Contracts\MeetingProviderInterface;

class ZoomMeetingProvider implements MeetingProviderInterface
{
    public function getName(): string
    {
        return 'Zoom Meeting';
    }

    public function getSlug(): string
    {
        return 'zoom';
    }

    public function supportsEmbedding(): bool
    {
        return true;
    }

    public function generateAccessPayload(LiveSession $session, User $user): array
    {
        $sessionMeeting = $session->sessionMeeting;
        $meetingId = $sessionMeeting?->provider_meeting_id ?: (string) $session->id;
        $passcode = $sessionMeeting?->passcode ?: '123456';
        $role = $user->isTeacher() || $user->isAdmin() ? 1 : 0; // 1 = host, 0 = attendee

        // Generate signature or secure access config for Zoom Web SDK
        $sdkKey = config('services.zoom.sdk_key', 'ELITE_ZOOM_SDK_KEY');
        $sdkSecret = config('services.zoom.sdk_secret', 'ELITE_ZOOM_SDK_SECRET');

        $iat = time();
        $exp = $iat + 60 * 60 * 2; // 2 hours

        $payload = [
            'sdkKey' => $sdkKey,
            'mn' => $meetingId,
            'role' => $role,
            'iat' => $iat,
            'exp' => $exp,
            'tokenExp' => $exp,
        ];

        // Create base64 signature token safely
        $signature = base64_encode(hash_hmac('sha256', json_encode($payload), $sdkSecret, true));

        return [
            'provider' => $this->getSlug(),
            'supports_embedding' => true,
            'embed_type' => 'zoom_sdk',
            'meeting_id' => $meetingId,
            'passcode' => $passcode,
            'signature' => $signature,
            'sdk_key' => $sdkKey,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'join_url' => $sessionMeeting?->join_url ?: $session->meeting_link,
        ];
    }

    public function createMeeting(LiveSession $session, array $options = []): array
    {
        $meetingId = 'ZOOM-' . rand(100000000, 999999999);
        $passcode = substr(md5(uniqid()), 0, 6);

        return [
            'provider_meeting_id' => $meetingId,
            'passcode' => $passcode,
            'join_url' => "https://zoom.us/j/{$meetingId}?pwd={$passcode}",
            'host_url' => "https://zoom.us/s/{$meetingId}?pwd={$passcode}",
        ];
    }
}

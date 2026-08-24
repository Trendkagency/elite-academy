<?php

namespace App\Services\Meeting;

use App\Models\LiveSession;
use App\Models\User;
use App\Services\Session\LiveSessionService;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;

class MeetingAccessService
{
    public function __construct(
        protected LiveSessionService $liveSessionService,
        protected MeetingProviderManager $providerManager
    ) {}

    /**
     * Generate secure short-lived embedded meeting access payload.
     */
    public function generateEmbeddedAccessPayload(LiveSession $session, User $user): array
    {
        // 1. Evaluate access eligibility
        $access = $this->liveSessionService->getStreamAccess($session, $user);

        if (! $access['can_access']) {
            return array_merge([
                'success' => false,
                'can_access' => false,
                'message' => $access['message'] ?? 'Access denied',
            ], $access);
        }

        // 2. Determine provider slug
        $providerSlug = $session->sessionMeeting?->provider_slug ?: ($session->meeting_platform ?: 'google_meet');
        $provider = $this->providerManager->resolve($providerSlug);

        // 3. Generate short-lived signed token (valid for 3 minutes)
        $expiresAt = now()->addMinutes(3)->timestamp;
        $signaturePayload = "session_{$session->id}:user_{$user->id}:exp_{$expiresAt}";
        $shortLivedToken = hash_hmac('sha256', $signaturePayload, config('app.key'));

        // 4. Get provider specific payload
        $providerPayload = $provider->generateAccessPayload($session, $user);

        return array_merge([
            'success' => true,
            'can_access' => true,
            'session' => [
                'id' => $session->id,
                'title' => $session->title ?: 'Live Stream Session',
                'scheduled_at' => $session->scheduled_at?->toIso8601String(),
                'start_at' => $session->effective_start_at?->toIso8601String(),
                'end_at' => $session->effective_end_at?->toIso8601String(),
                'duration_minutes' => $session->duration_minutes ?: 60,
                'teacher_name' => $session->teacherProfile?->user?->name ?: 'Dr. Instructor',
                'subject_name' => $session->subject?->name ?: 'Physics',
            ],
            'access_token' => $shortLivedToken,
            'expires_at' => $expiresAt,
            'watermark' => [
                'student_name' => $user->name,
                'student_id' => 'STU-' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                'session_id' => 'SES-' . str_pad((string) $session->id, 5, '0', STR_PAD_LEFT),
            ],
        ], $providerPayload);
    }

    /**
     * Verify short-lived meeting access token.
     */
    public function verifyAccessToken(LiveSession $session, User $user, string $token, int $expiresAt): bool
    {
        if (now()->timestamp > $expiresAt) {
            return false;
        }

        $signaturePayload = "session_{$session->id}:user_{$user->id}:exp_{$expiresAt}";
        $expectedToken = hash_hmac('sha256', $signaturePayload, config('app.key'));

        return hash_equals($expectedToken, $token);
    }
}

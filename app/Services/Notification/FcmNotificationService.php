<?php

namespace App\Services\Notification;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\FcmToken;
use App\Models\LiveSession;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    /**
     * Register or update an FCM token for a user.
     */
    public function registerToken(User $user, string $token, string $deviceType = 'web'): FcmToken
    {
        Log::info("[FCM TOKEN REGISTERED] User #{$user->id} ({$user->name}) Token: {$token}");

        return FcmToken::updateOrCreate(
            ['token' => $token],
            [
                'user_id'     => $user->id,
                'device_type' => $deviceType,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Store a UserNotification record and dispatch FCM push to all user devices.
     */
    public function sendNotification(User $user, string $type, string $title, string $body, ?string $actionUrl = null): UserNotification
    {
        $notification = UserNotification::create([
            'user_id'    => $user->id,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'action_url' => $actionUrl ?: route('student-portal'),
            'is_read'    => false,
        ]);

        $tokens = FcmToken::where('user_id', $user->id)->pluck('token')->toArray();

        Log::info("=================================================================");
        Log::info("[FCM NOTIFICATION SENDING] User #{$user->id} ({$user->email})", [
            'user_id'        => $user->id,
            'user_name'      => $user->name,
            'title'          => $title,
            'body'           => $body,
            'fcm_token_count'=> count($tokens),
            'fcm_tokens'     => $tokens,
        ]);
        Log::info("=================================================================");

        if (! empty($tokens)) {
            $this->dispatchFcmPayload($tokens, $title, $body, $actionUrl);
        }

        return $notification;
    }

    /**
     * Broadcast FCM push notification to a target audience.
     * Target: 'all' | 'students' | 'teachers' | 'parents'
     */
    public function broadcastNotification(string $targetAudience, string $title, string $body, ?string $actionUrl = null): int
    {
        $query = User::query();

        match ($targetAudience) {
            'students' => $query->whereHas('studentProfile'),
            'teachers' => $query->whereHas('teacherProfile'),
            'parents'  => $query->whereHas('parentProfile'),
            default    => null,
        };

        $dispatchedCount = 0;

        $query->each(function (User $user) use ($title, $body, $actionUrl, &$dispatchedCount) {
            $this->sendNotification($user, 'BROADCAST_ALERT', $title, $body, $actionUrl);
            $dispatchedCount++;
        });

        return $dispatchedCount;
    }

    /**
     * Scan sessions starting in ~24h and remind students with unsubmitted assignments.
     */
    public function sendAssignmentDeadlineReminders(): int
    {
        $windowStart = now()->addHours(23);
        $windowEnd   = now()->addHours(25);

        $sessions = LiveSession::where('status', 'scheduled')
            ->where(function ($query) use ($windowStart, $windowEnd) {
                $query->whereBetween('start_at', [$windowStart, $windowEnd])
                      ->orWhereBetween('scheduled_at', [$windowStart, $windowEnd]);
            })
            ->with(['assignments', 'student'])
            ->get();

        $reminderCount = 0;

        foreach ($sessions as $session) {
            $student = $session->student;
            if (! $student) continue;

            foreach ($session->assignments as $assignment) {
                $hasSubmitted = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_user_id', $student->id)
                    ->exists();

                if (! $hasSubmitted) {
                    $sessionTitle = $session->title ?: 'Interactive Live Lesson';
                    $deadlineStr  = $assignment->effective_due_at
                        ? $assignment->effective_due_at->format('Y-m-d H:i')
                        : '24h Pre-Lesson';

                    $title = app()->getLocale() === 'ar'
                        ? '⏰ موعد تسليم الواجب (قبل الحصة بـ 24 ساعة)'
                        : '⏰ Homework Deadline Alert (24h Before Lesson)';

                    $body = app()->getLocale() === 'ar'
                        ? "تذكرة: يرجى تسليم واجب ({$assignment->title}) قبل موعد الحصة التفاعلية المباشرة ({$sessionTitle}). الموعد النهائي: {$deadlineStr}."
                        : "Reminder: Please complete your assignment ({$assignment->title}) for live session ({$sessionTitle}). Deadline: {$deadlineStr}.";

                    $this->sendNotification(
                        $student,
                        'ASSIGNMENT_DEADLINE_REMINDER',
                        $title,
                        $body,
                        route('student.assignment.take', ['id' => $assignment->id])
                    );

                    $reminderCount++;
                }
            }
        }

        return $reminderCount;
    }

    /**
     * Dispatch notification when admin approves a student request.
     */
    public function notifyAdminApproval(User $user, string $requestType, string $details = ''): UserNotification
    {
        $title = app()->getLocale() === 'ar'
            ? '✅ تم اعتماد طلبك من قبل الإدارة'
            : '✅ Request Approved by Administration';

        $body = app()->getLocale() === 'ar'
            ? "تمت الموافقة على طلب ({$requestType}) الخاص بك بنجاح. يمكنك الآن المتابعة في Portal."
            : "Your ({$requestType}) request has been approved by the admin team. Details: {$details}";

        return $this->sendNotification($user, 'ADMIN_APPROVAL_ALERT', $title, $body, route('student-portal'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal Dispatch Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Route to the correct FCM API based on FCM_MODE config.
     */
    protected function dispatchFcmPayload(array $tokens, string $title, string $body, ?string $actionUrl = null): void
    {
        $mode = config('fcm.mode', 'legacy');

        if ($mode === 'v1') {
            $this->dispatchV1($tokens, $title, $body, $actionUrl);
        } else {
            $this->dispatchLegacy($tokens, $title, $body, $actionUrl);
        }
    }

    /**
     * FCM Legacy HTTP API (v0) — Server Key based.
     * Batches tokens in chunks of config('fcm.batch_size').
     */
    protected function dispatchLegacy(array $tokens, string $title, string $body, ?string $actionUrl = null): void
    {
        $serverKey = config('fcm.legacy.key');

        if (! $serverKey) {
            $this->logFallback('legacy', $title, $body, count($tokens));
            return;
        }

        $defaults   = config('fcm.defaults');
        $android    = config('fcm.android');
        $batchSize  = config('fcm.batch_size', 500);
        $clickUrl   = $actionUrl ?: $defaults['click_action'];
        $endpoint   = config('fcm.legacy.endpoint', 'https://fcm.googleapis.com/fcm/send');

        foreach (array_chunk($tokens, $batchSize) as $chunk) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $serverKey,
                    'Content-Type'  => 'application/json',
                ])->post($endpoint, [
                    'registration_ids' => $chunk,
                    'notification' => [
                        'title'        => $title,
                        'body'         => $body,
                        'icon'         => $defaults['icon'],
                        'color'        => $defaults['color'],
                        'sound'        => $defaults['sound'],
                        'click_action' => $clickUrl,
                        'badge'        => $defaults['badge'],
                    ],
                    'data' => [
                        'title' => $title,
                        'body'  => $body,
                        'url'   => $clickUrl,
                    ],
                    'android' => [
                        'priority' => $android['priority'],
                        'ttl'      => $android['ttl'] . 's',
                        'notification' => [
                            'channel_id' => $android['channel_id'],
                            'sound'      => $defaults['sound'],
                            'color'      => $defaults['color'],
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => config('fcm.apns.sound', 'default'),
                                'badge' => config('fcm.apns.badge', 1),
                            ],
                        ],
                    ],
                ]);

                if (config('fcm.logging.enabled') && $response->failed()) {
                    Log::channel(config('fcm.logging.channel'))->warning('FCM Legacy: Push batch failed', [
                        'status'  => $response->status(),
                        'body'    => $response->body(),
                        'tokens'  => count($chunk),
                    ]);
                }

            } catch (\Throwable $e) {
                if (config('fcm.logging.on_error')) {
                    Log::channel(config('fcm.logging.channel'))->error('FCM Legacy dispatch error: ' . $e->getMessage(), [
                        'title'  => $title,
                        'tokens' => count($chunk),
                    ]);
                }
            }
        }
    }

    /**
     * FCM HTTP v1 API — OAuth2 Service Account based (recommended).
     * Sends one message at a time (v1 does not support multi-cast natively).
     */
    protected function dispatchV1(array $tokens, string $title, string $body, ?string $actionUrl = null): void
    {
        $projectId          = config('fcm.v1.project_id');
        $serviceAccountPath = config('fcm.v1.service_account_path');

        if ($serviceAccountPath && ! file_exists($serviceAccountPath) && file_exists(base_path($serviceAccountPath))) {
            $serviceAccountPath = base_path($serviceAccountPath);
        }

        if (! $projectId || ! $serviceAccountPath || ! file_exists($serviceAccountPath)) {
            $this->logFallback('v1', $title, $body, count($tokens));
            return;
        }

        $accessToken = $this->getV1AccessToken($serviceAccountPath);
        if (! $accessToken) {
            Log::channel(config('fcm.logging.channel'))->error('FCM v1: Failed to obtain OAuth2 access token.');
            return;
        }

        $defaults  = config('fcm.defaults');
        $android   = config('fcm.android');
        $endpoint  = sprintf(config('fcm.v1.endpoint'), $projectId);
        $clickUrl  = $actionUrl ?: $defaults['click_action'];

        foreach ($tokens as $token) {
            try {
                $response = Http::withToken($accessToken)
                    ->post($endpoint, [
                        'message' => [
                            'token'        => $token,
                            'notification' => [
                                'title' => $title,
                                'body'  => $body,
                                'image' => $defaults['icon'],
                            ],
                            'android' => [
                                'priority' => strtoupper($android['priority']),
                                'ttl'      => $android['ttl'] . 's',
                                'notification' => [
                                    'channel_id' => $android['channel_id'],
                                    'sound'      => $defaults['sound'],
                                    'color'      => $defaults['color'],
                                    'click_action' => $clickUrl,
                                ],
                            ],
                            'apns' => [
                                'payload' => [
                                    'aps' => [
                                        'sound' => config('fcm.apns.sound', 'default'),
                                        'badge' => config('fcm.apns.badge', 1),
                                    ],
                                ],
                            ],
                            'webpush' => [
                                'notification' => [
                                    'title' => $title,
                                    'body'  => $body,
                                    'icon'  => $defaults['icon'],
                                ],
                                'fcm_options' => [
                                    'link' => $clickUrl,
                                ],
                            ],
                            'data' => [
                                'url'  => $clickUrl,
                                'type' => 'push',
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    Log::channel(config('fcm.logging.channel'))->info("✅ [FCM v1 SUCCESS] Target Token: {$token}", [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);
                } else {
                    Log::channel(config('fcm.logging.channel'))->error("❌ [FCM v1 RESPONSE ERROR] Target Token: {$token}", [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }

            } catch (\Throwable $e) {
                if (config('fcm.logging.on_error')) {
                    Log::channel(config('fcm.logging.channel'))->error('FCM v1 dispatch error: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Obtain a short-lived OAuth2 access token from a Firebase service account JSON file.
     */
    protected function getV1AccessToken(string $serviceAccountPath): ?string
    {
        try {
            $credentials = json_decode(file_get_contents($serviceAccountPath), true);

            $now = time();
            $header  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            $signature = '';
            openssl_sign("{$header}.{$payload}", $signature, $credentials['private_key'], 'SHA256');
            $jwt = "{$header}.{$payload}." . base64_encode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            return $response->json('access_token');

        } catch (\Throwable $e) {
            Log::error('FCM v1 OAuth token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log a fallback message when FCM credentials are not configured.
     */
    protected function logFallback(string $mode, string $title, string $body, int $tokenCount): void
    {
        if (config('fcm.logging.enabled')) {
            Log::channel(config('fcm.logging.channel'))->info("FCM [{$mode}] not configured — notification logged only", [
                'title'  => $title,
                'body'   => $body,
                'tokens' => $tokenCount,
            ]);
        }
    }
}

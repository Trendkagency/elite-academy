<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Services\Notification\FcmNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected FcmNotificationService $notificationService;

    public function __construct(FcmNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get active notifications feed for authenticated user.
     */
    public function feed(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $perPage = (int) $request->input('per_page', 5);

        $paginated = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $unreadCount = UserNotification::where('user_id', $user->id)->where('is_read', false)->count();

        $latestFcmToken = \App\Models\FcmToken::where('user_id', $user->id)
            ->latest('updated_at')
            ->first();

        return response()->json([
            'success' => true,
            'notifications' => $paginated->items(),
            'unread_count' => $unreadCount,
            'fcm_token' => $latestFcmToken ? $latestFcmToken->token : null,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'has_more' => $paginated->hasMorePages(),
            ],
        ]);
    }

    /**
     * Register FCM Web Push device token.
     */
    public function registerToken(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        $fcmTokenRecord = $this->notificationService->registerToken(
            $user,
            $request->input('token'),
            $request->input('device_type', 'web')
        );

        return response()->json([
            'success' => true,
            'message' => 'FCM Device token registered successfully',
            'token' => $fcmTokenRecord->token,
            'fcm_token' => $fcmTokenRecord,
        ]);
    }

    /**
     * Trigger real-time 30-second FCM test notification.
     */
    public function triggerTestPush(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $title = app()->getLocale() === 'ar'
            ? "🔔 إشعار تجريبي لاختبار FCM والبث المباشر"
            : "🔔 FCM Real-Time Test Push Alert";

        $body = app()->getLocale() === 'ar'
            ? "تم تفعيل الإشعارات بنجاح! الإشعارات المرتبطة بالواجبات واعتمادات الأدمن تعمل الآن."
            : "FCM Push active! Automated 24h pre-session homework alerts & admin approvals are online.";

        $notification = $this->notificationService->sendNotification(
            $user,
            'TEST_NOTIFICATION',
            $title,
            $body,
            route('student-portal')
        );

        $fcmTokens = \App\Models\FcmToken::where('user_id', $user->id)->pluck('token')->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Test notification dispatched successfully! Displays in 30s timer banner.',
            'notification' => $notification,
            'fcm_tokens' => $fcmTokens,
            'delay_seconds' => 30,
        ]);
    }
}

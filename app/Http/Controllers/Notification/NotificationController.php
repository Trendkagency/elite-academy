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
     * Check for new real-time notifications since a given ID or timestamp.
     */
    public function checkRealtime(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $sinceId = (int) $request->input('since_id', 0);

        $query = UserNotification::where('user_id', $user->id)
            ->orderBy('id', 'desc');

        if ($sinceId > 0) {
            $newNotifications = (clone $query)->where('id', '>', $sinceId)->take(10)->get();
        } else {
            $newNotifications = collect([]);
        }

        $unreadCount = UserNotification::where('user_id', $user->id)->where('is_read', false)->count();
        $latest = UserNotification::where('user_id', $user->id)->orderBy('id', 'desc')->first();

        // Also fetch up to 5 recent notifications for dropdown
        $recent = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'has_new' => $newNotifications->isNotEmpty(),
            'new_notifications' => $newNotifications,
            'recent_notifications' => $recent,
            'unread_count' => $unreadCount,
            'latest_id' => $latest ? $latest->id : 0,
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

    /**
     * Send a custom in-app notification to a specific user (admin-only).
     * Route: POST /ajax/notifications/send-custom
     */
    public function sendCustomNotification(Request $request): JsonResponse
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id'    => 'required|integer|exists:users,id',
            'type'       => 'required|string|max:100',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string|max:2000',
            'action_url' => 'nullable|url|max:500',
        ]);

        $targetUser = \App\Models\User::findOrFail($request->input('user_id'));

        $notification = $this->notificationService->sendNotification(
            $targetUser,
            $request->input('type'),
            $request->input('title'),
            $request->input('body'),
            $request->input('action_url')
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Custom notification sent successfully.',
            'notification' => $notification,
        ]);
    }

    /**
     * Mark a single notification as read.
     * Route: POST /ajax/notifications/{id}/read
     */
    public function markAsRead(int $id): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $notification = UserNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Marked as read.']);
    }

    /**
     * Mark all notifications for the authenticated user as read.
     * Route: POST /ajax/notifications/read-all
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
    }
}

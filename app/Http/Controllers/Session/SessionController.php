<?php

namespace App\Http\Controllers\Session;

use App\Actions\Session\SubmitExceptionRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Session\SubmitExceptionFormRequest;
use App\Models\CourseSession;
use App\Models\LiveSession;
use App\Services\Session\LiveSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SessionController extends Controller
{
    /**
     * Show CourseSession details.
     */
    public function show(int $id): JsonResponse
    {
        $session = CourseSession::find($id);
        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }

        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if (Gate::denies('view', $session)) {
            return response()->json([
                'success' => false,
                'can_access' => false,
                'message' => 'Access denied: You cannot start this session until you complete and pass the assignment for the previous session.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'can_access' => true,
            'session' => [
                'id' => $session->id,
                'title' => $session->title,
                'description' => $session->description,
                'video_url' => $session->video_url,
                'duration_minutes' => $session->duration_minutes,
            ],
        ]);
    }

    /**
     * Join/access LiveSession stream endpoint with strict server-side start_at, end_at, and prerequisite validation.
     */
    public function liveSessionAccess(int $id, LiveSessionService $liveSessionService): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'can_access' => false, 'message' => 'Unauthenticated'], 401);
        }

        $liveSession = LiveSession::with(['teacherProfile.user', 'course', 'subject'])->find($id);

        if (! $liveSession) {
            return response()->json(['success' => false, 'can_access' => false, 'message' => 'Live Session not found'], 404);
        }

        // Authorization check: User must own this session assignment or be admin
        if (Gate::denies('join', $liveSession)) {
            return response()->json([
                'success' => false,
                'can_access' => false,
                'message' => 'Unauthorized: You are not authorized to join this live session stream.',
            ], 403);
        }

        // Evaluate server-side stream access state (time window: now >= start_at && now < end_at)
        $accessResult = $liveSessionService->getStreamAccess($liveSession, $user);

        $statusCode = $accessResult['status_code'] ?? ($accessResult['can_access'] ? 200 : 422);

        return response()->json(array_merge([
            'success' => $accessResult['can_access'],
            'meeting_link' => $liveSession->meeting_link ?: ($liveSession->stream_url ?: 'https://meet.google.com/test-live-stream'),
            'session' => [
                'id' => $liveSession->id,
                'title' => $liveSession->title ?: 'Live Stream Session',
                'scheduled_at' => $liveSession->scheduled_at?->toIso8601String(),
                'start_at' => $liveSession->effective_start_at?->toIso8601String(),
                'end_at' => $liveSession->effective_end_at?->toIso8601String(),
                'duration_minutes' => $liveSession->duration_minutes,
            ],
        ], $accessResult), $statusCode);
    }

    /**
     * Submit an exception request or absence excuse.
     */
    public function submitException(SubmitExceptionFormRequest $request, SubmitExceptionRequestAction $action): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validated();
        $liveSession = ! empty($validated['live_session_id']) ? LiveSession::find($validated['live_session_id']) : null;

        try {
            $exceptionRequest = $action->execute(
                $user,
                $liveSession,
                $validated['reason'],
                $validated['attachment_path'] ?? null,
                $validated['course_id'] ?? null,
                $validated['is_global'] ?? false,
                $validated['scope'] ?? 'course'
            );

            return response()->json([
                'success' => true,
                'message' => 'Absence excuse / exception request submitted successfully!',
                'request_id' => $exceptionRequest->id,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }
}

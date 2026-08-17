<?php

namespace App\Http\Controllers\Session;

use App\Actions\Session\SubmitExceptionRequestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Session\SubmitExceptionFormRequest;
use App\Models\CourseSession;
use App\Models\LiveSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SessionController extends Controller
{
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

    public function submitException(SubmitExceptionFormRequest $request, SubmitExceptionRequestAction $action): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validated();
        $liveSession = LiveSession::find($validated['live_session_id']);

        if (! $liveSession) {
            return response()->json(['success' => false, 'message' => 'Live session not found'], 404);
        }

        try {
            $exceptionRequest = $action->execute(
                $user,
                $liveSession,
                $validated['reason'],
                $validated['attachment_path'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Absence excuse request submitted successfully!',
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

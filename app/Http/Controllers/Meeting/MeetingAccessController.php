<?php

namespace App\Http\Controllers\Meeting;

use App\Enums\MeetingSecurityEventType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Meeting\JoinMeetingRequest;
use App\Http\Requests\Meeting\MeetingHeartbeatRequest;
use App\Http\Requests\Meeting\SecurityEventRequest;
use App\Models\LiveSession;
use App\Services\Meeting\MeetingAccessService;
use App\Services\Meeting\MeetingAttendanceService;
use App\Services\Meeting\MeetingSecurityService;
use App\Services\Session\LiveSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MeetingAccessController extends Controller
{
    public function __construct(
        protected MeetingAccessService $accessService,
        protected MeetingAttendanceService $attendanceService,
        protected MeetingSecurityService $securityService,
        protected LiveSessionService $liveSessionService
    ) {}

    /**
     * Display the in-system live meeting interface page.
     */
    public function show(int $id): View|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login')->with('error', __('app.auth.unauthenticated'));
        }

        $session = LiveSession::with(['teacherProfile.user', 'course', 'subject', 'sessionMeeting'])->find($id);
        if (! $session) {
            abort(404, __('Session not found'));
        }

        // Authorization check: Policy Gate::join
        if (Gate::denies('join', $session)) {
            abort(403, __('Unauthorized: You are not authorized to join this live session stream.'));
        }

        return view('pages.student-meeting', [
            'session' => $session,
            'user' => $user,
            'pageTitle' => ($session->title ?: 'Live Stream Session') . ' — Elite Academy',
        ]);
    }

    /**
     * Join/Initialize in-system meeting access.
     */
    public function join(int $id, JoinMeetingRequest $request): JsonResponse
    {
        $user = auth()->user();
        $session = LiveSession::with(['teacherProfile.user', 'course', 'subject', 'sessionMeeting'])->find($id);

        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Live Session not found'], 404);
        }

        // Check authorization gate
        if (Gate::denies('join', $session)) {
            $this->securityService->logEvent(
                $session,
                $user,
                MeetingSecurityEventType::MEETING_ACCESS_DENIED->value,
                ['reason' => 'Gate unauthorized'],
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'success' => false,
                'can_access' => false,
                'message' => 'Unauthorized: You are not authorized to join this live session stream.',
            ], 403);
        }

        // Deduct session package credit if applicable and not free demo
        if (! $this->liveSessionService->isSessionFreeDemo($session, $user)) {
            $package = \App\Models\StudentPackage::where('student_user_id', $user->id)
                ->where('status', 'active')
                ->where('remaining_sessions', '>', 0)
                ->first();

            if ($package) {
                $alreadyDeducted = \App\Models\PackageTransaction::where('student_package_id', $package->id)
                    ->where('live_session_id', $session->id)
                    ->where('type', 'session_deduct')
                    ->exists();

                if (! $alreadyDeducted) {
                    $package->deductSession($session->id, "Attendance for Live Session #{$session->id}");
                }
            }
        }

        // Generate embedded meeting access payload
        $accessPayload = $this->accessService->generateEmbeddedAccessPayload($session, $user);

        if (! $accessPayload['can_access']) {
            $statusCode = $accessPayload['status_code'] ?? 422;
            return response()->json($accessPayload, $statusCode);
        }

        // Record attendance join & security join event
        $this->attendanceService->recordJoin($session, $user, $request->ip(), $request->userAgent());
        $this->securityService->logEvent(
            $session,
            $user,
            MeetingSecurityEventType::MEETING_JOIN->value,
            ['provider' => $accessPayload['provider'] ?? 'google_meet'],
            $request->ip(),
            $request->userAgent()
        );

        return response()->json($accessPayload, 200);
    }

    /**
     * Process presence heartbeat ping.
     */
    public function heartbeat(int $id, MeetingHeartbeatRequest $request): JsonResponse
    {
        $user = auth()->user();
        $session = LiveSession::find($id);

        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }

        $validated = $request->validated();
        $isValidToken = $this->accessService->verifyAccessToken(
            $session,
            $user,
            $validated['access_token'],
            $validated['expires_at']
        );

        if (! $isValidToken) {
            $this->securityService->logEvent(
                $session,
                $user,
                MeetingSecurityEventType::TOKEN_REJECTED->value,
                ['reason' => 'Invalid or expired access token'],
                $request->ip(),
                $request->userAgent()
            );

            return response()->json(['success' => false, 'message' => 'Invalid or expired session access token'], 401);
        }

        // Verify session time has not ended
        $now = now();
        $endTime = $session->effective_end_at;
        if ($endTime && $now->gt($endTime)) {
            $this->attendanceService->recordLeave($session, $user);

            return response()->json([
                'success' => false,
                'session_ended' => true,
                'message' => 'Live session has ended.',
            ], 422);
        }

        $heartbeatResult = $this->attendanceService->processHeartbeat($session, $user, $request->ip(), $request->userAgent());

        return response()->json(array_merge([
            'success' => true,
        ], $heartbeatResult));
    }

    /**
     * Record student leaving meeting.
     */
    public function leave(int $id, Request $request): JsonResponse
    {
        $user = auth()->user();
        $session = LiveSession::find($id);

        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }

        $attendance = $this->attendanceService->recordLeave($session, $user);
        $this->securityService->logEvent(
            $session,
            $user,
            MeetingSecurityEventType::MEETING_LEAVE->value,
            ['duration_seconds' => $attendance?->duration_seconds ?? 0],
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'message' => 'Left meeting successfully',
        ]);
    }

    /**
     * Log client security audit event (tab hidden, window blur, fullscreen exit).
     */
    public function logSecurityEvent(int $id, SecurityEventRequest $request): JsonResponse
    {
        $user = auth()->user();
        $session = LiveSession::find($id);

        if (! $session) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }

        $validated = $request->validated();
        $this->securityService->logEvent(
            $session,
            $user,
            $validated['event_type'],
            $validated['metadata'] ?? [],
            $request->ip(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'message' => 'Security event logged',
        ]);
    }
}

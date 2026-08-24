<?php

namespace App\Services\Meeting;

use App\Models\LiveSession;
use App\Models\MeetingAttendance;
use App\Models\User;
use Carbon\Carbon;

class MeetingAttendanceService
{
    /**
     * Record student joining live meeting.
     */
    public function recordJoin(LiveSession $session, User $student, ?string $ipAddress = null, ?string $userAgent = null): MeetingAttendance
    {
        $providerSlug = $session->sessionMeeting?->provider_slug ?: ($session->meeting_platform ?: 'google_meet');
        $providerMeetingId = $session->sessionMeeting?->provider_meeting_id ?: (string) $session->id;

        // Check if active attendance record already exists for this student and session
        $attendance = MeetingAttendance::where('live_session_id', $session->id)
            ->where('student_user_id', $student->id)
            ->whereIn('status', ['joined', 'active'])
            ->latest('id')
            ->first();

        if ($attendance) {
            $attendance->update([
                'last_seen_at' => now(),
                'status' => 'active',
                'ip_address' => $ipAddress ?: $attendance->ip_address,
                'user_agent' => $userAgent ?: $attendance->user_agent,
            ]);

            return $attendance;
        }

        return MeetingAttendance::create([
            'live_session_id' => $session->id,
            'student_user_id' => $student->id,
            'joined_at' => now(),
            'last_seen_at' => now(),
            'duration_seconds' => 0,
            'status' => 'joined',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'provider_slug' => $providerSlug,
            'provider_meeting_id' => $providerMeetingId,
        ]);
    }

    /**
     * Process heartbeat ping from client (sent every 30-60s).
     */
    public function processHeartbeat(LiveSession $session, User $student, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        $attendance = MeetingAttendance::where('live_session_id', $session->id)
            ->where('student_user_id', $student->id)
            ->latest('id')
            ->first();

        if (! $attendance) {
            $attendance = $this->recordJoin($session, $student, $ipAddress, $userAgent);
        }

        $now = now();
        $lastSeen = $attendance->last_seen_at ?: $attendance->joined_at;
        $elapsed = max(0, $now->diffInSeconds($lastSeen));

        // Limit elapsed increment to maximum 90s per heartbeat to prevent client spoofing
        $increment = min($elapsed, 90);
        $newDuration = $attendance->duration_seconds + $increment;

        $attendance->update([
            'last_seen_at' => $now,
            'duration_seconds' => $newDuration,
            'status' => 'active',
        ]);

        return [
            'status' => 'active',
            'total_duration_seconds' => $newDuration,
            'formatted_duration' => gmdate('H:i:s', $newDuration),
        ];
    }

    /**
     * Record student leaving live meeting.
     */
    public function recordLeave(LiveSession $session, User $student): ?MeetingAttendance
    {
        $attendance = MeetingAttendance::where('live_session_id', $session->id)
            ->where('student_user_id', $student->id)
            ->latest('id')
            ->first();

        if (! $attendance) {
            return null;
        }

        $now = now();
        $joinedAt = $attendance->joined_at ?: $now;
        $calculatedDuration = max($attendance->duration_seconds, $now->diffInSeconds($joinedAt));

        $attendance->update([
            'left_at' => $now,
            'last_seen_at' => $now,
            'duration_seconds' => $calculatedDuration,
            'status' => 'left',
        ]);

        return $attendance;
    }
}

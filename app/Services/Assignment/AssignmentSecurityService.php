<?php

namespace App\Services\Assignment;

use App\Models\Assignment;
use App\Models\AssignmentSecurityAudit;
use App\Models\User;
use Illuminate\Http\Request;

class AssignmentSecurityService
{
    /**
     * Log security audit event for assessment anti-cheating tracking.
     */
    public function logEvent(User $student, Assignment $assignment, string $eventType, ?array $metadata = [], int $riskScore = 1): AssignmentSecurityAudit
    {
        $request = request();

        return AssignmentSecurityAudit::create([
            'student_user_id' => $student->id,
            'assignment_id' => $assignment->id,
            'event_type' => $eventType,
            'risk_score' => $riskScore,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    /**
     * Compute cumulative risk score for a student's assessment attempt.
     */
    public function getCumulativeRiskScore(User $student, Assignment $assignment): array
    {
        $audits = AssignmentSecurityAudit::where('student_user_id', $student->id)
            ->where('assignment_id', $assignment->id)
            ->get();

        $totalScore = $audits->sum('risk_score');

        $level = 'NORMAL';
        if ($totalScore >= 15) {
            $level = 'HIGH_RISK';
        } elseif ($totalScore >= 8) {
            $level = 'MEDIUM_RISK';
        } elseif ($totalScore >= 3) {
            $level = 'LOW_RISK';
        }

        return [
            'total_risk_score' => $totalScore,
            'risk_level' => $level,
            'events_count' => $audits->count(),
            'events' => $audits->map(fn ($a) => [
                'event_type' => $a->event_type,
                'risk_score' => $a->risk_score,
                'timestamp' => $a->created_at?->toIso8601String(),
            ]),
        ];
    }
}

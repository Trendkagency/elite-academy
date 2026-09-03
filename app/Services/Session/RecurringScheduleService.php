<?php

namespace App\Services\Session;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\LiveSession;
use App\Models\RecurringSchedule;
use App\Models\SessionAuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecurringScheduleService
{
    /**
     * Map day names to ISO day-of-week (1 = Monday, 7 = Sunday) or Carbon index (0 = Sunday, 6 = Saturday)
     */
    protected const DAY_MAP = [
        'sunday' => 0,
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
    ];

    /**
     * Preview recurring dates and validate conflicts without saving to the database.
     */
    public function previewDates(array $params): array
    {
        $startDate = Carbon::parse($params['start_date'])->startOfDay();
        $endDate = Carbon::parse($params['end_date'])->endOfDay();
        $startTime = $params['start_time']; // e.g. "10:00"
        $duration = (int) ($params['duration_minutes'] ?? 60);
        $recurrenceType = $params['recurrence_type'] ?? 'weekly';
        $daysOfWeek = $params['days_of_week'] ?? []; // e.g. [0, 6] or ['saturday', 'sunday']
        $teacherProfileId = (int) $params['teacher_profile_id'];
        $studentUserId = !empty($params['student_user_id']) ? (int) $params['student_user_id'] : null;

        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => __('The end date cannot be earlier than the start date.'),
            ]);
        }

        // Normalize days of week to numeric (0 = Sunday, 6 = Saturday)
        $normalizedDays = array_map(function ($d) {
            if (is_numeric($d)) return (int) $d;
            $lower = strtolower(trim($d));
            return self::DAY_MAP[$lower] ?? 0;
        }, $daysOfWeek);

        $dates = [];
        $current = $startDate->copy();
        $maxLimit = 366; // Prevent runaway loops
        $count = 0;

        if ($recurrenceType === 'single') {
            $sessionStart = Carbon::parse($startDate->format('Y-m-d') . ' ' . $startTime);
            $sessionEnd = $sessionStart->copy()->addMinutes($duration);
            $conflicts = $this->detectConflicts($teacherProfileId, $studentUserId, $sessionStart, $sessionEnd);

            $dates[] = [
                'date' => $sessionStart->format('Y-m-d'),
                'day_name' => $sessionStart->locale(app()->getLocale())->translatedFormat('l'),
                'start_time' => $sessionStart->format('H:i'),
                'end_time' => $sessionEnd->format('H:i'),
                'has_conflict' => !empty($conflicts),
                'conflict_details' => $conflicts,
            ];
            return $dates;
        }

        if ($recurrenceType === 'weekly' || $recurrenceType === 'multi_month' || $recurrenceType === 'yearly') {
            if (empty($normalizedDays)) {
                $normalizedDays = [$startDate->dayOfWeek];
            }

            while ($current->lte($endDate) && $count < $maxLimit) {
                if (in_array($current->dayOfWeek, $normalizedDays, true)) {
                    $sessionStart = Carbon::parse($current->format('Y-m-d') . ' ' . $startTime);
                    $sessionEnd = $sessionStart->copy()->addMinutes($duration);
                    $conflicts = $this->detectConflicts($teacherProfileId, $studentUserId, $sessionStart, $sessionEnd);

                    $dates[] = [
                        'date' => $sessionStart->format('Y-m-d'),
                        'day_name' => $sessionStart->locale(app()->getLocale())->translatedFormat('l'),
                        'start_time' => $sessionStart->format('H:i'),
                        'end_time' => $sessionEnd->format('H:i'),
                        'has_conflict' => !empty($conflicts),
                        'conflict_details' => $conflicts,
                    ];
                    $count++;
                }
                $current->addDay();
            }
        } elseif ($recurrenceType === 'monthly') {
            $monthlyPattern = $params['monthly_pattern'] ?? ['type' => 'day_of_month', 'day' => $startDate->day];
            $monthCursor = $startDate->copy()->startOfMonth();

            while ($monthCursor->lte($endDate) && $count < $maxLimit) {
                $targetDate = null;

                if (($monthlyPattern['type'] ?? 'day_of_month') === 'day_of_month') {
                    $dayNum = min((int) ($monthlyPattern['day'] ?? $startDate->day), $monthCursor->daysInMonth);
                    $targetDate = $monthCursor->copy()->day($dayNum);
                } elseif (($monthlyPattern['type'] ?? '') === 'nth_weekday') {
                    $nth = $monthlyPattern['nth'] ?? 'first'; // first, second, third, fourth, last
                    $weekday = (int) ($monthlyPattern['weekday'] ?? 6); // 0 = Sun, 6 = Sat
                    $targetDate = $this->getNthWeekdayOfMonth($monthCursor->year, $monthCursor->month, $nth, $weekday);
                }

                if ($targetDate && $targetDate->gte($startDate) && $targetDate->lte($endDate)) {
                    $sessionStart = Carbon::parse($targetDate->format('Y-m-d') . ' ' . $startTime);
                    $sessionEnd = $sessionStart->copy()->addMinutes($duration);
                    $conflicts = $this->detectConflicts($teacherProfileId, $studentUserId, $sessionStart, $sessionEnd);

                    $dates[] = [
                        'date' => $sessionStart->format('Y-m-d'),
                        'day_name' => $sessionStart->locale(app()->getLocale())->translatedFormat('l'),
                        'start_time' => $sessionStart->format('H:i'),
                        'end_time' => $sessionEnd->format('H:i'),
                        'has_conflict' => !empty($conflicts),
                        'conflict_details' => $conflicts,
                    ];
                    $count++;
                }

                $monthCursor->addMonthNoOverflow()->startOfMonth();
            }
        }

        return $dates;
    }

    /**
     * Calculate Nth weekday of a month (e.g. 1st Saturday, last Sunday)
     */
    protected function getNthWeekdayOfMonth(int $year, int $month, string $nth, int $weekday): ?Carbon
    {
        $date = Carbon::createFromDate($year, $month, 1);
        if ($nth === 'last') {
            $date = $date->endOfMonth();
            while ($date->dayOfWeek !== $weekday) {
                $date->subDay();
            }
            return $date;
        }

        $nthMap = ['first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4];
        $targetOccurrence = $nthMap[$nth] ?? 1;
        $occurrence = 0;

        while ($date->month === $month) {
            if ($date->dayOfWeek === $weekday) {
                $occurrence++;
                if ($occurrence === $targetOccurrence) {
                    return $date;
                }
            }
            $date->addDay();
        }

        return null;
    }

    /**
     * Detect schedule conflicts for teacher and student.
     */
    public function detectConflicts(int $teacherProfileId, ?int $studentUserId, Carbon $startAt, Carbon $endAt, ?int $ignoreSessionId = null): array
    {
        $conflicts = [];

        // 1. Teacher overlapping sessions
        $teacherQuery = LiveSession::where('teacher_profile_id', $teacherProfileId)
            ->whereNotIn('status', ['cancelled', 'cancelled_by_teacher'])
            ->where(function ($q) use ($startAt, $endAt) {
                $q->where(function ($sub) use ($startAt, $endAt) {
                    $sub->where('scheduled_at', '<', $endAt)
                        ->where('end_at', '>', $startAt);
                })->orWhere(function ($sub) use ($startAt, $endAt) {
                    $sub->where('start_at', '<', $endAt)
                        ->where('end_at', '>', $startAt);
                });
            });

        if ($ignoreSessionId) {
            $teacherQuery->where('id', '!=', $ignoreSessionId);
        }

        $teacherOverlap = $teacherQuery->first();
        if ($teacherOverlap) {
            $conflicts[] = [
                'type' => 'teacher_overlap',
                'message' => __('Teacher already has a scheduled session at this time: :title (:start - :end)', [
                    'title' => $teacherOverlap->title,
                    'start' => $teacherOverlap->scheduled_at?->format('H:i') ?? '',
                    'end' => $teacherOverlap->end_at?->format('H:i') ?? '',
                ]),
                'session_id' => $teacherOverlap->id,
            ];
        }

        // 2. Student overlapping sessions
        if ($studentUserId) {
            $studentQuery = LiveSession::where('student_user_id', $studentUserId)
                ->whereNotIn('status', ['cancelled', 'cancelled_by_teacher'])
                ->where(function ($q) use ($startAt, $endAt) {
                    $q->where(function ($sub) use ($startAt, $endAt) {
                        $sub->where('scheduled_at', '<', $endAt)
                            ->where('end_at', '>', $startAt);
                    })->orWhere(function ($sub) use ($startAt, $endAt) {
                        $sub->where('start_at', '<', $endAt)
                            ->where('end_at', '>', $startAt);
                    });
                });

            if ($ignoreSessionId) {
                $studentQuery->where('id', '!=', $ignoreSessionId);
            }

            $studentOverlap = $studentQuery->first();
            if ($studentOverlap) {
                $conflicts[] = [
                    'type' => 'student_overlap',
                    'message' => __('Student is already enrolled in another session at this time: :title', [
                        'title' => $studentOverlap->title,
                    ]),
                    'session_id' => $studentOverlap->id,
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Create Recurring Schedule and generate all session instances in batch.
     */
    public function createSchedule(array $data, User $creator): RecurringSchedule
    {
        return DB::transaction(function () use ($data, $creator) {
            $course = Course::findOrFail($data['course_id']);
            $teacherProfileId = (int) $data['teacher_profile_id'];
            $studentUserId = !empty($data['student_user_id']) ? (int) $data['student_user_id'] : null;

            $schedule = RecurringSchedule::create([
                'teacher_profile_id' => $teacherProfileId,
                'course_id' => $course->id,
                'student_user_id' => $studentUserId,
                'title' => $data['title'],
                'recurrence_type' => $data['recurrence_type'] ?? 'weekly',
                'days_of_week' => $data['days_of_week'] ?? [],
                'monthly_pattern' => $data['monthly_pattern'] ?? null,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'] ?? Carbon::parse($data['start_time'])->addMinutes((int)($data['duration_minutes'] ?? 60))->format('H:i:s'),
                'duration_minutes' => (int) ($data['duration_minutes'] ?? 60),
                'timezone' => $data['timezone'] ?? 'Africa/Cairo',
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => 'active',
                'meeting_link' => $data['meeting_link'] ?? null,
                'meeting_platform' => $data['meeting_platform'] ?? 'agora',
                'notes' => $data['notes'] ?? null,
                'created_by_user_id' => $creator->id,
            ]);

            // Generate Session Instances
            $previewDates = $this->previewDates($data);

            foreach ($previewDates as $idx => $item) {
                $start = Carbon::parse($item['date'] . ' ' . $item['start_time']);
                $end = Carbon::parse($item['date'] . ' ' . $item['end_time']);

                LiveSession::create([
                    'title' => $schedule->title . ' (' . ($idx + 1) . ')',
                    'student_user_id' => $studentUserId,
                    'teacher_profile_id' => $teacherProfileId,
                    'subject_id' => $course->subject_id,
                    'course_id' => $course->id,
                    'recurring_schedule_id' => $schedule->id,
                    'scheduled_at' => $start,
                    'start_at' => $start,
                    'end_at' => $end,
                    'duration_minutes' => $schedule->duration_minutes,
                    'meeting_link' => $schedule->meeting_link,
                    'meeting_platform' => $schedule->meeting_platform,
                    'status' => 'scheduled',
                    'lifecycle_state' => 'scheduled',
                    'is_override' => false,
                ]);
            }

            SessionAuditLog::create([
                'user_id' => $creator->id,
                'recurring_schedule_id' => $schedule->id,
                'action' => 'created',
                'new_values' => $schedule->toArray(),
                'reason' => 'Created recurring schedule with ' . count($previewDates) . ' sessions',
                'ip_address' => request()->ip(),
            ]);

            return $schedule;
        });
    }

    /**
     * Scope 1: Edit This Session Only (Marks as override)
     */
    public function updateSingleSessionOverride(LiveSession $session, array $data, string $reason, User $user): LiveSession
    {
        return DB::transaction(function () use ($session, $data, $reason, $user) {
            $oldValues = $session->toArray();

            $scheduledAt = isset($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : $session->scheduled_at;
            $duration = (int) ($data['duration_minutes'] ?? $session->duration_minutes);
            $endAt = $scheduledAt->copy()->addMinutes($duration);

            $session->update([
                'title' => $data['title'] ?? $session->title,
                'scheduled_at' => $scheduledAt,
                'start_at' => $scheduledAt,
                'end_at' => $endAt,
                'duration_minutes' => $duration,
                'meeting_link' => $data['meeting_link'] ?? $session->meeting_link,
                'teacher_notes' => $data['teacher_notes'] ?? $session->teacher_notes,
                'is_override' => true,
                'override_reason' => $reason,
                'status' => $data['status'] ?? $session->status,
                'lifecycle_state' => $data['lifecycle_state'] ?? $session->lifecycle_state,
            ]);

            SessionAuditLog::create([
                'user_id' => $user->id,
                'live_session_id' => $session->id,
                'recurring_schedule_id' => $session->recurring_schedule_id,
                'action' => 'override_applied',
                'old_values' => $oldValues,
                'new_values' => $session->fresh()->toArray(),
                'reason' => $reason,
                'ip_address' => request()->ip(),
            ]);

            return $session;
        });
    }

    /**
     * Scope 2: Edit This and Future Sessions
     */
    public function updateFutureSessions(LiveSession $pivotSession, array $data, User $user): void
    {
        DB::transaction(function () use ($pivotSession, $data, $user) {
            $recurringSchedule = $pivotSession->recurringSchedule;
            if (! $recurringSchedule) {
                $this->updateSingleSessionOverride($pivotSession, $data, 'Single session update', $user);
                return;
            }

            $futureSessions = LiveSession::where('recurring_schedule_id', $recurringSchedule->id)
                ->where('scheduled_at', '>=', $pivotSession->scheduled_at)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->get();

            $newStartTime = $data['start_time'] ?? null;
            $newDuration = (int) ($data['duration_minutes'] ?? $recurringSchedule->duration_minutes);

            foreach ($futureSessions as $session) {
                $dateStr = $session->scheduled_at->format('Y-m-d');
                $start = $newStartTime ? Carbon::parse($dateStr . ' ' . $newStartTime) : $session->scheduled_at;
                $end = $start->copy()->addMinutes($newDuration);

                $session->update([
                    'title' => $data['title'] ?? $session->title,
                    'scheduled_at' => $start,
                    'start_at' => $start,
                    'end_at' => $end,
                    'duration_minutes' => $newDuration,
                    'meeting_link' => $data['meeting_link'] ?? $session->meeting_link,
                ]);
            }

            SessionAuditLog::create([
                'user_id' => $user->id,
                'recurring_schedule_id' => $recurringSchedule->id,
                'live_session_id' => $pivotSession->id,
                'action' => 'updated_future_sessions',
                'new_values' => $data,
                'reason' => 'Updated ' . $futureSessions->count() . ' future sessions',
                'ip_address' => request()->ip(),
            ]);
        });
    }

    /**
     * Scope 3: Edit Entire Recurring Schedule Rule and active sessions
     */
    public function updateEntireSchedule(RecurringSchedule $schedule, array $data, User $user): void
    {
        DB::transaction(function () use ($schedule, $data, $user) {
            $oldValues = $schedule->toArray();

            $schedule->update([
                'title' => $data['title'] ?? $schedule->title,
                'days_of_week' => $data['days_of_week'] ?? $schedule->days_of_week,
                'start_time' => $data['start_time'] ?? $schedule->start_time,
                'end_time' => $data['end_time'] ?? $schedule->end_time,
                'duration_minutes' => (int) ($data['duration_minutes'] ?? $schedule->duration_minutes),
                'meeting_link' => $data['meeting_link'] ?? $schedule->meeting_link,
                'notes' => $data['notes'] ?? $schedule->notes,
            ]);

            // Update uncompleted, non-overridden sessions
            $activeSessions = $schedule->sessions()
                ->where('scheduled_at', '>=', now())
                ->where('is_override', false)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->get();

            $newStartTime = $data['start_time'] ?? null;
            $newDuration = (int) ($data['duration_minutes'] ?? $schedule->duration_minutes);

            foreach ($activeSessions as $session) {
                $dateStr = $session->scheduled_at->format('Y-m-d');
                $start = $newStartTime ? Carbon::parse($dateStr . ' ' . $newStartTime) : $session->scheduled_at;
                $end = $start->copy()->addMinutes($newDuration);

                $session->update([
                    'title' => $schedule->title,
                    'scheduled_at' => $start,
                    'start_at' => $start,
                    'end_at' => $end,
                    'duration_minutes' => $newDuration,
                    'meeting_link' => $schedule->meeting_link,
                ]);
            }

            SessionAuditLog::create([
                'user_id' => $user->id,
                'recurring_schedule_id' => $schedule->id,
                'action' => 'updated_entire_schedule',
                'old_values' => $oldValues,
                'new_values' => $schedule->fresh()->toArray(),
                'reason' => 'Updated entire schedule rule and active sessions',
                'ip_address' => request()->ip(),
            ]);
        });
    }

    /**
     * Cancel an individual session
     */
    public function cancelSession(LiveSession $session, string $reason, User $user): LiveSession
    {
        return DB::transaction(function () use ($session, $reason, $user) {
            $oldValues = $session->toArray();

            $session->update([
                'status' => 'cancelled_by_teacher',
                'lifecycle_state' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            SessionAuditLog::create([
                'user_id' => $user->id,
                'live_session_id' => $session->id,
                'recurring_schedule_id' => $session->recurring_schedule_id,
                'action' => 'cancelled',
                'old_values' => $oldValues,
                'new_values' => $session->fresh()->toArray(),
                'reason' => $reason,
                'ip_address' => request()->ip(),
            ]);

            return $session;
        });
    }

    /**
     * Reschedule an individual session
     */
    public function rescheduleSession(LiveSession $session, Carbon $newScheduledAt, int $duration, string $reason, User $user): LiveSession
    {
        return DB::transaction(function () use ($session, $newScheduledAt, $duration, $reason, $user) {
            $oldValues = $session->toArray();
            $endAt = $newScheduledAt->copy()->addMinutes($duration);

            $conflicts = $this->detectConflicts($session->teacher_profile_id, $session->student_user_id, $newScheduledAt, $endAt, $session->id);
            if (! empty($conflicts)) {
                throw ValidationException::withMessages([
                    'scheduled_at' => $conflicts[0]['message'],
                ]);
            }

            $session->update([
                'scheduled_at' => $newScheduledAt,
                'start_at' => $newScheduledAt,
                'end_at' => $endAt,
                'duration_minutes' => $duration,
                'status' => 'scheduled',
                'lifecycle_state' => 'rescheduled',
                'is_override' => true,
                'override_reason' => $reason,
            ]);

            SessionAuditLog::create([
                'user_id' => $user->id,
                'live_session_id' => $session->id,
                'recurring_schedule_id' => $session->recurring_schedule_id,
                'action' => 'rescheduled',
                'old_values' => $oldValues,
                'new_values' => $session->fresh()->toArray(),
                'reason' => $reason,
                'ip_address' => request()->ip(),
            ]);

            return $session;
        });
    }
}

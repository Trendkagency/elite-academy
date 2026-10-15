<?php

namespace App\Services\Session;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\LiveSession;
use App\Models\RecurringSchedule;
use App\Models\SessionAuditLog;
use App\Models\StudentPackage;
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
     * Calculate automatic end date and check real-time student package quota.
     * End date is calculated automatically based on the course sessions count (or monthly distribution)
     * and the selected days of the week, with strict student package balance verification.
     */
    public function calculateEndDateAndQuota(array $params): array
    {
        $courseId = ! empty($params['course_id']) ? (int) $params['course_id'] : null;
        $studentUserId = (! empty($params['student_user_id']) && $params['student_user_id'] !== '__group__' && $params['student_user_id'] !== 'null')
            ? (int) $params['student_user_id']
            : null;
        $startDateStr = ! empty($params['start_date']) ? $params['start_date'] : now()->toDateString();
        $startDate = Carbon::parse($startDateStr)->startOfDay();
        $recurrenceType = $params['recurrence_type'] ?? 'weekly';
        $daysOfWeek = $params['days_of_week'] ?? [];

        // Normalize days of week
        $normalizedDays = array_values(array_unique(array_map(function ($d) {
            if (is_numeric($d)) return (int) $d;
            $lower = strtolower(trim((string) $d));
            return self::DAY_MAP[$lower] ?? 0;
        }, (array) $daysOfWeek)));

        $course = $courseId ? Course::find($courseId) : null;

        // Determine target sessions count:
        // 1. If course has defined sessions_count > 0, use it.
        // 2. Otherwise distribute across a standard month (4 weeks * selected days, min 4 sessions).
        $daysCount = count($normalizedDays);
        if ($course && (int) $course->sessions_count > 0) {
            $targetSessions = (int) $course->sessions_count;
            $sessionsSource = 'course_defined';
        } else {
            $effectiveDays = $daysCount > 0 ? $daysCount : 2;
            $targetSessions = max(1, $effectiveDays * 4); // 4 weeks of recurrence across the month
            $sessionsSource = 'monthly_distributed';
        }

        // Student Package Quota Check (for 1-to-1 private schedule)
        $studentQuota = null;
        if ($studentUserId) {
            $studentUser = User::find($studentUserId);

            $package = StudentPackage::where('student_user_id', $studentUserId)
                ->where(function ($q) use ($courseId) {
                    if ($courseId) {
                        $q->where('course_id', $courseId)
                          ->orWhereNull('course_id');
                    }
                })
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->orderByRaw('course_id IS NOT NULL DESC')
                ->latest('created_at')
                ->first();

            $isAr = app()->getLocale() === 'ar';
            if ($package) {
                $remaining = (int) $package->remaining_sessions;
                $canCreate = $remaining >= $targetSessions;
                $packageName = $package->packageTemplate?->name ?: ($isAr ? 'باقة مخصصة' : 'Custom Package');

                $studentQuota = [
                    'has_student' => true,
                    'student_name' => $studentUser?->name ?: "Student #{$studentUserId}",
                    'has_package' => true,
                    'package_id' => $package->id,
                    'package_name' => $packageName,
                    'total_sessions' => (int) $package->total_sessions,
                    'remaining_sessions' => $remaining,
                    'required_sessions' => $targetSessions,
                    'can_create' => $canCreate,
                    'status' => $canCreate ? 'sufficient' : 'insufficient',
                    'message' => $canCreate
                        ? ($isAr
                            ? "✅ رصيد باقة الطالب كافٍ ({$remaining} حصة متاحة من {$package->total_sessions}، والمطلوب {$targetSessions} حصة)."
                            : "✅ Student package balance is sufficient ({$remaining} available / {$targetSessions} required).")
                        : ($isAr
                            ? "❌ عجز في رصيد الباقة: الطالب لديه {$remaining} حصة متاحة فقط، بينما الجدول يتطلب {$targetSessions} حصة!"
                            : "❌ Insufficient package balance: Student has {$remaining} sessions available, but schedule requires {$targetSessions}."),
                ];
            } else {
                $hasAnyPackage = StudentPackage::where('student_user_id', $studentUserId)->exists();
                $isEnrolled = $courseId ? \App\Models\CourseEnrollment::where('course_id', $courseId)
                    ->where('student_user_id', $studentUserId)
                    ->where('status', 'active')
                    ->exists() : false;

                if (! $hasAnyPackage && $isEnrolled) {
                    $studentQuota = [
                        'has_student' => true,
                        'student_name' => $studentUser?->name ?: "Student #{$studentUserId}",
                        'has_package' => false,
                        'package_id' => null,
                        'package_name' => $isAr ? 'تسجيل معتمد في المقرر' : 'Active Course Enrollment',
                        'total_sessions' => $targetSessions,
                        'remaining_sessions' => $targetSessions,
                        'required_sessions' => $targetSessions,
                        'can_create' => true,
                        'status' => 'enrolled_direct',
                        'message' => $isAr
                            ? "✅ الطالب مسجل رسمياً في هذا المقرر بنجاح (تسجيل مباشر معتمد - لا يتطلب باقة منفصلة)."
                            : "✅ Student is actively enrolled in this course (direct enrollment - no separate package required).",
                    ];
                } else {
                    $studentQuota = [
                        'has_student' => true,
                        'student_name' => $studentUser?->name ?: "Student #{$studentUserId}",
                        'has_package' => false,
                        'package_id' => null,
                        'package_name' => null,
                        'total_sessions' => 0,
                        'remaining_sessions' => 0,
                        'required_sessions' => $targetSessions,
                        'can_create' => false,
                        'status' => 'no_package',
                        'message' => $isAr
                            ? "⚠️ لا توجد باقة حصص نشطة أو تسجيل معتمد لهذا الطالب في هذا المقرر. يرجى تفعيل أو شحن الباقة أولاً."
                            : "⚠️ No active package or valid course enrollment found for this student.",
                    ];
                }
            }
        }

        // Calculate exact end_date based on targetSessions and recurrence pattern
        $calculatedEndDate = $startDate->copy();

        if ($recurrenceType === 'single') {
            $calculatedEndDate = $startDate->copy();
        } elseif ($recurrenceType === 'monthly') {
            $calculatedEndDate = $startDate->copy()->addMonths(max(1, $targetSessions - 1));
        } else {
            // weekly, biweekly, daily
            if (empty($normalizedDays)) {
                $normalizedDays = [$startDate->dayOfWeek];
            }

            $cursor = $startDate->copy();
            $scheduledCount = 0;
            $maxSafetyLimit = 750;
            $loop = 0;

            // Reference base date for biweekly alternating parity
            $parityBaseDate = $startDate->copy()->startOfWeek(Carbon::SATURDAY);

            while ($scheduledCount < $targetSessions && $loop < $maxSafetyLimit) {
                $dayOfWeek = $cursor->dayOfWeek;
                $isBiweeklySkip = false;

                if ($recurrenceType === 'biweekly') {
                    $weekDiff = $cursor->copy()->startOfWeek(Carbon::SATURDAY)->diffInWeeks($parityBaseDate);
                    if (($weekDiff % 2) !== 0) {
                        $isBiweeklySkip = true;
                    }
                }

                if (! $isBiweeklySkip && in_array($dayOfWeek, $normalizedDays, true)) {
                    $scheduledCount++;
                    if ($scheduledCount === $targetSessions) {
                        $calculatedEndDate = $cursor->copy();
                        break;
                    }
                }

                $cursor->addDay();
                $loop++;
            }
        }

        return [
            'start_date' => $startDate->toDateString(),
            'end_date' => $calculatedEndDate->toDateString(),
            'target_sessions' => $targetSessions,
            'sessions_source' => $sessionsSource,
            'course_id' => $courseId,
            'course_title' => $course?->title,
            'course_sessions_count' => $course ? (int) $course->sessions_count : null,
            'student_quota' => $studentQuota,
        ];
    }

    /**
     * Preview recurring dates and validate conflicts without saving to the database.
     */
    public function previewDates(array $params): array
    {
        if (empty($params['end_date'])) {
            $calc = $this->calculateEndDateAndQuota($params);
            $params['end_date'] = $calc['end_date'];
        }

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
        $maxLimit = 1000; // Allow long schedules across academic semesters
        $count = 0;
        $ignoreRecurringScheduleId = ! empty($params['ignore_recurring_schedule_id']) ? (int) $params['ignore_recurring_schedule_id'] : null;

        if ($recurrenceType === 'single') {
            $sessionStart = Carbon::parse($startDate->format('Y-m-d') . ' ' . $startTime);
            $sessionEnd = $sessionStart->copy()->addMinutes($duration);
            $conflicts = $this->detectConflicts($teacherProfileId, $studentUserId, $sessionStart, $sessionEnd, null, $ignoreRecurringScheduleId);

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

        $dayStartTimes = $params['day_start_times'] ?? [];
        $dayDurations = $params['day_durations'] ?? [];
        $dayMeetingLinks = $params['day_meeting_links'] ?? [];
        $mainMeetingLink = $params['meeting_link'] ?? null;

        if (in_array($recurrenceType, ['weekly', 'biweekly', 'daily', 'multi_month', 'yearly'], true)) {
            if ($recurrenceType === 'daily' && empty($normalizedDays)) {
                $normalizedDays = [0, 1, 2, 3, 4, 5, 6];
            } elseif (empty($normalizedDays)) {
                $normalizedDays = [$startDate->dayOfWeek];
            }

            // Reference base date for alternating weeks in biweekly pattern
            $parityBaseDate = isset($params['base_start_date'])
                ? Carbon::parse($params['base_start_date'])->startOfWeek(Carbon::SATURDAY)
                : $startDate->copy()->startOfWeek(Carbon::SATURDAY);

            while ($current->lte($endDate) && $count < $maxLimit) {
                $dayOfWeek = $current->dayOfWeek;
                $isBiweeklySkip = false;

                if ($recurrenceType === 'biweekly') {
                    $weekDiff = $current->copy()->startOfWeek(Carbon::SATURDAY)->diffInWeeks($parityBaseDate);
                    if (($weekDiff % 2) !== 0) {
                        $isBiweeklySkip = true;
                    }
                }

                if (! $isBiweeklySkip && (in_array($dayOfWeek, $normalizedDays, true) || ($recurrenceType === 'daily' && empty($params['days_of_week'])))) {
                    $dayCustomTime = null;
                    if (is_array($dayStartTimes)) {
                        if (!empty($dayStartTimes[$dayOfWeek])) {
                            $dayCustomTime = $dayStartTimes[$dayOfWeek];
                        } elseif (!empty($dayStartTimes[(string)$dayOfWeek])) {
                            $dayCustomTime = $dayStartTimes[(string)$dayOfWeek];
                        } else {
                            $dayName = strtolower($current->englishDayOfWeek);
                            if (!empty($dayStartTimes[$dayName])) {
                                $dayCustomTime = $dayStartTimes[$dayName];
                            }
                        }
                    }

                    $dayName = strtolower($current->englishDayOfWeek);
                    $dayCustomDuration = null;
                    if (is_array($dayDurations)) {
                        if (!empty($dayDurations[$dayOfWeek])) {
                            $dayCustomDuration = (int) $dayDurations[$dayOfWeek];
                        } elseif (!empty($dayDurations[(string)$dayOfWeek])) {
                            $dayCustomDuration = (int) $dayDurations[(string)$dayOfWeek];
                        } elseif (!empty($dayDurations[$dayName])) {
                            $dayCustomDuration = (int) $dayDurations[$dayName];
                        }
                    }

                    $dayCustomLink = null;
                    if (is_array($dayMeetingLinks)) {
                        if (!empty($dayMeetingLinks[$dayOfWeek])) {
                            $dayCustomLink = $dayMeetingLinks[$dayOfWeek];
                        } elseif (!empty($dayMeetingLinks[(string)$dayOfWeek])) {
                            $dayCustomLink = $dayMeetingLinks[(string)$dayOfWeek];
                        } elseif (!empty($dayMeetingLinks[$dayName])) {
                            $dayCustomLink = $dayMeetingLinks[$dayName];
                        }
                    }

                    $effectiveStartTime = $dayCustomTime ?: $startTime;
                    $effectiveDuration = ($dayCustomDuration && $dayCustomDuration >= 15) ? $dayCustomDuration : $duration;
                    $effectiveLink = $dayCustomLink ?: $mainMeetingLink;

                    $sessionStart = Carbon::parse($current->format('Y-m-d') . ' ' . $effectiveStartTime);
                    $sessionEnd = $sessionStart->copy()->addMinutes($effectiveDuration);
                    $conflicts = $this->detectConflicts($teacherProfileId, $studentUserId, $sessionStart, $sessionEnd, null, $ignoreRecurringScheduleId);

                    $dates[] = [
                        'date' => $sessionStart->format('Y-m-d'),
                        'day_name' => $sessionStart->locale(app()->getLocale())->translatedFormat('l'),
                        'start_time' => $sessionStart->format('H:i'),
                        'end_time' => $sessionEnd->format('H:i'),
                        'meeting_link' => $effectiveLink,
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
                    $conflicts = $this->detectConflicts($teacherProfileId, $studentUserId, $sessionStart, $sessionEnd, null, $ignoreRecurringScheduleId);

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
    public function detectConflicts(int $teacherProfileId, ?int $studentUserId, Carbon $startAt, Carbon $endAt, ?int $ignoreSessionId = null, ?int $ignoreRecurringScheduleId = null): array
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

        if ($ignoreRecurringScheduleId) {
            $teacherQuery->where(function ($q) use ($ignoreRecurringScheduleId) {
                $q->whereNull('recurring_schedule_id')
                    ->orWhere('recurring_schedule_id', '!=', $ignoreRecurringScheduleId);
            });
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

            if ($ignoreRecurringScheduleId) {
                $studentQuery->where(function ($q) use ($ignoreRecurringScheduleId) {
                    $q->whereNull('recurring_schedule_id')
                        ->orWhere('recurring_schedule_id', '!=', $ignoreRecurringScheduleId);
                });
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

            // Automatically compute end_date and validate student package quota
            $calc = $this->calculateEndDateAndQuota($data);
            if (empty($data['end_date'])) {
                $data['end_date'] = $calc['end_date'];
            }

            if ($studentUserId) {
                $quota = $calc['student_quota'];
                if ($quota && ! $quota['can_create']) {
                    throw ValidationException::withMessages([
                        'student_user_id' => $quota['message'],
                    ]);
                }
            }

            $schedule = RecurringSchedule::create([
                'teacher_profile_id' => $teacherProfileId,
                'course_id' => $course->id,
                'student_user_id' => $studentUserId,
                'title' => $data['title'],
                'recurrence_type' => $data['recurrence_type'] ?? 'weekly',
                'days_of_week' => $data['days_of_week'] ?? [],
                'day_start_times' => $data['day_start_times'] ?? null,
                'day_durations' => $data['day_durations'] ?? null,
                'day_meeting_links' => $data['day_meeting_links'] ?? null,
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

                $session = LiveSession::create([
                    'title' => $schedule->title . ' (' . ($idx + 1) . ')',
                    'student_user_id' => $studentUserId,
                    'teacher_profile_id' => $teacherProfileId,
                    'subject_id' => $course->subject_id,
                    'course_id' => $course->id,
                    'recurring_schedule_id' => $schedule->id,
                    'scheduled_at' => $start,
                    'start_at' => $start,
                    'end_at' => $end,
                    'duration_minutes' => $start->diffInMinutes($end),
                    'meeting_link' => !empty($item['meeting_link']) ? $item['meeting_link'] : $schedule->meeting_link,
                    'meeting_platform' => $schedule->meeting_platform,
                    'status' => 'scheduled',
                    'lifecycle_state' => 'scheduled',
                    'is_override' => false,
                ]);

                if ($studentUserId) {
                    \App\Models\StudentSession::updateOrCreate([
                        'student_user_id' => $studentUserId,
                        'live_session_id' => $session->id,
                    ], [
                        'session_status' => 'scheduled',
                    ]);
                }
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

            $updatePayload = [
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
            ];

            if (array_key_exists('student_user_id', $data)) {
                $updatePayload['student_user_id'] = ! empty($data['student_user_id']) ? (int) $data['student_user_id'] : null;
            }

            $session->update($updatePayload);

            if (! empty($updatePayload['student_user_id'])) {
                \App\Models\StudentSession::firstOrCreate([
                    'student_user_id' => $updatePayload['student_user_id'],
                    'live_session_id' => $session->id,
                ], [
                    'session_status' => 'scheduled',
                ]);
            }

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

                $futurePayload = [
                    'title' => $data['title'] ?? $session->title,
                    'scheduled_at' => $start,
                    'start_at' => $start,
                    'end_at' => $end,
                    'duration_minutes' => $newDuration,
                    'meeting_link' => $data['meeting_link'] ?? $session->meeting_link,
                ];

                if (array_key_exists('student_user_id', $data)) {
                    $futurePayload['student_user_id'] = ! empty($data['student_user_id']) ? (int) $data['student_user_id'] : null;
                }

                $session->update($futurePayload);

                if (! empty($futurePayload['student_user_id'])) {
                    \App\Models\StudentSession::firstOrCreate([
                        'student_user_id' => $futurePayload['student_user_id'],
                        'live_session_id' => $session->id,
                    ], [
                        'session_status' => 'scheduled',
                    ]);
                }
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

            $startTime = $data['start_time'] ?? $schedule->start_time;
            $duration = (int) ($data['duration_minutes'] ?? $schedule->duration_minutes);
            $endTime = $data['end_time'] ?? Carbon::parse($startTime)->addMinutes($duration)->format('H:i:s');

            $merged = array_merge($schedule->toArray(), $data);
            $calc = $this->calculateEndDateAndQuota($merged);
            $endDate = $calc['end_date'];

            $studentUserId = array_key_exists('student_user_id', $data) ? $data['student_user_id'] : $schedule->student_user_id;
            if ($studentUserId) {
                $quota = $calc['student_quota'];
                if ($quota && ! $quota['can_create']) {
                    throw ValidationException::withMessages([
                        'student_user_id' => $quota['message'],
                    ]);
                }
            }

            $schedule->update([
                'title' => $data['title'] ?? $schedule->title,
                'teacher_profile_id' => $data['teacher_profile_id'] ?? $schedule->teacher_profile_id,
                'course_id' => $data['course_id'] ?? $schedule->course_id,
                'student_user_id' => $studentUserId,
                'recurrence_type' => $data['recurrence_type'] ?? $schedule->recurrence_type,
                'meeting_platform' => $data['meeting_platform'] ?? $schedule->meeting_platform,
                'status' => $data['status'] ?? $schedule->status,
                'days_of_week' => array_key_exists('days_of_week', $data) ? $data['days_of_week'] : $schedule->days_of_week,
                'day_start_times' => array_key_exists('day_start_times', $data) ? $data['day_start_times'] : $schedule->day_start_times,
                'day_durations' => array_key_exists('day_durations', $data) ? $data['day_durations'] : $schedule->day_durations,
                'day_meeting_links' => array_key_exists('day_meeting_links', $data) ? $data['day_meeting_links'] : $schedule->day_meeting_links,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $duration,
                'start_date' => $data['start_date'] ?? $schedule->start_date,
                'end_date' => $endDate,
                'meeting_link' => array_key_exists('meeting_link', $data) ? $data['meeting_link'] : $schedule->meeting_link,
                'notes' => array_key_exists('notes', $data) ? $data['notes'] : $schedule->notes,
            ]);

            $this->syncFutureSessions($schedule->fresh(), $user);

            SessionAuditLog::create([
                'user_id' => $user->id,
                'recurring_schedule_id' => $schedule->id,
                'action' => 'updated_entire_schedule',
                'old_values' => $oldValues,
                'new_values' => $schedule->fresh()->toArray(),
                'reason' => 'Updated entire schedule rule and synchronized future sessions',
                'ip_address' => request()->ip(),
            ]);
        });
    }

    /**
     * Synchronize all future live sessions with the recurring schedule rule.
     * Updates times, durations, links and titles for upcoming sessions,
     * deletes sessions for removed days/dates, and generates new sessions for added days/extended dates.
     */
    public function syncFutureSessions(RecurringSchedule $schedule, ?User $user = null): void
    {
        $now = now();

        if ($schedule->status === 'cancelled') {
            $schedule->liveSessions()
                ->where('scheduled_at', '>=', $now)
                ->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])
                ->update([
                    'status' => 'cancelled',
                    'lifecycle_state' => 'cancelled',
                    'cancellation_reason' => __('Recurring schedule was cancelled.'),
                ]);
            return;
        }

        if ($schedule->status !== 'active') {
            return;
        }

        $startDate = $schedule->start_date ? Carbon::parse($schedule->start_date)->startOfDay() : $now->copy()->startOfDay();
        $endDate = $schedule->end_date ? Carbon::parse($schedule->end_date)->endOfDay() : null;

        if (! $endDate || $endDate->lt($now)) {
            return;
        }

        // Only manage slots from max(today, start_date) up to end_date
        $effectiveStartDate = $startDate->lt($now->copy()->startOfDay()) ? $now->copy()->startOfDay() : $startDate;

        $previewParams = [
            'course_id' => $schedule->course_id,
            'teacher_profile_id' => $schedule->teacher_profile_id,
            'student_user_id' => $schedule->student_user_id,
            'start_date' => $effectiveStartDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'base_start_date' => $schedule->start_date ? Carbon::parse($schedule->start_date)->format('Y-m-d') : null,
            'ignore_recurring_schedule_id' => $schedule->id,
            'start_time' => $schedule->start_time ?: '10:00',
            'duration_minutes' => $schedule->duration_minutes ?: 60,
            'recurrence_type' => $schedule->recurrence_type ?? 'weekly',
            'days_of_week' => $schedule->days_of_week ?? [],
            'day_start_times' => $schedule->day_start_times ?? [],
            'day_durations' => $schedule->day_durations ?? [],
            'day_meeting_links' => $schedule->day_meeting_links ?? [],
            'meeting_link' => $schedule->meeting_link,
            'monthly_pattern' => $schedule->monthly_pattern ?? null,
        ];

        if (empty($schedule->days_of_week)) {
            $schedule->liveSessions()
                ->where('scheduled_at', '>=', $now)
                ->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])
                ->where('is_override', false)
                ->update([
                    'title' => $schedule->title,
                    'teacher_profile_id' => $schedule->teacher_profile_id,
                    'student_user_id' => $schedule->student_user_id,
                    'course_id' => $schedule->course_id,
                    'duration_minutes' => $schedule->duration_minutes ?: 60,
                    'meeting_link' => $schedule->meeting_link,
                    'meeting_platform' => $schedule->meeting_platform ?: 'agora',
                ]);
            return;
        }

        $targetSlots = $this->previewDates($previewParams);

        // Filter out slots that have already started/passed in the past
        $targetSlotsByDate = [];
        foreach ($targetSlots as $slot) {
            $slotStart = Carbon::parse($slot['date'] . ' ' . $slot['start_time']);
            if ($slotStart->isPast()) {
                continue;
            }
            $targetSlotsByDate[$slot['date']] = $slot;
        }

        // Fetch existing future uncompleted sessions for this schedule
        $existingFutureSessions = $schedule->liveSessions()
            ->where('scheduled_at', '>=', $now)
            ->whereNotIn('status', ['completed', 'cancelled', 'cancelled_by_teacher'])
            ->get();

        $existingByDate = [];
        $sessionsToDelete = [];

        foreach ($existingFutureSessions as $ses) {
            if ($ses->is_override) {
                // Keep manual teacher overrides untouched
                continue;
            }

            $dateStr = $ses->scheduled_at->format('Y-m-d');
            if (isset($targetSlotsByDate[$dateStr]) && ! isset($existingByDate[$dateStr])) {
                $existingByDate[$dateStr] = $ses;
            } else {
                // Day was unchecked, date range shortened, or redundant session on the same date
                $sessionsToDelete[] = $ses;
            }
        }

        // Delete orphaned sessions (clean up student sessions & attendances)
        foreach ($sessionsToDelete as $ses) {
            \App\Models\StudentSession::where('live_session_id', $ses->id)->delete();
            \App\Models\MeetingAttendance::where('live_session_id', $ses->id)->delete();
            $ses->delete();
        }

        // Update matching existing sessions
        foreach ($existingByDate as $dateStr => $ses) {
            $slot = $targetSlotsByDate[$dateStr];
            $start = Carbon::parse($slot['date'] . ' ' . $slot['start_time']);
            $end = Carbon::parse($slot['date'] . ' ' . $slot['end_time']);
            $duration = $start->diffInMinutes($end);
            $link = ! empty($slot['meeting_link']) ? $slot['meeting_link'] : $schedule->meeting_link;

            $ses->update([
                'title' => $schedule->title,
                'teacher_profile_id' => $schedule->teacher_profile_id,
                'student_user_id' => $schedule->student_user_id,
                'course_id' => $schedule->course_id,
                'subject_id' => $schedule->course?->subject_id ?? $ses->subject_id,
                'scheduled_at' => $start,
                'start_at' => $start,
                'end_at' => $end,
                'duration_minutes' => $duration,
                'meeting_link' => $link,
                'meeting_platform' => $schedule->meeting_platform ?: 'agora',
            ]);
        }

        // Create new sessions for dates in targetSlotsByDate that do not have an existing session
        $course = $schedule->course;
        foreach ($targetSlotsByDate as $dateStr => $slot) {
            if (isset($existingByDate[$dateStr])) {
                continue;
            }

            // If an override already exists on this date, respect it
            $hasOverride = $schedule->liveSessions()
                ->whereDate('scheduled_at', $dateStr)
                ->where('is_override', true)
                ->exists();
            if ($hasOverride) {
                continue;
            }

            $start = Carbon::parse($slot['date'] . ' ' . $slot['start_time']);
            $end = Carbon::parse($slot['date'] . ' ' . $slot['end_time']);
            $duration = $start->diffInMinutes($end);
            $link = ! empty($slot['meeting_link']) ? $slot['meeting_link'] : $schedule->meeting_link;

            $newSession = LiveSession::create([
                'title' => $schedule->title,
                'student_user_id' => $schedule->student_user_id,
                'teacher_profile_id' => $schedule->teacher_profile_id,
                'subject_id' => $course?->subject_id,
                'course_id' => $schedule->course_id,
                'recurring_schedule_id' => $schedule->id,
                'scheduled_at' => $start,
                'start_at' => $start,
                'end_at' => $end,
                'duration_minutes' => $duration,
                'meeting_link' => $link,
                'meeting_platform' => $schedule->meeting_platform ?: 'agora',
                'status' => 'scheduled',
                'lifecycle_state' => 'scheduled',
                'is_override' => false,
            ]);

            if ($schedule->student_user_id) {
                \App\Models\StudentSession::updateOrCreate([
                    'student_user_id' => $schedule->student_user_id,
                    'live_session_id' => $newSession->id,
                ], [
                    'session_status' => 'scheduled',
                ]);
            }
        }

        // Ensure any past date slots between start_date and today exist for recording past attendance
        if ($startDate->lt($now->copy()->startOfDay())) {
            $pastPreviewParams = array_merge($previewParams, [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $now->format('Y-m-d'),
            ]);
            $pastSlots = $this->previewDates($pastPreviewParams);
            foreach ($pastSlots as $slot) {
                $dateStr = $slot['date'];
                $exists = $schedule->liveSessions()->whereDate('scheduled_at', $dateStr)->exists();
                if (! $exists) {
                    $start = Carbon::parse($slot['date'] . ' ' . $slot['start_time']);
                    $end = Carbon::parse($slot['date'] . ' ' . $slot['end_time']);
                    $duration = $start->diffInMinutes($end);
                    $link = ! empty($slot['meeting_link']) ? $slot['meeting_link'] : $schedule->meeting_link;

                    $pastSession = LiveSession::create([
                        'title' => $schedule->title,
                        'student_user_id' => $schedule->student_user_id,
                        'teacher_profile_id' => $schedule->teacher_profile_id,
                        'subject_id' => $course?->subject_id,
                        'course_id' => $schedule->course_id,
                        'recurring_schedule_id' => $schedule->id,
                        'scheduled_at' => $start,
                        'start_at' => $start,
                        'end_at' => $end,
                        'duration_minutes' => $duration,
                        'meeting_link' => $link,
                        'meeting_platform' => $schedule->meeting_platform ?: 'agora',
                        'status' => 'scheduled',
                        'lifecycle_state' => 'scheduled',
                        'is_override' => false,
                    ]);

                    if ($schedule->student_user_id) {
                        \App\Models\StudentSession::updateOrCreate([
                            'student_user_id' => $schedule->student_user_id,
                            'live_session_id' => $pastSession->id,
                        ], [
                            'session_status' => 'scheduled',
                        ]);
                    }
                }
            }
        }
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

    /**
     * Delete Recurring Schedule and clean up future uncompleted sessions
     */
    public function deleteSchedule(RecurringSchedule $schedule, User $user): void
    {
        DB::transaction(function () use ($schedule, $user) {
            $oldValues = $schedule->toArray();

            // Delete upcoming/scheduled sessions that have not been completed
            $sessionsToDelete = $schedule->sessions()
                ->whereNotIn('status', ['completed'])
                ->get();

            foreach ($sessionsToDelete as $ses) {
                // Delete related student sessions and attendance if no actual completion
                \App\Models\StudentSession::where('live_session_id', $ses->id)->delete();
                \App\Models\MeetingAttendance::where('live_session_id', $ses->id)->delete();
                $ses->delete();
            }

            SessionAuditLog::create([
                'user_id' => $user->id,
                'recurring_schedule_id' => $schedule->id,
                'action' => 'deleted',
                'old_values' => $oldValues,
                'reason' => 'Deleted recurring schedule and ' . $sessionsToDelete->count() . ' uncompleted sessions',
                'ip_address' => request()->ip(),
            ]);

            $schedule->delete();
        });
    }

    /**
     * Extend recurring schedule cycle to a new end date and generate new session instances.
     */
    public function extendScheduleCycle(RecurringSchedule $schedule, Carbon|string $newEndDate, ?User $user = null, ?string $reason = 'Extended recurring schedule cycle'): int
    {
        $newEnd = Carbon::parse($newEndDate)->endOfDay();
        $oldEnd = Carbon::parse($schedule->end_date)->startOfDay();

        if ($newEnd->toDateString() <= $oldEnd->toDateString()) {
            throw ValidationException::withMessages([
                'new_end_date' => __('تاريخ نهاية الدورة الجديد (:new) يجب أن يكون بعد تاريخ نهاية الدورة الحالية (:current).', [
                    'new' => $newEnd->format('Y-m-d'),
                    'current' => $schedule->end_date ? Carbon::parse($schedule->end_date)->format('Y-m-d') : 'اليوم'
                ]),
            ]);
        }

        $userId = $user?->id ?? auth()->id() ?? User::whereHas('adminProfile')->value('id') ?? 1;

        return DB::transaction(function () use ($schedule, $oldEnd, $newEnd, $userId, $reason) {
            $oldValues = $schedule->toArray();

            // Preview dates for the newly extended window
            $extensionParams = [
                'start_date' => $oldEnd->copy()->addDay()->format('Y-m-d'),
                'end_date' => $newEnd->format('Y-m-d'),
                'start_time' => $schedule->start_time ?: '10:00',
                'duration_minutes' => $schedule->duration_minutes ?: 60,
                'recurrence_type' => $schedule->recurrence_type ?? 'weekly',
                'days_of_week' => $schedule->days_of_week ?? [],
                'day_start_times' => $schedule->day_start_times ?? [],
                'day_durations' => $schedule->day_durations ?? [],
                'day_meeting_links' => $schedule->day_meeting_links ?? [],
                'meeting_link' => $schedule->meeting_link,
                'teacher_profile_id' => $schedule->teacher_profile_id,
                'student_user_id' => $schedule->student_user_id,
            ];

            $newDates = $this->previewDates($extensionParams);
            $existingCount = $schedule->sessions()->count();
            $generatedCount = 0;

            foreach ($newDates as $idx => $item) {
                $start = Carbon::parse($item['date'] . ' ' . $item['start_time']);
                $end = Carbon::parse($item['date'] . ' ' . $item['end_time']);

                $session = LiveSession::create([
                    'title' => $schedule->title . ' (' . ($existingCount + $idx + 1) . ')',
                    'student_user_id' => $schedule->student_user_id,
                    'teacher_profile_id' => $schedule->teacher_profile_id,
                    'subject_id' => $schedule->course?->subject_id,
                    'course_id' => $schedule->course_id,
                    'recurring_schedule_id' => $schedule->id,
                    'scheduled_at' => $start,
                    'start_at' => $start,
                    'end_at' => $end,
                    'duration_minutes' => $start->diffInMinutes($end),
                    'meeting_link' => !empty($item['meeting_link']) ? $item['meeting_link'] : $schedule->meeting_link,
                    'meeting_platform' => $schedule->meeting_platform ?: 'agora',
                    'status' => 'scheduled',
                    'lifecycle_state' => 'scheduled',
                    'is_override' => false,
                ]);

                if ($schedule->student_user_id) {
                    \App\Models\StudentSession::updateOrCreate([
                        'student_user_id' => $schedule->student_user_id,
                        'live_session_id' => $session->id,
                    ], [
                        'session_status' => 'scheduled',
                    ]);
                }

                $generatedCount++;
            }

            $schedule->update([
                'end_date' => $newEnd->format('Y-m-d'),
                'status' => 'active',
            ]);

            SessionAuditLog::create([
                'user_id' => $userId,
                'recurring_schedule_id' => $schedule->id,
                'action' => 'extended',
                'old_values' => $oldValues,
                'new_values' => $schedule->fresh()->toArray(),
                'reason' => $reason . " — Added {$generatedCount} sessions until " . $newEnd->format('Y-m-d'),
                'ip_address' => request()->ip(),
            ]);

            return $generatedCount;
        });
    }
}

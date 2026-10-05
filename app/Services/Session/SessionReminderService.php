<?php

namespace App\Services\Session;

use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\Notification\FcmNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SessionReminderService
{
    public function __construct(
        protected FcmNotificationService $fcmService
    ) {}

    /**
     * Process all upcoming live sessions and dispatch multi-tier reminders:
     * - More than 24h away (e.g. tomorrow/earlier): Every 3 hours (48h, 45h, 42h, 39h, 36h, 33h, 30h, 27h)
     * - 24 hours milestone: 24h
     * - On session day (between 24h and 2h): Every 1 hour (23h down to 2h)
     * - Approaching session (< 2 hours): Every 15 minutes (105m, 90m, 75m, 1h, 45m, 30m, 15m)
     * - Final countdown (< 15 min & live): 10m, 5m, 2m, started (0m), live_5m
     * 
     * Notifies Student(s), Teacher, and Admins via DB notification & real-time FCM WebPush with loud sound.
     */
    public function processDueReminders(): int
    {
        $now = Carbon::now();
        $dispatchedCount = 0;

        // Fetch sessions scheduled between 10 minutes ago and the next 49 hours
        $sessions = LiveSession::with(['teacherProfile.user', 'studentUser', 'course', 'subject'])
            ->whereNotIn('status', ['cancelled', 'cancelled_by_teacher', 'completed'])
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNotNull('scheduled_at')
                      ->where('scheduled_at', '>=', $now->copy()->subMinutes(12))
                      ->where('scheduled_at', '<=', $now->copy()->addHours(49));
                })->orWhere(function ($q) use ($now) {
                    $q->whereNull('scheduled_at')
                      ->whereNotNull('start_at')
                      ->where('start_at', '>=', $now->copy()->subMinutes(12))
                      ->where('start_at', '<=', $now->copy()->addHours(49));
                });
            })
            ->get();

        foreach ($sessions as $session) {
            $scheduledAt = $session->scheduled_at ?: $session->start_at;
            if (! $scheduledAt) continue;

            $diffInMinutes = (int) round($now->diffInMinutes($scheduledAt, false)); // positive if in the future
            $sent = is_array($session->reminders_sent) ? $session->reminders_sent : [];

            $eligibleTier = $this->determineEligibleTier($diffInMinutes, $sent);

            if ($eligibleTier) {
                $tierKey = $eligibleTier['key'];
                $humanTime = $eligibleTier['human_time'];

                $this->dispatchReminder($session, $tierKey, $humanTime);

                $sent[] = $tierKey;
                $updateData = ['reminders_sent' => array_values(array_unique($sent))];

                if ($tierKey === 'started') {
                    $updateData['lifecycle_state'] = 'ready';
                    if ($session->status === 'scheduled') {
                        $updateData['status'] = 'link_visible';
                    }
                }

                $session->update($updateData);
                $dispatchedCount++;
            }
        }

        return $dispatchedCount;
    }

    /**
     * Determine which reminder tier is currently active and due for this session.
     */
    protected function determineEligibleTier(int $diffInMinutes, array $sent): ?array
    {
        // 1. Session Started / Live Now (-5m to 1m)
        if ($diffInMinutes <= 1 && $diffInMinutes >= -5 && !in_array('started', $sent, true)) {
            return ['key' => 'started', 'human_time' => app()->getLocale() === 'ar' ? 'الآن فوراً (بدأت الحصة)' : 'right now (Session is live)'];
        }

        // 2. Final Countdown Tiers (10m, 5m, 2m)
        if ($diffInMinutes <= 3 && $diffInMinutes >= 1 && !in_array('2m', $sent, true)) {
            return ['key' => '2m', 'human_time' => app()->getLocale() === 'ar' ? 'دقيقتين' : '2 minutes'];
        }
        if ($diffInMinutes <= 7 && $diffInMinutes >= 4 && !in_array('5m', $sent, true)) {
            return ['key' => '5m', 'human_time' => app()->getLocale() === 'ar' ? '5 دقائق' : '5 minutes'];
        }
        if ($diffInMinutes <= 12 && $diffInMinutes >= 8 && !in_array('10m', $sent, true)) {
            return ['key' => '10m', 'human_time' => app()->getLocale() === 'ar' ? '10 دقائق' : '10 minutes'];
        }

        // 3. Approaching Tiers: Every 15 minutes under 2 hours
        // 15m tier (12m to 20m) — preserves standard '15m' key for test suite
        if ($diffInMinutes <= 20 && $diffInMinutes >= 12 && !in_array('15m', $sent, true)) {
            return ['key' => '15m', 'human_time' => app()->getLocale() === 'ar' ? '15 دقيقة' : '15 minutes'];
        }
        // 30m tier (25m to 35m)
        if ($diffInMinutes <= 35 && $diffInMinutes >= 25 && !in_array('30m', $sent, true)) {
            return ['key' => '30m', 'human_time' => app()->getLocale() === 'ar' ? '30 دقيقة' : '30 minutes'];
        }
        // 45m tier (40m to 50m)
        if ($diffInMinutes <= 50 && $diffInMinutes >= 40 && !in_array('45m', $sent, true)) {
            return ['key' => '45m', 'human_time' => app()->getLocale() === 'ar' ? '45 دقيقة' : '45 minutes'];
        }
        // 1h tier (50m to 70m) — preserves standard '1h' key for test suite
        if ($diffInMinutes <= 70 && $diffInMinutes >= 50 && !in_array('1h', $sent, true)) {
            return ['key' => '1h', 'human_time' => app()->getLocale() === 'ar' ? 'ساعة واحدة' : '1 hour'];
        }
        // 75m tier (70m to 80m)
        if ($diffInMinutes <= 80 && $diffInMinutes >= 70 && !in_array('75m', $sent, true)) {
            return ['key' => '75m', 'human_time' => app()->getLocale() === 'ar' ? 'ساعة و15 دقيقة' : '1 hour and 15 minutes'];
        }
        // 90m tier (85m to 95m)
        if ($diffInMinutes <= 95 && $diffInMinutes >= 85 && !in_array('90m', $sent, true)) {
            return ['key' => '90m', 'human_time' => app()->getLocale() === 'ar' ? 'ساعة ونصف' : '1 hour and 30 minutes'];
        }
        // 105m tier (100m to 110m)
        if ($diffInMinutes <= 110 && $diffInMinutes >= 100 && !in_array('105m', $sent, true)) {
            return ['key' => '105m', 'human_time' => app()->getLocale() === 'ar' ? 'ساعة و45 دقيقة' : '1 hour and 45 minutes'];
        }

        // 4. Hourly Tiers on the day of the session (from 2h up to 23h before)
        $hourlyCheck = [
            2 => '2h', 3 => '3h', 4 => '4h', 5 => '5h', 6 => '6h', 7 => '7h', 8 => '8h',
            9 => '9h', 10 => '10h', 11 => '11h', 12 => '12h', 13 => '13h', 14 => '14h',
            15 => '15h', 16 => '16h', 17 => '17h', 18 => '18h', 19 => '19h', 20 => '20h',
            21 => '21h', 22 => '22h', 23 => '23h'
        ];
        foreach ($hourlyCheck as $hours => $key) {
            $targetMin = $hours * 60;
            if ($diffInMinutes <= ($targetMin + 15) && $diffInMinutes >= ($targetMin - 15) && !in_array($key, $sent, true)) {
                $humanStr = app()->getLocale() === 'ar' ? "{$hours} ساعات" : "{$hours} hours";
                return ['key' => $key, 'human_time' => $humanStr];
            }
        }

        // 5. The 24-Hour Milestone — preserves standard '24h' key
        if ($diffInMinutes <= 1500 && $diffInMinutes >= 1380 && !in_array('24h', $sent, true)) {
            return ['key' => '24h', 'human_time' => app()->getLocale() === 'ar' ? '24 ساعة (غداً)' : '24 hours (Tomorrow)'];
        }

        // 6. Multi-day Prior Tiers (> 24h up to 48h): Every 3 hours
        $prior3hCheck = [
            27 => '27h', 30 => '30h', 33 => '33h', 36 => '36h',
            39 => '39h', 42 => '42h', 45 => '45h', 48 => '48h'
        ];
        foreach ($prior3hCheck as $hours => $key) {
            $targetMin = $hours * 60;
            if ($diffInMinutes <= ($targetMin + 25) && $diffInMinutes >= ($targetMin - 25) && !in_array($key, $sent, true)) {
                $humanStr = app()->getLocale() === 'ar' ? "{$hours} ساعة" : "{$hours} hours";
                return ['key' => $key, 'human_time' => $humanStr];
            }
        }

        return null;
    }

    /**
     * Dispatch notification to Student(s), Teacher, and Admins in real time.
     */
    protected function dispatchReminder(LiveSession $session, string $tier, string $humanTimeLeft): void
    {
        $teacher = $session->teacherProfile?->user;
        $subjectName = $session->subject?->name ?: ($session->title ?: (app()->getLocale() === 'ar' ? 'الحصة المباشرة' : 'Live Session'));
        $scheduledAt = $session->scheduled_at ?: $session->start_at;
        $timeStr = $scheduledAt ? $scheduledAt->format('H:i') : '';

        $isUrgent = in_array($tier, ['started', '2m', '5m', '10m', '15m'], true);
        $urgencyIcon = $isUrgent ? '🚨' : '⏰';

        // 1. ─── NOTIFY STUDENT(S) ───────────────────────────────────────────
        $studentsToNotify = collect();
        if ($session->studentUser) {
            $studentsToNotify->push($session->studentUser);
        }
        if ($session->course_id) {
            $enrolledStudents = CourseEnrollment::where('course_id', $session->course_id)
                ->whereIn('status', ['active', 'enrolled', 'completed'])
                ->with('studentUser')
                ->get()
                ->pluck('studentUser')
                ->filter();
            $studentsToNotify = $studentsToNotify->merge($enrolledStudents)->unique('id');
        }

        foreach ($studentsToNotify as $student) {
            $studentTitle = app()->getLocale() === 'ar'
                ? "{$urgencyIcon} تذكير بموعد حصتك: {$subjectName}"
                : "{$urgencyIcon} Session Reminder: {$subjectName}";

            $studentBody = app()->getLocale() === 'ar'
                ? ($tier === 'started'
                    ? "بدأت الآن حصة ({$subjectName})! ادخل القاعة التفاعلية فوراً للمشاركة."
                    : "تذكير: تبدأ حصتك المباشرة لمادة ({$subjectName}) خلال {$humanTimeLeft} (الساعة {$timeStr}). استعد للحضور.")
                : ($tier === 'started'
                    ? "Session ({$subjectName}) is live now! Join the classroom immediately."
                    : "Reminder: Your live session ({$subjectName}) starts in {$humanTimeLeft} at {$timeStr}. Get ready to join.");

            try {
                $this->fcmService->sendNotification(
                    $student,
                    'SESSION_REMINDER_' . strtoupper($tier),
                    $studentTitle,
                    $studentBody,
                    route('student-portal')
                );
            } catch (\Throwable $e) {
                Log::warning("[SessionReminder] Failed notifying student #{$student->id}: " . $e->getMessage());
            }

            // Also notify Parent if student has linked parent profile
            $parent = $student->parentProfile?->user;
            if ($parent) {
                $parentTitle = app()->getLocale() === 'ar'
                    ? "{$urgencyIcon} تذكير بحصة نجلكم ({$student->name})"
                    : "{$urgencyIcon} Session Reminder for ({$student->name})";

                $parentBody = app()->getLocale() === 'ar'
                    ? "تذكير: حصة نجلكم ({$student->name}) لمادة ({$subjectName}) تبدأ خلال {$humanTimeLeft}."
                    : "Reminder: Session for ({$student->name}) in ({$subjectName}) starts in {$humanTimeLeft}.";

                try {
                    $this->fcmService->sendNotification(
                        $parent,
                        'CHILD_SESSION_REMINDER_' . strtoupper($tier),
                        $parentTitle,
                        $parentBody,
                        route('parent-portal')
                    );
                } catch (\Throwable $e) {}
            }
        }

        // 2. ─── NOTIFY TEACHER ──────────────────────────────────────────────
        if ($teacher) {
            $teacherTitle = app()->getLocale() === 'ar'
                ? "{$urgencyIcon} تذكير بموعد حصتك التدريسية: {$subjectName}"
                : "{$urgencyIcon} Teaching Session Reminder: {$subjectName}";

            $teacherBody = app()->getLocale() === 'ar'
                ? ($tier === 'started'
                    ? "حان موعد حصتك التدريسية ({$subjectName}) الآن! يرجى فتح البث التفاعلي واستقبال الطلاب."
                    : "تذكير: موعد حصتك التدريسية ({$subjectName}) خلال {$humanTimeLeft} (الساعة {$timeStr}). يرجى تجهيز البث.")
                : ($tier === 'started'
                    ? "Your session ({$subjectName}) is live now! Please start the interactive stream."
                    : "Reminder: Your teaching session ({$subjectName}) starts in {$humanTimeLeft} at {$timeStr}.");

            try {
                $this->fcmService->sendNotification(
                    $teacher,
                    'TEACHER_SESSION_REMINDER_' . strtoupper($tier),
                    $teacherTitle,
                    $teacherBody,
                    route('teachers')
                );
            } catch (\Throwable $e) {
                Log::warning("[SessionReminder] Failed notifying teacher #{$teacher->id}: " . $e->getMessage());
            }
        }

        // 3. ─── NOTIFY ADMINS ───────────────────────────────────────────────
        $teacherName = $teacher?->name ?: (app()->getLocale() === 'ar' ? 'المعلم' : 'Teacher');
        $adminTitle = app()->getLocale() === 'ar'
            ? "🔔 تذكير جلسة مباشرة: {$subjectName}"
            : "🔔 Live Session Reminder: {$subjectName}";

        $adminBody = app()->getLocale() === 'ar'
            ? ($tier === 'started'
                ? "بدأت الآن الجلسة المباشرة ({$subjectName}) للمعلم ({$teacherName}). الجلسة قيد الانعقاد."
                : "تنبيه: تبدأ الجلسة المباشرة ({$subjectName}) للمعلم ({$teacherName}) خلال {$humanTimeLeft} (الساعة {$timeStr}).")
            : ($tier === 'started'
                ? "Live session ({$subjectName}) by ({$teacherName}) is running now."
                : "Alert: Live session ({$subjectName}) by ({$teacherName}) starts in {$humanTimeLeft} at {$timeStr}.");

        try {
            $this->fcmService->notifyAllAdmins(
                'ADMIN_SESSION_REMINDER_' . strtoupper($tier),
                $adminTitle,
                $adminBody,
                url('/admin/live-sessions')
            );
        } catch (\Throwable $e) {
            Log::warning("[SessionReminder] Failed notifying admins: " . $e->getMessage());
        }
    }
}

<?php

namespace App\Services\Session;

use App\Models\LiveSession;
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
     * Process all upcoming sessions and dispatch multi-tier reminders (24h, 1h, 15m, started)
     */
    public function processDueReminders(): int
    {
        $now = Carbon::now();
        $dispatchedCount = 0;

        // 1. Sessions within the next 25 hours
        $sessions = LiveSession::with(['teacherProfile.user', 'studentUser', 'course'])
            ->whereNotIn('status', ['cancelled', 'cancelled_by_teacher', 'completed'])
            ->where('scheduled_at', '>=', $now->copy()->subMinutes(10))
            ->where('scheduled_at', '<=', $now->copy()->addHours(25))
            ->get();

        foreach ($sessions as $session) {
            $scheduledAt = $session->scheduled_at ?: $session->start_at;
            if (! $scheduledAt) continue;

            $diffInMinutes = $now->diffInMinutes($scheduledAt, false); // positive if in future
            $sent = $session->reminders_sent ?? [];

            // 24 Hours Reminder (between 23h and 25h before)
            if ($diffInMinutes <= 1500 && $diffInMinutes >= 1380 && !in_array('24h', $sent, true)) {
                $this->dispatchReminder($session, '24h', __('Upcoming session tomorrow: :title at :time', [
                    'title' => $session->title,
                    'time' => $scheduledAt->format('H:i'),
                ]));
                $sent[] = '24h';
                $session->update(['reminders_sent' => $sent]);
                $dispatchedCount++;
            }

            // 1 Hour Reminder (between 50m and 70m before)
            if ($diffInMinutes <= 70 && $diffInMinutes >= 50 && !in_array('1h', $sent, true)) {
                $this->dispatchReminder($session, '1h', __('Upcoming session in 1 hour: :title', [
                    'title' => $session->title,
                ]));
                $sent[] = '1h';
                $session->update(['reminders_sent' => $sent]);
                $dispatchedCount++;
            }

            // 15 Minutes Reminder (between 10m and 20m before)
            if ($diffInMinutes <= 20 && $diffInMinutes >= 10 && !in_array('15m', $sent, true)) {
                $this->dispatchReminder($session, '15m', __('Session starting in 15 minutes! Get ready to join :title', [
                    'title' => $session->title,
                ]));
                $sent[] = '15m';
                $session->update(['reminders_sent' => $sent]);
                $dispatchedCount++;
            }

            // Session Started (between -5m and 5m)
            if ($diffInMinutes <= 5 && $diffInMinutes >= -10 && !in_array('started', $sent, true)) {
                $this->dispatchReminder($session, 'started', __('Session is live now! Join the classroom for :title', [
                    'title' => $session->title,
                ]));
                $sent[] = 'started';
                $session->update([
                    'reminders_sent' => $sent,
                    'lifecycle_state' => 'ready',
                ]);
                $dispatchedCount++;
            }
        }

        return $dispatchedCount;
    }

    /**
     * Dispatch notification to Student, Parent, and Teacher
     */
    protected function dispatchReminder(LiveSession $session, string $type, string $message): void
    {
        $student = $session->studentUser;
        $teacher = $session->teacherProfile?->user;

        // 1. Notify Student
        if ($student) {
            UserNotification::create([
                'user_id' => $student->id,
                'title' => __('Session Reminder'),
                'body' => $message,
                'type' => 'SESSION_REMINDER_' . strtoupper($type),
                'action_url' => route('student-portal'),
                'is_read' => false,
            ]);

            try {
                $this->fcmService->sendToUser(
                    $student,
                    __('Session Reminder'),
                    $message,
                    [
                        'type' => 'SESSION_REMINDER',
                        'session_id' => (string) $session->id,
                        'url' => route('student-portal'),
                    ]
                );
            } catch (\Throwable $e) {
                Log::warning('FCM Reminder failed for student: ' . $e->getMessage());
            }

            // 2. Notify Parent if linked
            $parent = $student->parentProfile?->user;
            if ($parent) {
                UserNotification::create([
                    'user_id' => $parent->id,
                    'title' => __('Child Session Reminder'),
                    'body' => __('Your child :name has an upcoming session: :title', [
                        'name' => $student->name,
                        'title' => $session->title,
                    ]),
                    'type' => 'CHILD_SESSION_REMINDER',
                    'action_url' => route('parent-portal'),
                    'is_read' => false,
                ]);
            }
        }

        // 3. Notify Teacher
        if ($teacher && in_array($type, ['1h', '15m', 'started'], true)) {
            UserNotification::create([
                'user_id' => $teacher->id,
                'title' => __('Teaching Session Reminder'),
                'body' => __('You have a teaching session: :title', ['title' => $session->title]),
                'type' => 'TEACHER_SESSION_REMINDER',
                'action_url' => route('teacher-portal'),
                'is_read' => false,
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Services\Notification\FcmNotificationService;
use App\Services\Session\SessionReminderService;
use Illuminate\Console\Command;

class SendUpcomingSessionReminders extends Command
{
    protected $signature = 'notifications:upcoming-sessions';

    protected $description = 'Scan upcoming live sessions and dispatch real-time escalating multi-tier notifications to students, teachers, and admins';

    public function handle(SessionReminderService $reminderService, FcmNotificationService $notificationService): int
    {
        $this->info('Scanning upcoming live sessions and dispatching escalating reminders...');

        $dispatched = $reminderService->processDueReminders();

        $this->info("Completed! Dispatched {$dispatched} multi-tier session notification event(s).");

        return Command::SUCCESS;
    }
}

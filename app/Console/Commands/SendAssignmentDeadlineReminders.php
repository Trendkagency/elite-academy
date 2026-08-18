<?php

namespace App\Console\Commands;

use App\Services\Notification\FcmNotificationService;
use Illuminate\Console\Command;

class SendAssignmentDeadlineReminders extends Command
{
    protected $signature = 'notifications:deadline-reminders';

    protected $description = 'Scan upcoming live sessions in 24 hours and send FCM & system notifications to students with unsubmitted assignments';

    public function handle(FcmNotificationService $notificationService): int
    {
        $this->info('Scanning upcoming live sessions starting in 24 hours...');

        $remindersSent = $notificationService->sendAssignmentDeadlineReminders();

        $this->info("Completed! Dispatched {$remindersSent} assignment deadline reminder notification(s).");

        return Command::SUCCESS;
    }
}

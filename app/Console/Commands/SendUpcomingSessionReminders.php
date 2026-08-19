<?php

namespace App\Console\Commands;

use App\Services\Notification\FcmNotificationService;
use Illuminate\Console\Command;

class SendUpcomingSessionReminders extends Command
{
    protected $signature = 'notifications:upcoming-sessions';

    protected $description = 'Scan upcoming live sessions starting within 45 minutes and send FCM & system notifications to students';

    public function handle(FcmNotificationService $notificationService): int
    {
        $this->info('Scanning upcoming live sessions starting soon...');

        $remindersSent = $notificationService->sendUpcomingSessionReminders();

        $this->info("Completed! Dispatched {$remindersSent} upcoming session notification(s).");

        return Command::SUCCESS;
    }
}

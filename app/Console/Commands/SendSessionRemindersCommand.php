<?php

namespace App\Console\Commands;

use App\Services\Session\SessionReminderService;
use Illuminate\Console\Command;

class SendSessionRemindersCommand extends Command
{
    protected $signature = 'sessions:send-reminders';
    protected $description = 'Process and dispatch scheduled session reminders (24h, 1h, 15m, started)';

    public function handle(SessionReminderService $reminderService): int
    {
        $this->info('Checking for due session reminders...');
        $count = $reminderService->processDueReminders();
        $this->info("Processed reminders for {$count} trigger events.");
        return self::SUCCESS;
    }
}

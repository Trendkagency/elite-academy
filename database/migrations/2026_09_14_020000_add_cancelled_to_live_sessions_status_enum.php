<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE live_sessions MODIFY COLUMN status ENUM('scheduled','link_visible','in_progress','completed','cancelled','cancelled_by_teacher','rescheduled') NOT NULL DEFAULT 'scheduled'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE live_sessions MODIFY COLUMN status ENUM('scheduled','link_visible','in_progress','completed','cancelled_by_teacher','rescheduled') NOT NULL DEFAULT 'scheduled'");
        }
    }
};

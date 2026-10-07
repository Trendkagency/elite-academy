<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE live_sessions MODIFY COLUMN status ENUM('scheduled','link_visible','in_progress','live','completed','cancelled','cancelled_by_teacher','rescheduled') NOT NULL DEFAULT 'scheduled'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE live_sessions MODIFY COLUMN status ENUM('scheduled','link_visible','in_progress','completed','cancelled','cancelled_by_teacher','rescheduled') NOT NULL DEFAULT 'scheduled'");
        }
    }
};

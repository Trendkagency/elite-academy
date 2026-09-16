<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('recurring_schedules') && !Schema::hasColumn('recurring_schedules', 'day_meeting_links')) {
            Schema::table('recurring_schedules', function (Blueprint $table) {
                $table->json('day_meeting_links')->nullable()->after('day_start_times');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recurring_schedules') && Schema::hasColumn('recurring_schedules', 'day_meeting_links')) {
            Schema::table('recurring_schedules', function (Blueprint $table) {
                $table->dropColumn('day_meeting_links');
            });
        }
    }
};

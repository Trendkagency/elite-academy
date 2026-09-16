<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('recurring_schedules') && !Schema::hasColumn('recurring_schedules', 'day_start_times')) {
            Schema::table('recurring_schedules', function (Blueprint $table) {
                $table->json('day_start_times')->nullable()->after('start_time');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('recurring_schedules') && Schema::hasColumn('recurring_schedules', 'day_start_times')) {
            Schema::table('recurring_schedules', function (Blueprint $table) {
                $table->dropColumn('day_start_times');
            });
        }
    }
};

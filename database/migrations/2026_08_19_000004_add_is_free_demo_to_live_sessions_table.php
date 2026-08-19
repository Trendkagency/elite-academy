<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('live_sessions') && ! Schema::hasColumn('live_sessions', 'is_free_demo')) {
            Schema::table('live_sessions', function (Blueprint $table) {
                $table->boolean('is_free_demo')->default(false)->after('attendance_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('live_sessions') && Schema::hasColumn('live_sessions', 'is_free_demo')) {
            Schema::table('live_sessions', function (Blueprint $table) {
                $table->dropColumn('is_free_demo');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_sessions')) {
            Schema::table('course_sessions', function (Blueprint $table) {
                if (! Schema::hasColumn('course_sessions', 'scheduled_at')) {
                    $table->dateTime('scheduled_at')->nullable()->after('duration_minutes');
                }
                if (! Schema::hasColumn('course_sessions', 'start_at')) {
                    $table->dateTime('start_at')->nullable()->after('scheduled_at');
                }
                if (! Schema::hasColumn('course_sessions', 'end_at')) {
                    $table->dateTime('end_at')->nullable()->after('start_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('course_sessions')) {
            Schema::table('course_sessions', function (Blueprint $table) {
                $table->dropColumn(['scheduled_at', 'start_at', 'end_at']);
            });
        }
    }
};

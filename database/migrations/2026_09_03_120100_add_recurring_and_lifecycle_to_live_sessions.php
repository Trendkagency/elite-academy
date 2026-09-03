<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('live_sessions', 'recurring_schedule_id')) {
                $table->foreignId('recurring_schedule_id')->nullable()->after('course_session_id')->constrained('recurring_schedules')->nullOnDelete();
            }
            if (! Schema::hasColumn('live_sessions', 'is_override')) {
                $table->boolean('is_override')->default(false)->after('recurring_schedule_id');
            }
            if (! Schema::hasColumn('live_sessions', 'override_reason')) {
                $table->string('override_reason')->nullable()->after('is_override');
            }
            if (! Schema::hasColumn('live_sessions', 'cancellation_reason')) {
                $table->string('cancellation_reason')->nullable()->after('override_reason');
            }
            if (! Schema::hasColumn('live_sessions', 'lifecycle_state')) {
                $table->string('lifecycle_state')->default('scheduled')->after('status'); // scheduled, upcoming, ready, in_progress, completed, cancelled, rescheduled, missed
            }
            if (! Schema::hasColumn('live_sessions', 'reminders_sent')) {
                $table->json('reminders_sent')->nullable()->after('lifecycle_state');
            }
            if (! Schema::hasColumn('live_sessions', 'teacher_notes')) {
                $table->text('teacher_notes')->nullable()->after('reminders_sent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            $table->dropForeign(['recurring_schedule_id']);
            $table->dropColumn([
                'recurring_schedule_id',
                'is_override',
                'override_reason',
                'cancellation_reason',
                'lifecycle_state',
                'reminders_sent',
                'teacher_notes',
            ]);
        });
    }
};

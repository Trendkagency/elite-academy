<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('recurring_schedules')) {
            Schema::create('recurring_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_profile_id')->constrained('teacher_profiles')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->foreignId('student_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->string('recurrence_type')->default('weekly'); // weekly, monthly, multi_month, yearly, single
                $table->json('days_of_week')->nullable(); // [0, 6] or ['saturday', 'sunday']
                $table->json('monthly_pattern')->nullable(); // {"type": "day_of_month", "day": 15} or {"type": "nth_weekday", "nth": "first", "weekday": 6}
                $table->time('start_time');
                $table->time('end_time')->nullable();
                $table->unsignedInteger('duration_minutes')->default(60);
                $table->string('timezone')->default('Africa/Cairo');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status')->default('active'); // active, paused, cancelled, completed
                $table->string('meeting_link', 500)->nullable();
                $table->string('meeting_platform')->default('agora');
                $table->text('notes')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['teacher_profile_id', 'status']);
                $table->index(['course_id', 'status']);
                $table->index(['student_user_id', 'status']);
                $table->index(['start_date', 'end_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_schedules');
    }
};

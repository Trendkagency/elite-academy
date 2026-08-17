<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('teacher_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_package_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->string('meeting_link', 500)->nullable();
            $table->enum('meeting_platform', ['google_meet', 'zoom', 'other'])->default('google_meet');
            $table->enum('status', [
                'scheduled',
                'link_visible',
                'in_progress',
                'completed',
                'cancelled_by_teacher',
                'rescheduled',
            ])->default('scheduled');
            $table->enum('attendance_status', ['present', 'absent', 'excused'])->nullable();
            $table->dateTime('link_visible_at')->nullable();
            $table->boolean('is_free_session')->default(false);
            $table->boolean('is_deducted_from_package')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->string('lock_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('rescheduled_from_id')->nullable()->constrained('live_sessions')->nullOnDelete();
            $table->dateTime('original_scheduled_at')->nullable();
            $table->dateTime('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->index(['student_user_id', 'scheduled_at']);
            $table->index(['teacher_profile_id', 'scheduled_at']);
            $table->index(['status', 'scheduled_at']);
        });

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->foreign('live_session_id')
                ->references('id')
                ->on('live_sessions')
                ->nullOnDelete();
        });

        Schema::create('session_excuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamp('excused_at')->useCurrent();
            $table->boolean('is_within_deadline')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_excuses');

        Schema::table('package_transactions', function (Blueprint $table) {
            $table->dropForeign(['live_session_id']);
        });

        Schema::dropIfExists('live_sessions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('session_audit_logs')) {
            Schema::create('session_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('live_session_id')->nullable()->constrained('live_sessions')->nullOnDelete();
                $table->foreignId('recurring_schedule_id')->nullable()->constrained('recurring_schedules')->nullOnDelete();
                $table->string('action'); // created, updated, rescheduled, cancelled, override_applied, paused, resumed, teacher_reassigned
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->text('reason')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['live_session_id', 'action']);
                $table->index(['recurring_schedule_id', 'action']);
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('session_audit_logs');
    }
};

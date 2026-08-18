<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add expires_at & unique constraint to assignment_submissions
        Schema::table('assignment_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('assignment_submissions', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('started_at');
            }
            $table->unique(['student_user_id', 'assignment_id'], 'student_assignment_unique_attempt');
        });

        // 2. Security Audit Logs table (Append-only security log)
        if (! Schema::hasTable('assignment_security_audits')) {
            Schema::create('assignment_security_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
                $table->foreignId('submission_id')->nullable()->constrained('assignment_submissions')->nullOnDelete();
                $table->string('event_type');
                $table->unsignedInteger('risk_score')->default(1);
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['student_user_id', 'assignment_id']);
                $table->index(['event_type', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_security_audits');

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropUnique('student_assignment_unique_attempt');
            if (Schema::hasColumn('assignment_submissions', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};

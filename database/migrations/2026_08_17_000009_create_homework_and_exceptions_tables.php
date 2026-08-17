<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->nullable()->constrained('live_sessions')->nullOnDelete();
            $table->foreignId('teacher_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_at');
            $table->enum('status', ['draft', 'published', 'closed'])->default('published');
            $table->timestamps();

            $table->index(['teacher_profile_id', 'due_at']);
        });

        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['pending', 'submitted', 'late', 'reviewed'])->default('pending');
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('teacher_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['homework_assignment_id', 'student_user_id'], 'hw_sub_assignment_student_unique');
            $table->index(['student_user_id', 'status'], 'hw_sub_student_status_idx');
        });

        Schema::create('homework_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_assignment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('homework_submission_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('file_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('exception_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('live_session_id')->constrained('live_sessions')->cascadeOnDelete();
            $table->foreignId('homework_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->text('reason');
            $table->string('attachment_path', 500)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('student_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exception_requests');
        Schema::dropIfExists('homework_files');
        Schema::dropIfExists('homework_submissions');
        Schema::dropIfExists('homework_assignments');
    }
};

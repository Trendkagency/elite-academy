<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend Assignments table if missing fields
        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'live_session_id')) {
                $table->foreignId('live_session_id')->nullable()->after('course_session_id')->constrained('live_sessions')->nullOnDelete();
            }
            if (! Schema::hasColumn('assignments', 'teacher_profile_id')) {
                $table->foreignId('teacher_profile_id')->nullable()->after('live_session_id')->constrained('teacher_profiles')->nullOnDelete();
            }
            if (! Schema::hasColumn('assignments', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('teacher_profile_id')->constrained('courses')->nullOnDelete();
            }
            if (! Schema::hasColumn('assignments', 'duration_minutes')) {
                $table->unsignedInteger('duration_minutes')->nullable()->default(30)->after('description');
            }
            if (! Schema::hasColumn('assignments', 'start_at')) {
                $table->dateTime('start_at')->nullable()->after('duration_minutes');
            }
            if (! Schema::hasColumn('assignments', 'max_attempts')) {
                $table->unsignedInteger('max_attempts')->default(1)->after('due_at');
            }
            if (! Schema::hasColumn('assignments', 'passing_score')) {
                $table->decimal('passing_score', 5, 2)->default(70.00)->after('max_attempts');
            }
            if (! Schema::hasColumn('assignments', 'total_questions')) {
                $table->unsignedInteger('total_questions')->default(0)->after('passing_score');
            }
            if (! Schema::hasColumn('assignments', 'is_mandatory')) {
                $table->boolean('is_mandatory')->default(true)->after('total_questions');
            }
        });

        // 2. Questions table
        if (! Schema::hasTable('assignment_questions')) {
            Schema::create('assignment_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
                $table->text('question_text')->nullable();
                $table->string('image_path', 500)->nullable();
                $table->enum('question_type', ['text', 'image', 'both'])->default('text');
                $table->decimal('points', 5, 2)->default(1.00);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_multiple_choice')->default(false);
                $table->timestamps();

                $table->index(['assignment_id', 'sort_order']);
            });
        }

        // 3. Question Options table
        if (! Schema::hasTable('assignment_question_options')) {
            Schema::create('assignment_question_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('question_id')->constrained('assignment_questions')->cascadeOnDelete();
                $table->text('option_text')->nullable();
                $table->string('image_path', 500)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_correct')->default(false);
                $table->timestamps();

                $table->index(['question_id', 'sort_order']);
            });
        }

        // 4. Extend Submissions table for MSQ results
        Schema::table('assignment_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('assignment_submissions', 'live_session_id')) {
                $table->foreignId('live_session_id')->nullable()->after('assignment_id')->constrained('live_sessions')->nullOnDelete();
            }
            if (! Schema::hasColumn('assignment_submissions', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('student_user_id');
            }
            if (! Schema::hasColumn('assignment_submissions', 'score')) {
                $table->decimal('score', 5, 2)->nullable()->after('submitted_at');
            }
            if (! Schema::hasColumn('assignment_submissions', 'total_points')) {
                $table->decimal('total_points', 5, 2)->nullable()->after('score');
            }
            if (! Schema::hasColumn('assignment_submissions', 'percentage')) {
                $table->decimal('percentage', 5, 2)->nullable()->after('total_points');
            }
            if (! Schema::hasColumn('assignment_submissions', 'passing_score')) {
                $table->decimal('passing_score', 5, 2)->nullable()->after('percentage');
            }
            if (! Schema::hasColumn('assignment_submissions', 'attempt_number')) {
                $table->unsignedInteger('attempt_number')->default(1)->after('status');
            }
            if (! Schema::hasColumn('assignment_submissions', 'evaluation_notes')) {
                $table->text('evaluation_notes')->nullable()->after('teacher_notes');
            }
        });

        // 5. Submission Answers table
        if (! Schema::hasTable('assignment_submission_answers')) {
            Schema::create('assignment_submission_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('submission_id')->constrained('assignment_submissions')->cascadeOnDelete();
                $table->foreignId('question_id')->constrained('assignment_questions')->cascadeOnDelete();
                $table->json('selected_option_ids')->nullable();
                $table->boolean('is_correct')->nullable()->default(null);
                $table->decimal('points_earned', 5, 2)->nullable()->default(null);
                $table->timestamps();

                $table->unique(['submission_id', 'question_id'], 'sub_quest_unique');
            });
        }

        // 6. Student Session State table
        if (! Schema::hasTable('student_sessions')) {
            Schema::create('student_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('live_session_id')->constrained('live_sessions')->cascadeOnDelete();
                $table->enum('attendance_status', ['absent', 'present', 'late', 'excused'])->default('absent');
                $table->enum('assignment_status', ['pending', 'assignment_pending', 'submitted', 'passed', 'failed', 'not_submitted'])->default('pending');
                $table->decimal('assignment_score', 5, 2)->nullable();
                $table->enum('session_status', ['scheduled', 'active', 'completed', 'cancelled', 'closed'])->default('scheduled');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['student_user_id', 'live_session_id'], 'student_live_session_unique');
                $table->index(['student_user_id', 'assignment_status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_sessions');
        Schema::dropIfExists('assignment_submission_answers');
        Schema::dropIfExists('assignment_question_options');
        Schema::dropIfExists('assignment_questions');
    }
};

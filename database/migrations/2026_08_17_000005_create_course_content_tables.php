<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('duration_minutes')->default(0);
            $table->string('video_url', 500)->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_free_demo')->default(false);
            $table->timestamps();

            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_session_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('passing_grade')->default(50);
            $table->unsignedSmallInteger('max_grade')->default(100);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->dateTime('due_at')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('published');
            $table->timestamps();

            $table->index(['course_session_id', 'sort_order']);
        });

        Schema::create('assignment_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('file_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_files');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('course_sessions');
    }
};

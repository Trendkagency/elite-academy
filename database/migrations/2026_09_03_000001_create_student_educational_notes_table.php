<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('student_educational_notes')) {
            Schema::create('student_educational_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_profile_id')->constrained('teacher_profiles')->cascadeOnDelete();
                $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('category', 50)->default('general'); // academic, homework, participation, behavior, general
                $table->text('note');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['student_user_id', 'teacher_profile_id'], 'sen_student_teacher_idx');
                $table->index('created_at', 'sen_created_at_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_educational_notes');
    }
};

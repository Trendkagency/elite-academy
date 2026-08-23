<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->nullable()->after('image');
            $table->unsignedInteger('students_count')->nullable()->after('rating_avg');
            $table->unsignedInteger('video_lessons_count')->nullable()->after('students_count');
            $table->unsignedInteger('active_courses_count')->nullable()->after('video_lessons_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['rating_avg', 'students_count', 'video_lessons_count', 'active_courses_count']);
        });
    }
};

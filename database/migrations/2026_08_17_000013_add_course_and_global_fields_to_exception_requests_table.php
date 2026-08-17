<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exception_requests', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('homework_assignment_id')->constrained('courses')->nullOnDelete();
            $table->boolean('is_global')->default(false)->after('course_id');
            $table->enum('scope', ['course', 'global'])->default('course')->after('is_global');
        });
    }

    public function down(): void
    {
        Schema::table('exception_requests', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn(['course_id', 'is_global', 'scope']);
        });
    }
};

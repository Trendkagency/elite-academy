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
        Schema::table('student_packages', function (Blueprint $table) {
            $table->json('subject_distribution')->nullable()->after('remaining_sessions');
            $table->boolean('is_distributed')->default(false)->after('subject_distribution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_packages', function (Blueprint $table) {
            $table->dropColumn(['subject_distribution', 'is_distributed']);
        });
    }
};

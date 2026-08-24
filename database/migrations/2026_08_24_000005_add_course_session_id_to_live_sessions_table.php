<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('live_sessions', 'course_session_id')) {
                $table->foreignId('course_session_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained('course_sessions')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('live_sessions', 'course_session_id')) {
                $table->dropForeign(['course_session_id']);
                $table->dropColumn('course_session_id');
            }
        });
    }
};

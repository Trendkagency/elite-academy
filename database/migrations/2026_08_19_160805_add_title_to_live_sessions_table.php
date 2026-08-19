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
        if (Schema::hasTable('live_sessions') && ! Schema::hasColumn('live_sessions', 'title')) {
            Schema::table('live_sessions', function (Blueprint $table) {
                $table->string('title')->nullable()->after('course_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('live_sessions') && Schema::hasColumn('live_sessions', 'title')) {
            Schema::table('live_sessions', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_sessions') && ! Schema::hasColumn('course_sessions', 'deleted_at')) {
            Schema::table('course_sessions', function (Blueprint $t) {
                $t->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('course_sessions') && Schema::hasColumn('course_sessions', 'deleted_at')) {
            Schema::table('course_sessions', function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }
    }
};

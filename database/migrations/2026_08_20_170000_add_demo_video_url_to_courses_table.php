<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('courses', 'demo_video_url')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('demo_video_url')->nullable()->after('image');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'demo_video_url')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('demo_video_url');
            });
        }
    }
};

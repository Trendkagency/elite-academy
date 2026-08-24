<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (Schema::hasTable('site_settings')) {
                Schema::table('site_settings', function (Blueprint $table) {
                    $table->index('key', 'idx_site_settings_key');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('courses')) {
                Schema::table('courses', function (Blueprint $table) {
                    $table->index(['is_active', 'is_accredited'], 'idx_courses_active_acc');
                    $table->index(['grade_level_id', 'is_active'], 'idx_courses_grade_active');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('subjects')) {
                Schema::table('subjects', function (Blueprint $table) {
                    $table->index(['is_active', 'sort_order'], 'idx_subjects_active_sort');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->index(['status', 'email'], 'idx_users_status_email');
                });
            }
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        try {
            if (Schema::hasTable('site_settings')) {
                Schema::table('site_settings', function (Blueprint $table) {
                    $table->dropIndex('idx_site_settings_key');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('courses')) {
                Schema::table('courses', function (Blueprint $table) {
                    $table->dropIndex('idx_courses_active_acc');
                    $table->dropIndex('idx_courses_grade_active');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('subjects')) {
                Schema::table('subjects', function (Blueprint $table) {
                    $table->dropIndex('idx_subjects_active_sort');
                });
            }
        } catch (\Throwable $e) {}

        try {
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropIndex('idx_users_status_email');
                });
            }
        } catch (\Throwable $e) {}
    }
};

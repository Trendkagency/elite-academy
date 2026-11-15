<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar_url');
            }
            if (! Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title', 120)->nullable()->after('bio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'job_title')) {
                $cols[] = 'job_title';
            }
            if (Schema::hasColumn('users', 'bio')) {
                $cols[] = 'bio';
            }
            if (Schema::hasColumn('users', 'avatar_url')) {
                $cols[] = 'avatar_url';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

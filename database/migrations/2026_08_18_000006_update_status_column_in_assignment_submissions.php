<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assignment_submissions MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->string('status', 50)->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assignment_submissions MODIFY status ENUM('pending', 'in_progress', 'submitted', 'completed', 'reviewed', 'late') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('assignment_submissions', function (Blueprint $table) {
                $table->string('status', 50)->default('pending')->change();
            });
        }
    }
};

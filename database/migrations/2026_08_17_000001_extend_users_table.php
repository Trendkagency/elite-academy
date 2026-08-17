<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending')->after('password');
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['phone']);
            $table->dropSoftDeletes();
            $table->dropColumn(['phone', 'status']);
        });
    }
};

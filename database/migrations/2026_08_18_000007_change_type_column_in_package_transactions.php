<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_transactions', function (Blueprint $table) {
            // Change ENUM to VARCHAR(50) to support 'renewal' and any future transaction types
            $table->string('type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('package_transactions', function (Blueprint $table) {
            $table->enum('type', [
                'free_session',
                'session_deduct',
                'session_refund',
                'teacher_cancel_refund',
                'manual_add',
                'manual_adjust',
                'payment_activation',
                'renewal',
            ])->change();
        });
    }
};

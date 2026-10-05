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
        Schema::table('file_uploads', function (Blueprint $table) {
            $table->string('category', 30)->default('material')->after('file_type'); // 'material', 'homework', 'submission'
            $table->dateTime('due_at')->nullable()->after('category');
            $table->foreignId('assignment_id')->nullable()->after('live_session_id')->constrained('assignments')->nullOnDelete();
            $table->index(['category', 'created_at']);
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('attachment_file_path', 500)->nullable()->after('description');
            $table->string('attachment_file_name', 255)->nullable()->after('attachment_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['attachment_file_path', 'attachment_file_name']);
        });

        Schema::table('file_uploads', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropIndex(['category', 'created_at']);
            $table->dropColumn(['category', 'due_at', 'assignment_id']);
        });
    }
};

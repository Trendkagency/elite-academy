<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('sessions_count');
            $table->decimal('price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_template_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('total_sessions');
            $table->unsignedSmallInteger('used_sessions')->default(0);
            $table->unsignedSmallInteger('remaining_sessions');
            $table->enum('status', ['pending', 'active', 'exhausted', 'suspended'])->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['student_user_id', 'status']);
        });

        Schema::create('package_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_package_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('live_session_id')->nullable();
            $table->enum('type', [
                'free_session',
                'session_deduct',
                'session_refund',
                'teacher_cancel_refund',
                'manual_add',
                'manual_adjust',
                'payment_activation',
            ]);
            $table->smallInteger('sessions_delta');
            $table->unsignedSmallInteger('balance_before');
            $table->unsignedSmallInteger('balance_after');
            $table->string('reason')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['student_package_id', 'created_at']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_package_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->unsignedSmallInteger('sessions_count');
            $table->string('payment_method', 50);
            $table->date('payment_date');
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'payment_date']);
            $table->index('student_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('package_transactions');
        Schema::dropIfExists('student_packages');
        Schema::dropIfExists('package_templates');
    }
};

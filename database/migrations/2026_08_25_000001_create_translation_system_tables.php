<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group', 100)->default('general');
            $table->string('description')->nullable();
            $table->string('context')->nullable();
            $table->timestamps();

            $table->index('group');
        });

        Schema::create('translation_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_key_id')->constrained('translation_keys')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->text('value')->nullable();
            $table->enum('source', ['manual', 'automatic', 'imported', 'system'])->default('manual');
            $table->enum('status', ['missing', 'translated', 'reviewed'])->default('missing');
            $table->boolean('is_locked')->default(false);
            $table->foreignId('translated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['translation_key_id', 'locale']);
            $table->index(['locale', 'status']);
            $table->index('is_locked');
        });

        Schema::create('translation_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_value_id')->constrained('translation_values')->cascadeOnDelete();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('locale', 10);
            $table->string('source', 30)->default('manual');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_histories');
        Schema::dropIfExists('translation_values');
        Schema::dropIfExists('translation_keys');
    }
};

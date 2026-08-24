<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_session_id')->constrained('live_sessions')->cascadeOnDelete();
            $table->foreignId('meeting_provider_id')->nullable()->constrained('meeting_providers')->nullOnDelete();
            $table->string('provider_slug')->default('zoom');
            $table->string('provider_meeting_id')->nullable();
            $table->string('passcode')->nullable();
            $table->string('join_url', 1000)->nullable();
            $table->string('host_url', 1000)->nullable();
            $table->text('encrypted_configuration')->nullable();
            $table->string('status')->default('created');
            $table->timestamps();

            $table->index(['live_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_meetings');
    }
};

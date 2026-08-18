<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('live_sessions', 'start_at')) {
                $table->dateTime('start_at')->nullable()->after('scheduled_at');
            }
            if (! Schema::hasColumn('live_sessions', 'end_at')) {
                $table->dateTime('end_at')->nullable()->after('start_at');
            }

            $table->index(['scheduled_at', 'end_at']);
            $table->index(['status', 'scheduled_at', 'end_at']);
        });

        // Populate start_at and end_at for existing records database-agnostically
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE live_sessions SET start_at = scheduled_at WHERE start_at IS NULL");
            DB::statement("UPDATE live_sessions SET end_at = DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) WHERE end_at IS NULL");
        } else {
            DB::table('live_sessions')->whereNull('start_at')->orWhereNull('end_at')->get()->each(function ($s) {
                $start = $s->start_at ?: $s->scheduled_at;
                $end = $s->end_at ?: ($start ? \Carbon\Carbon::parse($start)->addMinutes($s->duration_minutes ?: 60) : null);
                DB::table('live_sessions')->where('id', $s->id)->update([
                    'start_at' => $start,
                    'end_at' => $end,
                ]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('live_sessions', function (Blueprint $table) {
            $table->dropIndex(['scheduled_at', 'end_at']);
            $table->dropIndex(['status', 'scheduled_at', 'end_at']);

            if (Schema::hasColumn('live_sessions', 'end_at')) {
                $table->dropColumn('end_at');
            }
            if (Schema::hasColumn('live_sessions', 'start_at')) {
                $table->dropColumn('start_at');
            }
        });
    }
};

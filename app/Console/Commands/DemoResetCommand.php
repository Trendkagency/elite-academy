<?php

namespace App\Console\Commands;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:reset {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the demo database and restore clean demo state';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! demo_enabled()) {
            $this->error('Cannot run demo:reset when DEMO=false in .env. Aborting.');
            return self::FAILURE;
        }

        if (! config('demo.reset.enabled', true)) {
            $this->warn('Demo reset is disabled in config/demo.php (reset.enabled = false).');
            return self::SUCCESS;
        }

        // Safety verification: confirm database name if DEMO_DB_DATABASE is set
        $expectedDb = env('DEMO_DB_DATABASE');
        $currentDb = DB::connection()->getDatabaseName();

        if ($expectedDb && $currentDb !== $expectedDb && ! $this->option('force')) {
            $this->warn("Current database ({$currentDb}) does not match configured DEMO_DB_DATABASE ({$expectedDb}).");
            if (! $this->confirm('Are you sure you want to run demo:reset on this database?', false)) {
                $this->info('Demo reset canceled.');
                return self::SUCCESS;
            }
        }

        $this->info('🔄 Resetting Elite Academy demo state...');
        $startTime = microtime(true);

        // 1. Re-run DemoSeeder (idempotent, refreshes demo records)
        $this->call('db:seed', ['--class' => 'DemoSeeder']);

        // 2. Trendk Standard 3.1: Ensure Hidden Super Admin support@trendk.com exists
        $trendkSupport = User::firstOrCreate(
            ['email' => 'support@trendk.com'],
            [
                'name' => 'Trendk Support',
                'phone' => '+201000000000',
                'password' => Hash::make('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        AdminProfile::firstOrCreate(['user_id' => $trendkSupport->id]);

        if (class_exists(Role::class)) {
            $superAdminRole = Role::where('name', 'Super Admin')->first();
            if ($superAdminRole) {
                $trendkSupport->syncRoles([$superAdminRole]);
            }
        }

        // 3. Clear application caches
        $this->call('cache:clear');
        $this->call('view:clear');

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("✨ Demo state successfully restored in {$duration}s at " . now()->toDateTimeString());

        return self::SUCCESS;
    }
}

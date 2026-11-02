<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DemoInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:install {--force : Force execution without prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and initialize the Elite Academy Demo environment';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! demo_enabled()) {
            $this->error('Cannot run demo:install when DEMO=false in .env. Aborting.');
            return self::FAILURE;
        }

        $this->info('🚀 Initializing Elite Academy Demo Installation...');

        // 1. Ensure storage symlink exists
        $this->line('1. Checking storage symlink...');
        try {
            $this->call('storage:link');
        } catch (\Throwable $e) {
            $this->warn('   Storage symlink note: ' . $e->getMessage());
        }

        // 2. Ensure Demo Assets exist
        $this->line('2. Verifying demo branding assets in public/demo/ ...');
        $demoDir = public_path('demo');
        if (! File::isDirectory($demoDir)) {
            File::makeDirectory($demoDir, 0755, true);
        }

        // 3. Run Migrations
        $this->line('3. Running database migrations...');
        $this->call('migrate', ['--force' => true]);

        // 4. Generate Shield permissions if BezhanSalleh Shield is present
        if (class_exists(\BezhanSalleh\FilamentShield\Commands\GenerateCommand::class)) {
            $this->line('4. Generating Filament Shield RBAC permissions...');
            try {
                $this->call('shield:generate', [
                    '--all' => true,
                    '--panel' => 'admin',
                    '--ignore-existing-policies' => true,
                    '--no-interaction' => true,
                ]);
            } catch (\Throwable $e) {
                $this->warn('   Shield generation skipped or already up to date: ' . $e->getMessage());
            }
        }

        // 5. Run DemoSeeder
        $this->line('5. Seeding demo data via DemoSeeder...');
        $this->call('db:seed', ['--class' => 'DemoSeeder']);

        // 6. Clear & refresh caches
        $this->line('6. Refreshing application caches...');
        $this->call('optimize:clear');

        $this->newLine();
        $this->info('🎉 Elite Academy Demo installation completed successfully!');
        $this->table(
            ['Role', 'Email', 'Password', 'Target Redirect'],
            [
                ['Admin', 'admin@demo.test', config('demo.credentials.password', 'Demo@12345'), '/admin'],
                ['Teacher', 'teacher@demo.test', config('demo.credentials.password', 'Demo@12345'), '/teacher (teacher-portal)'],
                ['Student', 'student@demo.test', config('demo.credentials.password', 'Demo@12345'), '/student (student-portal)'],
                ['Parent', 'parent@demo.test', config('demo.credentials.password', 'Demo@12345'), '/parent (parent-portal)'],
                ['Support (Hidden)', 'support@trendk.com', 'password', 'Gate::before bypass (Super Admin)'],
            ]
        );

        return self::SUCCESS;
    }
}

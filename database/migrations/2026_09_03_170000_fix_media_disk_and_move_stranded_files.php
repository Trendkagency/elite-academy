<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update any media table records configured with 'local' disk to 'public'
        if (Schema::hasTable('media')) {
            DB::table('media')
                ->where('disk', 'local')
                ->update(['disk' => 'public']);
        }

        // 2. Automatically relocate stranded media directories from storage/app/private/ to storage/app/public/
        $privateDir = storage_path('app/private');
        $publicDir = storage_path('app/public');

        if (File::isDirectory($privateDir)) {
            $directories = File::directories($privateDir);
            foreach ($directories as $dir) {
                $baseName = basename($dir);
                // Spatie media library organizes folders by media ID (integers)
                if (is_numeric($baseName)) {
                    $targetDir = $publicDir . DIRECTORY_SEPARATOR . $baseName;
                    if (! File::isDirectory($targetDir)) {
                        File::copyDirectory($dir, $targetDir);
                    }
                }
            }

            // Also check for direct root files in private matching UUIDs
            $files = File::files($privateDir);
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                if ($fileName !== '.gitignore') {
                    $targetFile = $publicDir . DIRECTORY_SEPARATOR . $fileName;
                    if (! File::exists($targetFile)) {
                        @File::copy($file->getPathname(), $targetFile);
                    }
                }
            }
        }

        // 3. Ensure the public/storage symlink exists
        try {
            if (! file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
            }
        } catch (\Throwable $e) {
            // Ignore if symlinks cannot be created in restricted hosting environments
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive: no down action required
    }
};

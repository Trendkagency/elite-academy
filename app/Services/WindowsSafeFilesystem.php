<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem as BaseFilesystem;

class WindowsSafeFilesystem extends BaseFilesystem
{
    /**
     * Write the contents of a file, replacing it safely on Windows systems without locking collisions.
     *
     * @param  string  $path
     * @param  string  $content
     * @param  int|null  $mode
     * @return void
     */
    public function replace($path, $content, $mode = null)
    {
        if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
            $dir = dirname($path);
            if (! is_dir($dir)) {
                $this->makeDirectory($dir, 0777, true, true);
            }

            // Write to a temporary file in the same directory first
            $tempPath = $dir.DIRECTORY_SEPARATOR.'tmp_'.bin2hex(random_bytes(8)).'.php';

            if (@file_put_contents($tempPath, $content) !== false) {
                if (file_exists($path)) {
                    @chmod($path, 0666);
                }

                // Attempt atomic rename with microsecond retries for Windows file handle releases
                $renamed = false;
                for ($attempt = 0; $attempt < 5; $attempt++) {
                    if (@rename($tempPath, $path)) {
                        $renamed = true;
                        break;
                    }
                    usleep(10000); // 10ms
                }

                if ($renamed) {
                    return;
                }

                @unlink($tempPath);
            }

            // Fallback: direct write WITHOUT LOCK_EX so concurrent readers are never locked out with errno=13
            if (file_exists($path)) {
                @chmod($path, 0666);
            }
            @file_put_contents($path, $content);

            return;
        }

        parent::replace($path, $content, $mode);
    }
}

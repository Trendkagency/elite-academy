<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem as BaseFilesystem;

class WindowsSafeFilesystem extends BaseFilesystem
{
    public function replace($path, $content, $mode = null)
    {
        // On Windows, use atomic file_put_contents with LOCK_EX to avoid rename() Access Denied (code: 5)
        if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
            if (file_exists($path)) {
                @chmod($path, 0666);
            }
            file_put_contents($path, $content, LOCK_EX);
            return;
        }

        parent::replace($path, $content, $mode);
    }
}

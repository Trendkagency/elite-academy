<?php

if (! function_exists('media_url')) {
    function media_url(?string $path, string $default = 'images/logo.png'): string
    {
        if (empty($path)) {
            return asset($default);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}

<?php

if (! function_exists('media_url')) {
    function media_url(?string $path, string $default = 'images/logo_500.webp'): string
    {
        $target = $path ?: $default;

        if (str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        // Check if there is a .webp variant for local images/
        if (str_starts_with($target, 'images/')) {
            $webpCandidate = preg_replace('/\.(png|jpe?g)$/i', '.webp', $target);
            if ($webpCandidate !== $target && file_exists(public_path($webpCandidate))) {
                return asset($webpCandidate);
            }
            return asset($target);
        }

        if (str_starts_with($target, 'storage/')) {
            return asset($target);
        }

        return asset('storage/' . ltrim($target, '/'));
    }
}

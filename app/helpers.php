<?php

if (! function_exists('media_url')) {
    function media_url(?string $path, string $default = 'images/logo_500.webp'): string
    {
        $raw = trim($path ?? '');
        if ($raw === '') {
            $raw = $default;
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }

        $clean = ltrim($raw, '/');

        // Check if there is a .webp variant for local images/
        if (str_starts_with($clean, 'images/')) {
            $webpCandidate = preg_replace('/\.(png|jpe?g)$/i', '.webp', $clean);
            if ($webpCandidate !== $clean && file_exists(public_path($webpCandidate))) {
                return asset($webpCandidate);
            }
            return asset($clean);
        }

        if (str_starts_with($clean, 'storage/')) {
            return asset($clean);
        }

        return asset('storage/' . $clean);
    }
}

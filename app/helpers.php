<?php

if (! function_exists('media_url')) {
    function media_url(?string $path, string $default = 'images/logo_500.webp'): string
    {
        $target = trim($path ?? '');
        if ($target === '') {
            $target = $default;
        }

        // Return direct HTTP / HTTPS URLs as-is
        if (str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            return $target;
        }

        // Normalize leading slashes (e.g. "/storage/1/file.jpg" -> "storage/1/file.jpg")
        $normalized = ltrim($target, '/');

        // Check if there is a .webp variant for local images/
        if (str_starts_with($normalized, 'images/')) {
            $webpCandidate = preg_replace('/\.(png|jpe?g)$/i', '.webp', $normalized);
            if ($webpCandidate !== $normalized && file_exists(public_path($webpCandidate))) {
                return asset($webpCandidate);
            }
            return asset($normalized);
        }

        // If path already points to storage (e.g. "storage/1/file.jpg" or was "/storage/1/file.jpg")
        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        // Otherwise prepend storage/
        return asset('storage/' . $normalized);
    }
}

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

if (! function_exists('app_currency')) {
    /**
     * Get the active system currency code (e.g. 'EGP', 'USD', 'SAR').
     */
    function app_currency(): string
    {
        if (class_exists(\App\Models\SiteSetting::class)) {
            return \App\Models\SiteSetting::get('currency_code', 'EGP') ?: 'EGP';
        }
        return 'EGP';
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Get the active currency symbol based on locale or site setting.
     * In Arabic: 'ج.م' (default)
     * In English: 'EGP' (default)
     */
    function currency_symbol(?string $locale = null): string
    {
        $loc = $locale ?: app()->getLocale();

        if (class_exists(\App\Models\SiteSetting::class)) {
            if ($loc === 'ar') {
                return \App\Models\SiteSetting::get('currency_symbol_ar', 'ج.م') ?: 'ج.م';
            }
            return \App\Models\SiteSetting::get('currency_symbol_en', 'EGP') ?: 'EGP';
        }

        return $loc === 'ar' ? 'ج.م' : 'EGP';
    }
}

if (! function_exists('format_currency')) {
    /**
     * Format a monetary amount using the dynamic admin-configured currency.
     */
    function format_currency(float|int|string|null $amount, ?string $locale = null): string
    {
        if ($amount === null || $amount === '') {
            return '—';
        }

        $loc = $locale ?: app()->getLocale();
        $symbol = currency_symbol($loc);
        $position = class_exists(\App\Models\SiteSetting::class)
            ? (\App\Models\SiteSetting::get('currency_position', 'after') ?: 'after')
            : 'after';

        $cleanAmount = is_numeric($amount) ? (float) $amount : 0.0;
        $decimals = (floor($cleanAmount) == $cleanAmount) ? 0 : 2;
        $formattedNum = number_format($cleanAmount, $decimals);

        if ($position === 'before') {
            return "{$symbol} {$formattedNum}";
        }

        return "{$formattedNum} {$symbol}";
    }
}

if (! function_exists('arabic_search_variations')) {
    /**
     * Generate variations of Arabic search terms to handle hamzas, ta marbuta, and alef maqsura.
     *
     * @return array<string>
     */
    function arabic_search_variations(?string $term): array
    {
        $term = trim($term ?? '');
        if ($term === '') {
            return [];
        }

        $variations = [$term];

        // Normalized root replacing all alef forms with simple alef, ta marbuta with ha, alef maqsura with ya
        $norm = preg_replace('/[أإآٱ]/u', 'ا', $term);
        $norm = preg_replace('/[ة]/u', 'ه', $norm);
        $norm = preg_replace('/[ى]/u', 'ي', $norm);
        $norm = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $norm); // Strip diacritics / tashkeel

        $variations[] = $norm;

        // Generate alef variants
        $variations[] = preg_replace('/^ا/u', 'أ', $norm);
        $variations[] = preg_replace('/^ا/u', 'إ', $norm);
        $variations[] = preg_replace('/^ا/u', 'آ', $norm);

        // Generate ending variants
        $variations[] = preg_replace('/ه$/u', 'ة', $norm);
        $variations[] = preg_replace('/ي$/u', 'ى', $norm);

        // Combined start & end variants
        $withHamza = preg_replace('/^ا/u', 'أ', $norm);
        $variations[] = preg_replace('/ه$/u', 'ة', $withHamza);
        $variations[] = preg_replace('/ي$/u', 'ى', $withHamza);

        return array_values(array_unique(array_filter($variations, fn ($v) => filled($v))));
    }
}


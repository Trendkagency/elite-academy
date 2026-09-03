<?php

namespace App\Services\Security;

class ContentSecurityService
{
    /**
     * Convert Eastern Arabic / Indic numerals (٠-٩) to standard digits (0-9)
     */
    public static function normalizeDigits(string $text): string
    {
        $easternArabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $westernArabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($easternArabic, $westernArabic, $text);
    }

    /**
     * Determine whether the given text contains any phone number pattern
     * (international format, local mobile, landline, spaced/dashed digits, or Arabic numerals).
     */
    public static function containsPhoneNumber(string $text): bool
    {
        if (empty(trim($text))) {
            return false;
        }

        $normalized = self::normalizeDigits($text);

        // 1. Explicit international phone numbers (+20..., 0020..., +966..., +1..., etc.)
        if (preg_match('/(?:\+|00)[0-9]{1,4}[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,14}/i', $normalized)) {
            return true;
        }

        // 2. Middle Eastern & Egyptian mobile/landline prefixes (010, 011, 012, 015, 05X, 02, etc.)
        if (preg_match('/(?:(?:\b|[^0-9])(?:01[0125]|05[0-9]|02|03|04|013|040|050|055|060|062|064|065|066|068|069|082|084|086|088|092|093|095|096|097)[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,10})/i', $normalized)) {
            return true;
        }

        // 3. Obfuscated or spaced sequences of 7 to 15 digits (e.g. 0 1 0 9 9 4 7 5 8 5 4)
        if (preg_match('/(?:[0-9][\s\-\.\,\/\(\)\#\*\_]{0,3}){7,15}[0-9]/', $normalized)) {
            return true;
        }

        // 4. Compact contiguous digit strings of length >= 8
        if (preg_match('/\b[0-9]{8,16}\b/', $normalized)) {
            return true;
        }

        return false;
    }

    /**
     * Redact/Mask phone numbers in content as a fail-safe measure
     */
    public static function maskPhoneNumbers(string $text): string
    {
        $normalized = self::normalizeDigits($text);

        // Redact international numbers
        $masked = preg_replace('/(?:\+|00)[0-9]{1,4}[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,14}/i', '[PROTECTED_CONTACT]', $normalized);

        // Redact local prefixes
        $masked = preg_replace('/(?:(?:\b|[^0-9])(?:01[0125]|05[0-9]|02|03|04)[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,10})/i', ' [PROTECTED_CONTACT]', $masked);

        // Redact spaced/obfuscated digit sequences
        $masked = preg_replace('/(?:[0-9][\s\-\.\,\/\(\)\#\*\_]{0,3}){7,15}[0-9]/', '[PROTECTED_CONTACT]', $masked);

        // Redact contiguous digit strings
        $masked = preg_replace('/\b[0-9]{8,16}\b/', '[PROTECTED_CONTACT]', $masked);

        return trim($masked);
    }
}

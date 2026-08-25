<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class AutoTranslationService
{
    /**
     * Translate text from source language to target language.
     *
     * @param string $text
     * @param string $from Default 'ar'
     * @param string $to Default 'en'
     * @return string
     */
    public static function translate(string $text, string $from = 'ar', string $to = 'en'): string
    {
        if (empty(trim($text))) {
            return '';
        }

        try {
            $response = Http::timeout(5)->get('https://api.mymemory.translated.net/get', [
                'q' => $text,
                'langpair' => "{$from}|{$to}",
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['responseData']['translatedText'])) {
                    return $data['responseData']['translatedText'];
                }
            }
        } catch (Throwable $e) {
            // Fallback on network failure
        }

        return $text;
    }

    /**
     * Auto-translate an array of form state fields (e.g. title_ar -> title_en).
     */
    public static function translateFormData(array $data, string $from = 'ar', string $to = 'en'): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = static::translateFormData($value, $from, $to);
            } elseif (is_string($value) && str_ends_with($key, "_{$from}")) {
                $targetKey = substr($key, 0, -strlen("_{$from}")) . "_{$to}";
                if (empty($data[$targetKey])) {
                    $data[$targetKey] = static::translate($value, $from, $to);
                }
            }
        }

        return $data;
    }
}

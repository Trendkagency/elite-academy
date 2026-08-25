<?php

namespace App\Services\Translation;

use App\Services\Translation\Contracts\TranslationServiceInterface;
use Illuminate\Support\Facades\Http;
use Throwable;

class MyMemoryTranslationService implements TranslationServiceInterface
{
    /**
     * Dictionary fallbacks for common developer & UI strings.
     */
    protected array $fallbackDict = [
        'en_ar' => [
            'Test Demo Headline' => 'عنوان رئيسي تجريبي للاختبار',
            'Demo' => 'عرض تجريبي',
            'Test' => 'اختبار',
            'Save' => 'حفظ',
            'Cancel' => 'إلغاء',
            'Delete' => 'حذف',
            'Edit' => 'تعديل',
            'Dashboard' => 'لوحة التحكم',
            'Settings' => 'الإعدادات',
        ],
        'ar_en' => [
            'عنوان رئيسي تجريبي للاختبار' => 'Test Demo Headline',
            'حفظ' => 'Save',
            'إلغاء' => 'Cancel',
            'حذف' => 'Delete',
            'تعديل' => 'Edit',
            'لوحة التحكم' => 'Dashboard',
        ],
    ];

    public function translate(string $text, string $from = 'ar', string $to = 'en'): string
    {
        if (empty(trim($text))) {
            return '';
        }

        // Check fallback dictionary first
        $dictKey = "{$from}_{$to}";
        if (isset($this->fallbackDict[$dictKey][$text])) {
            return $this->fallbackDict[$dictKey][$text];
        }

        // 1. Protect Placeholders (:name, {{ $var }}, HTML tags, URLs)
        $protectedMap = [];
        $protectedIndex = 0;

        // Protect HTML tags
        $cleanText = preg_replace_callback('/<[^>]+>/', function ($matches) use (&$protectedMap, &$protectedIndex) {
            $placeholder = "___HTML_{$protectedIndex}___";
            $protectedMap[$placeholder] = $matches[0];
            $protectedIndex++;
            return $placeholder;
        }, $text);

        // Protect Laravel placeholders like :name, :count
        $cleanText = preg_replace_callback('/:[a-zA-Z0-9_]+/', function ($matches) use (&$protectedMap, &$protectedIndex) {
            $placeholder = "___VAR_{$protectedIndex}___";
            $protectedMap[$placeholder] = $matches[0];
            $protectedIndex++;
            return $placeholder;
        }, $cleanText);

        // Protect Blade variables like {{ $var }}
        $cleanText = preg_replace_callback('/\{\{\s*\$[a-zA-Z0-9_]+\s*\}\}/', function ($matches) use (&$protectedMap, &$protectedIndex) {
            $placeholder = "___BLADE_{$protectedIndex}___";
            $protectedMap[$placeholder] = $matches[0];
            $protectedIndex++;
            return $placeholder;
        }, $cleanText);

        $translatedText = $text;

        // Engine 1: Google GTX Translate Engine
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            ])->timeout(5)->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl' => $from,
                'tl' => $to,
                'dt' => 't',
                'q' => $cleanText,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $gtxResult = '';
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data[0] as $sentence) {
                        if (isset($sentence[0]) && is_string($sentence[0])) {
                            $gtxResult .= $sentence[0];
                        }
                    }
                }
                if (!empty(trim($gtxResult)) && (mb_strtolower(trim($gtxResult)) !== mb_strtolower(trim($cleanText)) || strlen($cleanText) <= 2)) {
                    $translatedText = $gtxResult;
                }
            }
        } catch (Throwable $e) {
            // Fallback to Engine 2
        }

        // Engine 2: MyMemory API Fallback (if GTX failed or returned original)
        if ($translatedText === $text) {
            try {
                $response = Http::timeout(5)->get('https://api.mymemory.translated.net/get', [
                    'q' => $cleanText,
                    'langpair' => "{$from}|{$to}",
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['responseData']['translatedText'])) {
                        $translatedText = $data['responseData']['translatedText'];
                    }
                }
            } catch (Throwable $e) {
                $translatedText = $text;
            }
        }

        // 2. Restore Protected Placeholders
        foreach ($protectedMap as $placeholder => $originalValue) {
            $translatedText = str_replace($placeholder, $originalValue, $translatedText);
        }

        // 3. Basic XSS Sanitization (Strip malicious script tags)
        $translatedText = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $translatedText);
        $translatedText = preg_replace('/on\w+\s*=\s*(["\']).*?\1/i', '', $translatedText);

        return $translatedText;
    }

    public function batchTranslate(array $texts, string $from = 'ar', string $to = 'en'): array
    {
        $results = [];
        foreach ($texts as $key => $text) {
            $results[$key] = $this->translate($text, $from, $to);
        }
        return $results;
    }
}

<?php

namespace App\Services\Translation\Contracts;

interface TranslationServiceInterface
{
    /**
     * Translate single text string preserving HTML tags and placeholders.
     */
    public function translate(string $text, string $from = 'ar', string $to = 'en'): string;

    /**
     * Batch translate an array of text strings.
     *
     * @param array<string, string> $texts Keyed array of texts
     */
    public function batchTranslate(array $texts, string $from = 'ar', string $to = 'en'): array;
}

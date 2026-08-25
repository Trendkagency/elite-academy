<?php

namespace App\Services\Translation;

use App\Models\TranslationHistory;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Services\Translation\Contracts\TranslationServiceInterface;
use Illuminate\Support\Facades\Cache;

class TranslationManagerService
{
    protected TranslationServiceInterface $translator;

    public function __construct(TranslationServiceInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Fetch cached translation dictionary for locale.
     */
    public static function getDictionary(string $locale): array
    {
        return Cache::rememberForever("system_translation_dict_{$locale}", function () use ($locale) {
            $dictionary = [];
            $values = TranslationValue::with('key')
                ->where('locale', $locale)
                ->whereNotNull('value')
                ->where('value', '!=', '')
                ->get();

            foreach ($values as $val) {
                if ($val->key) {
                    $dictionary[$val->key->key] = $val->value;
                }
            }

            return $dictionary;
        });
    }

    /**
     * Invalidate translation cache for all locales.
     */
    public static function clearCache(): void
    {
        Cache::forget('system_translation_dict_ar');
        Cache::forget('system_translation_dict_en');
    }

    /**
     * Save or update a single translation value with history tracking.
     */
    public function updateTranslation(
        string $keyStr,
        string $locale,
        ?string $newValue,
        string $source = 'manual',
        ?int $userId = null,
        bool $isLocked = false
    ): TranslationValue {
        $key = TranslationKey::firstOrCreate(
            ['key' => $keyStr],
            ['group' => strtok($keyStr, '.')]
        );

        $valueModel = TranslationValue::firstOrNew([
            'translation_key_id' => $key->id,
            'locale' => $locale,
        ]);

        $oldValue = $valueModel->value;

        // Skip if locked and trying to overwrite with automatic source
        if ($valueModel->exists && $valueModel->is_locked && $source === 'automatic') {
            return $valueModel;
        }

        $valueModel->value = $newValue;
        $valueModel->source = $source;
        $valueModel->status = !empty(trim($newValue ?? '')) ? 'translated' : 'missing';
        if ($isLocked) {
            $valueModel->is_locked = true;
        }
        if ($userId) {
            $valueModel->translated_by = $userId;
        }
        $valueModel->save();

        // Record history if value changed
        if ($oldValue !== $newValue) {
            TranslationHistory::create([
                'translation_value_id' => $valueModel->id,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'locale' => $locale,
                'source' => $source,
                'changed_by_user_id' => $userId,
            ]);
        }

        static::clearCache();

        return $valueModel;
    }

    /**
     * Auto-translate a single key from source locale to target locale.
     */
    public function translateKey(
        TranslationKey $key,
        string $from = 'ar',
        string $to = 'en',
        ?int $userId = null
    ): ?TranslationValue {
        $sourceValue = $key->getValueForLocale($from);
        if (empty($sourceValue)) {
            return null;
        }

        $targetModel = $key->getTranslationValueModel($to);
        if ($targetModel && $targetModel->is_locked) {
            return $targetModel;
        }

        $translatedText = $this->translator->translate($sourceValue, $from, $to);
        return $this->updateTranslation($key->key, $to, $translatedText, 'automatic', $userId);
    }

    /**
     * Batch translate missing keys.
     */
    public function batchTranslateMissing(string $from = 'ar', string $to = 'en', ?int $userId = null): int
    {
        $count = 0;
        $keys = TranslationKey::with('values')->get();

        foreach ($keys as $key) {
            $targetVal = $key->getValueForLocale($to);
            $sourceVal = $key->getValueForLocale($from);

            if (!empty($sourceVal) && empty($targetVal)) {
                $targetModel = $key->getTranslationValueModel($to);
                if (!$targetModel || !$targetModel->is_locked) {
                    $this->translateKey($key, $from, $to, $userId);
                    $count++;
                }
            }
        }

        return $count;
    }
}

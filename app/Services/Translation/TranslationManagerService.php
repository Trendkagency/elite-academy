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
            if (!\Illuminate\Support\Facades\Schema::hasTable('translation_values') || !\Illuminate\Support\Facades\Schema::hasTable('translation_keys')) {
                return [];
            }

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
     * Invalidate translation cache for all locales and reset translator in memory.
     */
    public static function clearCache(): void
    {
        Cache::forget('system_translation_dict_ar');
        Cache::forget('system_translation_dict_en');

        try {
            if (app()->bound('translator')) {
                $translator = app('translator');
                $ref = new \ReflectionClass($translator);
                if ($ref->hasProperty('loaded')) {
                    $prop = $ref->getProperty('loaded');
                    $prop->setAccessible(true);
                    $prop->setValue($translator, []);
                }
            }
        } catch (\Throwable $e) {
            // Fail-safe in background tasks
        }
    }

    /**
     * Save or update a single translation value with history tracking and sync to disk.
     */
    public function updateTranslation(
        string $keyStr,
        string $locale,
        ?string $newValue,
        string $source = 'manual',
        ?int $userId = null,
        bool $isLocked = false,
        bool $autoExport = true
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

        if ($autoExport) {
            $this->exportToJsonFiles();
        }

        return $valueModel;
    }

    /**
     * Auto-translate a single key from source locale to target locale.
     */
    public function translateKey(
        TranslationKey $key,
        string $from = 'ar',
        string $to = 'en',
        ?int $userId = null,
        bool $autoExport = true
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
        return $this->updateTranslation($key->key, $to, $translatedText, 'automatic', $userId, false, $autoExport);
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
                    $this->translateKey($key, $from, $to, $userId, false);
                    $count++;
                }
            }
        }

        if ($count > 0) {
            static::clearCache();
            $this->exportToJsonFiles();
        }

        return $count;
    }

    /**
     * Flatten nested language array into dot notation.
     */
    public static function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";
            if (is_array($value)) {
                $result = array_merge($result, static::flattenArray($value, $fullKey));
            } else {
                $result[$fullKey] = (string) $value;
            }
        }
        return $result;
    }

    /**
     * Scan and import all translation keys from PHP lang files, JSON files, and DB.
     */
    public function importAllSystemKeys(): array
    {
        $importedCount = 0;
        $updatedCount = 0;

        // 1. Scan PHP language files
        $dirs = [
            base_path('lang/ar') => 'ar',
            base_path('lang/en') => 'en',
            resource_path('lang/ar') => 'ar',
            resource_path('lang/en') => 'en',
        ];

        $phpData = ['ar' => [], 'en' => []];
        foreach ($dirs as $dir => $loc) {
            if (!is_dir($dir)) continue;
            foreach (glob($dir . '/*.php') as $phpFile) {
                $grp = basename($phpFile, '.php');
                $arr = include $phpFile;
                if (is_array($arr)) {
                    $flat = static::flattenArray($arr, $grp);
                    $phpData[$loc] = array_merge($phpData[$loc], $flat);
                }
            }
        }

        // 2. Scan JSON files
        $jsonFiles = [
            'ar' => [base_path('lang/ar.json'), resource_path('lang/ar.json')],
            'en' => [base_path('lang/en.json'), resource_path('lang/en.json')],
        ];

        $jsonData = ['ar' => [], 'en' => []];
        foreach ($jsonFiles as $loc => $paths) {
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $decoded = json_decode(file_get_contents($path), true);
                    if (is_array($decoded)) {
                        $jsonData[$loc] = array_merge($jsonData[$loc], $decoded);
                    }
                }
            }
        }

        // Collect all distinct keys across PHP and JSON
        $allDiscoveredKeys = array_unique(array_merge(
            array_keys($phpData['ar']),
            array_keys($phpData['en']),
            array_keys($jsonData['ar']),
            array_keys($jsonData['en'])
        ));

        foreach ($allDiscoveredKeys as $keyStr) {
            $key = TranslationKey::firstOrCreate(
                ['key' => $keyStr],
                ['group' => strtok($keyStr, '.')]
            );

            if ($key->wasRecentlyCreated) {
                $importedCount++;
            }

            foreach (['ar', 'en'] as $loc) {
                $valueModel = TranslationValue::firstOrNew([
                    'translation_key_id' => $key->id,
                    'locale' => $loc,
                ]);

                // Determine best candidate value
                $candidate = null;

                // Candidate from DB
                if (!empty(trim($valueModel->value ?? '')) && $valueModel->value !== $keyStr) {
                    $candidate = $valueModel->value;
                }

                // Candidate from PHP file
                if (empty($candidate) && !empty(trim($phpData[$loc][$keyStr] ?? '')) && $phpData[$loc][$keyStr] !== $keyStr) {
                    $candidate = $phpData[$loc][$keyStr];
                }

                // Candidate from JSON file
                if (empty($candidate) && !empty(trim($jsonData[$loc][$keyStr] ?? '')) && $jsonData[$loc][$keyStr] !== $keyStr) {
                    $candidate = $jsonData[$loc][$keyStr];
                }

                // If it was raw placeholder, replace it with candidate if found
                if ($candidate !== null && $candidate !== $valueModel->value) {
                    $valueModel->value = $candidate;
                    $valueModel->source = $valueModel->source ?: 'imported';
                    $valueModel->status = 'translated';
                    $valueModel->save();
                    $updatedCount++;
                }
            }
        }

        static::clearCache();
        $this->exportToJsonFiles();

        return [
            'total_keys' => TranslationKey::count(),
            'newly_imported' => $importedCount,
            'values_updated' => $updatedCount,
        ];
    }

    /**
     * Export all current database translations directly to JSON files in lang/ and resources/lang/.
     */
    public function exportToJsonFiles(): array
    {
        $keys = TranslationKey::with('values')->get();

        $arDict = [];
        $enDict = [];

        // Seed with existing JSON files to preserve non-conflicting keys
        $existingPaths = [
            'ar' => [base_path('lang/ar.json'), resource_path('lang/ar.json')],
            'en' => [base_path('lang/en.json'), resource_path('lang/en.json')],
        ];

        foreach ($existingPaths as $loc => $paths) {
            foreach ($paths as $p) {
                if (file_exists($p)) {
                    $raw = json_decode(file_get_contents($p), true);
                    if (is_array($raw)) {
                        if ($loc === 'ar') {
                            $arDict = array_merge($arDict, $raw);
                        } else {
                            $enDict = array_merge($enDict, $raw);
                        }
                    }
                }
            }
        }

        // Overlay with database translations (DB is single source of truth)
        foreach ($keys as $keyRecord) {
            $k = $keyRecord->key;
            $arVal = $keyRecord->getValueForLocale('ar');
            $enVal = $keyRecord->getValueForLocale('en');

            if (!empty(trim($arVal ?? ''))) {
                $arDict[$k] = $arVal;
            }

            if (!empty(trim($enVal ?? ''))) {
                // If the EN value was mistakenly equal to a dot key, only write if real text
                if (!preg_match('/^[a-z0-9_]+(\.[a-z0-9_]+)+$/i', $k) || $enVal !== $k) {
                    $enDict[$k] = $enVal;
                }
            }
        }

        // Sort alphabetically for clean git diffs
        ksort($arDict, SORT_NATURAL | SORT_FLAG_CASE);
        ksort($enDict, SORT_NATURAL | SORT_FLAG_CASE);

        $jsonFlags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $arEncoded = json_encode($arDict, $jsonFlags) . "\n";
        $enEncoded = json_encode($enDict, $jsonFlags) . "\n";

        $targets = [
            base_path('lang/ar.json') => $arEncoded,
            base_path('lang/en.json') => $enEncoded,
            resource_path('lang/ar.json') => $arEncoded,
            resource_path('lang/en.json') => $enEncoded,
        ];

        foreach ($targets as $filePath => $content) {
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @file_put_contents($filePath, $content, LOCK_EX);
        }

        static::clearCache();

        return [
            'ar_count' => count($arDict),
            'en_count' => count($enDict),
        ];
    }
}


<?php

namespace App\Services\Translation;

use Illuminate\Support\Arr;
use Illuminate\Translation\FileLoader;

class DatabaseFileTranslationLoader extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string|null  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        $lines = parent::load($locale, $group, $namespace);

        // Only inject database translations for global namespace
        if ($namespace === '*' || is_null($namespace)) {
            try {
                $dbDict = TranslationManagerService::getDictionary($locale);

                if (!empty($dbDict)) {
                    if ($group === '*') {
                        // For JSON translations, DB translations override and supplement disk lines
                        $lines = array_merge($lines, $dbDict);
                    } else {
                        // For namespaced groups (e.g. 'app', 'navbar'), merge keys prefixed with "$group."
                        $prefix = $group . '.';
                        $prefixLen = strlen($prefix);

                        foreach ($dbDict as $key => $val) {
                            if (str_starts_with($key, $prefix) && !empty($val)) {
                                $subKey = substr($key, $prefixLen);
                                Arr::set($lines, $subKey, $val);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fail-safe during early bootstrapping or migrations
            }
        }

        return $lines;
    }
}

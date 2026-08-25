<?php

namespace Database\Seeders;

use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Services\Translation\TranslationManagerService;
use Illuminate\Database\Seeder;

class TranslationSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌐 Seeding Enterprise Translation System Keys & Values...');

        // 1. Load ar.json and en.json
        $arJsonPath = resource_path('lang/ar.json');
        $enJsonPath = resource_path('lang/en.json');

        $arData = file_exists($arJsonPath) ? json_decode(file_get_contents($arJsonPath), true) : [];
        $enData = file_exists($enJsonPath) ? json_decode(file_get_contents($enJsonPath), true) : [];

        $allKeys = array_unique(array_merge(array_keys($arData), array_keys($enData)));

        foreach ($allKeys as $keyStr) {
            $keyModel = TranslationKey::updateOrCreate(
                ['key' => $keyStr],
                ['group' => 'json', 'description' => 'System UI Dictionary String']
            );

            if (isset($arData[$keyStr])) {
                TranslationValue::updateOrCreate(
                    ['translation_key_id' => $keyModel->id, 'locale' => 'ar'],
                    ['value' => $arData[$keyStr], 'source' => 'system', 'status' => 'translated']
                );
            }

            if (isset($enData[$keyStr])) {
                TranslationValue::updateOrCreate(
                    ['translation_key_id' => $keyModel->id, 'locale' => 'en'],
                    ['value' => $enData[$keyStr], 'source' => 'system', 'status' => 'translated']
                );
            }
        }

        // 2. Load Feature PHP Arrays
        $files = ['app.php', 'blog.php', 'common.php', 'contact.php', 'events.php', 'footer.php', 'home.php', 'navbar.php', 'subjects.php', 'teachers.php', 'validation.php'];

        foreach ($files as $file) {
            $group = substr($file, 0, -4);
            $arFile = resource_path("lang/ar/{$file}");
            $enFile = resource_path("lang/en/{$file}");

            $arArr = file_exists($arFile) ? include($arFile) : [];
            $enArr = file_exists($enFile) ? include($enFile) : [];

            $this->seedFlatArray($group, $arArr, $enArr);
        }

        TranslationManagerService::clearCache();

        $this->command->info('✅ Translation System Seeded Successfully!');
    }

    protected function seedFlatArray(string $group, array $arArr, array $enArr, string $prefix = ''): void
    {
        $keys = array_unique(array_merge(array_keys($arArr), array_keys($enArr)));

        foreach ($keys as $k) {
            $fullKey = "{$group}." . ($prefix === '' ? $k : "{$prefix}.{$k}");

            $arVal = $arArr[$k] ?? null;
            $enVal = $enArr[$k] ?? null;

            if (is_array($arVal) || is_array($enVal)) {
                $this->seedFlatArray($group, is_array($arVal) ? $arVal : [], is_array($enVal) ? $enVal : [], $prefix === '' ? $k : "{$prefix}.{$k}");
            } else {
                $keyModel = TranslationKey::updateOrCreate(
                    ['key' => $fullKey],
                    ['group' => $group, 'description' => "Feature {$group} array string"]
                );

                if (!empty($arVal)) {
                    TranslationValue::updateOrCreate(
                        ['translation_key_id' => $keyModel->id, 'locale' => 'ar'],
                        ['value' => $arVal, 'source' => 'system', 'status' => 'translated']
                    );
                }

                if (!empty($enVal)) {
                    TranslationValue::updateOrCreate(
                        ['translation_key_id' => $keyModel->id, 'locale' => 'en'],
                        ['value' => $enVal, 'source' => 'system', 'status' => 'translated']
                    );
                }
            }
        }
    }
}

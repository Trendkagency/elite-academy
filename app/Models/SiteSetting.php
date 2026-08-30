<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    protected static ?array $runtimeCache = null;

    /**
     * Get all settings key-value pairs cached in memory.
     */
    public static function allCached(): array
    {
        if (static::$runtimeCache !== null) {
            return static::$runtimeCache;
        }

        return static::$runtimeCache = Cache::rememberForever('site_settings_dict', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = static::allCached();

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return $default;
    }

    public static function getLocalized(string $key, ?string $default = null): ?string
    {
        $locale = app()->getLocale();
        $localizedKey = "{$key}_{$locale}";
        $val = static::get($localizedKey);

        if ($val !== null && $val !== '') {
            return $val;
        }

        $fallbackLocale = $locale === 'ar' ? 'en' : 'ar';
        $fallbackKey = "{$key}_{$fallbackLocale}";
        $val = static::get($fallbackKey);

        if ($val !== null && $val !== '') {
            return $val;
        }

        $raw = static::get($key, $default);
        return __($raw ?? '');
    }

    public static function set(string $key, ?string $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        static::$runtimeCache = null;
        Cache::forget('site_settings_dict');
    }
}

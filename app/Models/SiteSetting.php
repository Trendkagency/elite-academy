<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
            return $default;
        }

        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
}

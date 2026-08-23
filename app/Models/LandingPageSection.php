<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPageSection extends Model
{
    protected $fillable = [
        'landing_page_id',
        'section_key',
        'type',
        'title_en',
        'title_ar',
        'subtitle_en',
        'subtitle_ar',
        'badge_en',
        'badge_ar',
        'image_url',
        'settings_json',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'settings_json' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }

    public function counters(): HasMany
    {
        return $this->hasMany(LandingPageCounter::class, 'section_id')->orderBy('sort_order', 'asc');
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings_json ?? [];
        return $settings[$key] ?? $default;
    }
}

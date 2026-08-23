<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'track_label',
        'cta_primary_url',
        'cta_secondary_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getLocalizedTitle(): string
    {
        return __($this->title ?? '');
    }

    public function getLocalizedSubtitle(): string
    {
        return __($this->subtitle ?? '');
    }

    public function getLocalizedTrackLabel(): string
    {
        return __($this->track_label ?? '');
    }
}
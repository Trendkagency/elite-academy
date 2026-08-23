<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'published_version_id',
        'status',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(LandingPageSection::class, 'landing_page_id')->orderBy('sort_order', 'asc');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(LandingPageVersion::class, 'landing_page_id')->orderBy('version_number', 'desc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color_theme',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Course::class, Subject::class);
    }

    public function getLocalizedName(): string
    {
        return __($this->name ?? '');
    }

    public function getActiveSubjectsCount(): int
    {
        return $this->subjects()->where('is_active', true)->count();
    }

    public function getActiveCoursesCount(): int
    {
        return $this->courses()->where('courses.is_active', true)->count();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $table = 'articles';

    protected $fillable = [
        'author_user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'category',
        'read_time_minutes',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'read_time_minutes' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->useDisk('public')
            ->singleFile();
    }

    public function getFeaturedImageUrlAttribute(): string
    {
        if ($this->hasMedia('featured_image')) {
            return $this->getFirstMediaUrl('featured_image');
        }

        return $this->image ?: 'images/hero_student.png';
    }

    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSession extends Model
{
    protected $table = 'course_sessions';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
        'duration_minutes',
        'video_url',
        'content',
        'is_free_demo',
    ];

    protected $casts = [
        'is_free_demo' => 'boolean',
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'course_session_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CourseSessionProgress::class, 'course_session_id');
    }
}

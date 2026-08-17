<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TeacherProfile extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $table = 'teacher_profiles';

    protected $fillable = [
        'user_id',
        'slug',
        'photo',
        'title',
        'specialization',
        'bio',
        'years_experience',
        'rating_avg',
        'students_count',
        'is_featured',
        'is_public',
        'show_contact_info',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'rating_avg' => 'float',
        'years_experience' => 'integer',
        'students_count' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile();
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->hasMedia('photo')) {
            return $this->getFirstMediaUrl('photo');
        }

        return $this->photo ?: 'images/hero_student.png';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher');
    }

    public function gradeLevels(): BelongsToMany
    {
        return $this->belongsToMany(GradeLevel::class, 'teacher_grade_level');
    }
}

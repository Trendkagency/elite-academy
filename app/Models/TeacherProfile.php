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
            ->useDisk('public')
            ->singleFile();
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->hasMedia('photo')) {
            $url = $this->getFirstMediaUrl('photo');
            if (! empty($url)) {
                return $url;
            }
        }

        // 2. Custom uploaded photo if not a static demo template image
        if (! empty($this->photo) && ! in_array(basename($this->photo), [
            'instructor_portrait.png', 'instructor_female.png', 'instructor_male.png',
            'instructor_portrait.webp', 'instructor_female.webp', 'instructor_male.webp',
        ], true)) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo)) {
                return asset('storage/' . $this->photo);
            }
            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
        }

        $name = urlencode($this->user?->name ?? 'Teacher');

        return "https://ui-avatars.com/api/?name={$name}&background=4F46E5&color=ffffff&size=256&bold=true&font-size=0.38";
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

    public function educationalNotes(): HasMany
    {
        return $this->hasMany(StudentEducationalNote::class, 'teacher_profile_id');
    }
}

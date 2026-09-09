<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'grade_level_id',
        'school_name',
        'date_of_birth',
        'avatar',
        'has_used_free_session',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'has_used_free_session' => 'boolean',
    ];

    public function getAvatarUrlAttribute(): string
    {
        if (! empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
                return asset('storage/' . $this->avatar);
            }
            if (file_exists(public_path($this->avatar))) {
                return asset($this->avatar);
            }

            return asset('storage/' . $this->avatar);
        }

        $name = urlencode($this->user?->name ?? 'Student');

        return "https://ui-avatars.com/api/?name={$name}&background=0D9488&color=ffffff&size=200&bold=true&font-size=0.38";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'student_user_id', 'user_id');
    }

    public function sessionProgress(): HasMany
    {
        return $this->hasMany(CourseSessionProgress::class);
    }

    public function subjects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subject', 'student_profile_id', 'subject_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_user_id', 'user_id');
    }
}

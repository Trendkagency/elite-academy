<?php

namespace App\Models;

use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'grade_level_id',
        'title',
        'slug',
        'description',
        'image',
        'is_active',
        'sessions_count',
        'session_duration_minutes',
        'has_free_demo',
        'is_accredited',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_free_demo' => 'boolean',
        'is_accredited' => 'boolean',
        'sessions_count' => 'integer',
        'session_duration_minutes' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CourseSession::class)->orderBy('order');
    }
}

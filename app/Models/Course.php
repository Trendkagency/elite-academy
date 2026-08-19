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

    protected static function booted(): void
    {
        static::saved(function (Course $course) {
            // 1. Sync free demo policy across all sessions
            if ($course->wasChanged('has_free_demo')) {
                if (! $course->has_free_demo) {
                    CourseSession::where('course_id', $course->id)->update(['is_free_demo' => false]);
                    LiveSession::where('course_id', $course->id)->update(['is_free_demo' => false]);
                } else {
                    $firstSession = CourseSession::where('course_id', $course->id)->orderBy('sort_order')->first();
                    if ($firstSession) {
                        $firstSession->update(['is_free_demo' => true]);
                    }
                    $firstLive = LiveSession::where('course_id', $course->id)->orderBy('scheduled_at')->first();
                    if ($firstLive) {
                        $firstLive->update(['is_free_demo' => true]);
                    }
                }
            }

            // 2. Sync session duration
            if ($course->wasChanged('session_duration_minutes')) {
                CourseSession::where('course_id', $course->id)->update(['duration_minutes' => $course->session_duration_minutes]);
                LiveSession::where('course_id', $course->id)->update(['duration_minutes' => $course->session_duration_minutes]);
            }
        });
    }

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
        return $this->hasMany(CourseSession::class)->orderBy('sort_order');
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class)->orderBy('scheduled_at', 'desc');
    }
}

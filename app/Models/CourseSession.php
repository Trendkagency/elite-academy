<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSession extends Model
{
    use SoftDeletes;
    protected $table = 'course_sessions';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
        'duration_minutes',
        'scheduled_at',
        'start_at',
        'end_at',
        'video_url',
        'content',
        'is_free_demo',
    ];

    protected $casts = [
        'is_free_demo' => 'boolean',
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
        'scheduled_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (CourseSession $courseSession) {
            $scheduledAt = $courseSession->scheduled_at ?: ($courseSession->start_at ?: now());
            $startAt = $courseSession->start_at ?: $scheduledAt;
            $duration = $courseSession->duration_minutes ?: 60;
            $endAt = $courseSession->end_at ?: $scheduledAt->copy()->addMinutes($duration);

            $updateData = [
                'course_id' => $courseSession->course_id,
                'title' => $courseSession->title,
                'subject_id' => $courseSession->course?->subject_id,
                'teacher_profile_id' => $courseSession->course?->teacher_id,
                'is_free_demo' => (bool) $courseSession->is_free_demo,
                'duration_minutes' => $duration,
                'scheduled_at' => $scheduledAt,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => 'scheduled',
            ];

            if ($courseSession->video_url) {
                $updateData['meeting_link'] = $courseSession->video_url;
            }

            LiveSession::updateOrCreate(
                ['course_session_id' => $courseSession->id],
                $updateData
            );
        });

        static::deleted(function (CourseSession $courseSession) {
            LiveSession::where('course_session_id', $courseSession->id)->delete();
        });
    }

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

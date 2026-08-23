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
        'demo_video_url',
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

    public function getDemoVideoUrl(): string
    {
        if (! empty($this->demo_video_url)) {
            $url = trim($this->demo_video_url);
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                return $url;
            }

            return asset(ltrim($url, '/'));
        }

        $subjectSlug = $this->subject ? $this->subject->slug : '';

        return match ($subjectSlug) {
            'physics' => asset('videos/physics_demo.mp4'),
            'chemistry' => asset('videos/chemistry_demo.mp4'),
            'biology' => asset('videos/chemistry_demo.mp4'),
            'mathematics' => asset('videos/math_demo.mp4'),
            'programming' => asset('videos/programming_demo.mp4'),
            'arabic' => asset('videos/arabic_demo.mp4'),
            'english' => asset('videos/english_demo.mp4'),
            default => asset('videos/physics_demo.mp4'),
        };
    }

    public function getVideoEmbedData(): array
    {
        $rawUrl = $this->getDemoVideoUrl();

        // 1. YouTube check
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $rawUrl, $matches)) {
            $youtubeId = $matches[1];
            return [
                'type' => 'youtube',
                'embed_url' => "https://www.youtube.com/embed/{$youtubeId}?autoplay=1&rel=0&enablejsapi=1",
                'raw_url' => $rawUrl,
                'youtube_id' => $youtubeId,
            ];
        }

        // 2. Vimeo check
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)/i', $rawUrl, $matches)) {
            $vimeoId = $matches[1];
            return [
                'type' => 'vimeo',
                'embed_url' => "https://player.vimeo.com/video/{$vimeoId}?autoplay=1",
                'raw_url' => $rawUrl,
                'vimeo_id' => $vimeoId,
            ];
        }

        // 3. Native MP4
        return [
            'type' => 'mp4',
            'embed_url' => $rawUrl,
            'raw_url' => $rawUrl,
        ];
    }
}

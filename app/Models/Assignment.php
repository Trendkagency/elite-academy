<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'course_session_id',
        'live_session_id',
        'teacher_profile_id',
        'course_id',
        'title',
        'description',
        'attachment_file_path',
        'attachment_file_name',
        'duration_minutes',
        'start_at',
        'due_at',
        'sort_order',
        'status',
        'max_attempts',
        'passing_score',
        'passing_grade',
        'total_questions',
        'is_mandatory',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'due_at' => 'datetime',
        'sort_order' => 'integer',
        'duration_minutes' => 'integer',
        'max_attempts' => 'integer',
        'total_questions' => 'integer',
        'passing_score' => 'float',
        'passing_grade' => 'float',
        'is_mandatory' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }

    public function courseSession(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'course_session_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_profile_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssignmentQuestion::class, 'assignment_id')->orderBy('sort_order', 'asc');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }

    public function fileUploads(): HasMany
    {
        return $this->hasMany(FileUpload::class, 'assignment_id');
    }

    public function homeworkFiles(): HasMany
    {
        return $this->hasMany(FileUpload::class, 'assignment_id')->where('category', 'homework');
    }

    public function studentSubmissionFiles(?int $studentUserId = null): HasMany
    {
        $query = $this->hasMany(FileUpload::class, 'assignment_id')->where('category', 'submission');
        if ($studentUserId) {
            $query->where('student_user_id', $studentUserId);
        }
        return $query;
    }

    public function getHomeworkFileAttribute(): ?FileUpload
    {
        if (! empty($this->attachment_file_path)) {
            $file = $this->fileUploads()->where('file_path', $this->attachment_file_path)->first();
            if ($file) {
                return $file;
            }
        }

        return $this->homeworkFiles()->first();
    }

    public function getIsFileHomeworkAttribute(): bool
    {
        return ! empty($this->attachment_file_path)
            || $this->homeworkFiles()->exists()
            || ($this->questions_count ?? $this->questions()->count()) === 0;
    }

    public function getHasInteractiveQuestionsAttribute(): bool
    {
        return ($this->questions_count ?? $this->questions()->count()) > 0;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Assignment $assignment) {
            // Rule: Deadline for submitting assignment is 24 hours (One day) before the lesson
            if (! $assignment->due_at && $assignment->live_session_id) {
                $liveSession = $assignment->liveSession ?: LiveSession::find($assignment->live_session_id);
                if ($liveSession && $liveSession->effective_start_at) {
                    $assignment->due_at = $liveSession->effective_start_at->copy()->subDay();
                }
            }
        });

        static::created(function (Assignment $assignment) {
            if ($assignment->status === 'published' || ! $assignment->status) {
                app(\App\Services\Notification\FcmNotificationService::class)->notifyAssignmentAdded($assignment);
            }
            $assignment->syncAttachmentToFileUpload();
        });

        static::updated(function (Assignment $assignment) {
            if ($assignment->wasChanged('status') && $assignment->status === 'published') {
                app(\App\Services\Notification\FcmNotificationService::class)->notifyAssignmentAdded($assignment);
            }
            if ($assignment->wasChanged(['attachment_file_path', 'attachment_file_name', 'title', 'due_at', 'description'])) {
                $assignment->syncAttachmentToFileUpload();
            }
        });
    }

    public function syncAttachmentToFileUpload(): void
    {
        if (empty($this->attachment_file_path)) {
            return;
        }

        $userId = auth()->id() ?: ($this->teacherProfile?->user_id ?: 1);
        $fileName = $this->attachment_file_name ?: basename($this->attachment_file_path);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileType = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? 'image' : 'pdf';
        $fileSize = 0;
        $mimeType = $fileType === 'pdf' ? 'application/pdf' : 'image/' . $ext;

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->attachment_file_path)) {
            $fileSize = \Illuminate\Support\Facades\Storage::disk('public')->size($this->attachment_file_path);
            $mimeType = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($this->attachment_file_path) ?: $mimeType;
        }

        FileUpload::updateOrCreate(
            ['assignment_id' => $this->id],
            [
                'user_id' => $userId,
                'title' => $this->title,
                'description' => $this->description,
                'file_path' => $this->attachment_file_path,
                'original_name' => $fileName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'file_type' => $fileType,
                'category' => 'homework',
                'due_at' => $this->due_at,
                'teacher_profile_id' => $this->teacher_profile_id,
                'course_id' => $this->course_id,
                'live_session_id' => $this->live_session_id,
            ]
        );
    }

    public function getEffectiveDueAtAttribute(): ?\Carbon\Carbon
    {
        if ($this->due_at) {
            return $this->due_at;
        }

        $sessionStart = $this->liveSession?->effective_start_at;
        if ($sessionStart) {
            return $sessionStart->copy()->subDay(); // 24 hours (One day) before lesson
        }

        return null;
    }

    public function isExpired(): bool
    {
        $deadline = $this->effective_due_at;
        return $deadline && now()->greaterThan($deadline);
    }
}

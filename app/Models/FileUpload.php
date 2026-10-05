<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileUpload extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'file_uploads';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'file_type',
        'category',
        'due_at',
        'assignment_id',
        'student_user_id',
        'teacher_profile_id',
        'course_id',
        'live_session_id',
        'downloads_count',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'downloads_count' => 'integer',
        'due_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_size',
        'is_pdf',
        'is_image',
        'is_homework',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_profile_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function getIsHomeworkAttribute(): bool
    {
        return $this->category === 'homework' || $this->assignment_id !== null;
    }

    public function getIsSubmissionAttribute(): bool
    {
        return $this->category === 'submission';
    }

    public function getIsMaterialAttribute(): bool
    {
        return empty($this->category) || $this->category === 'material';
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->file_type === 'pdf' || strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION)) === 'pdf';
    }

    public function getIsImageAttribute(): bool
    {
        return $this->file_type === 'image' || in_array(strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }

    /**
     * Strict Authorization Checker:
     * Validates whether the given user can access/view/download this file.
     */
    public function canAccess(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // 1. Admin has universal access
        if ($user->isAdmin()) {
            return true;
        }

        // 2. The uploader can always access
        if ((int) $user->id === (int) $this->user_id) {
            return true;
        }

        // 3. Directly targeted student
        if ($this->student_user_id && (int) $user->id === (int) $this->student_user_id) {
            return true;
        }

        // 4. Current user is a Teacher
        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;
            if (! $teacherProfile) {
                return false;
            }

            // Attached directly to this teacher's profile
            if ($this->teacher_profile_id && (int) $this->teacher_profile_id === (int) $teacherProfile->id) {
                return true;
            }

            // Attached to a course owned by this teacher
            if ($this->course_id) {
                $isCourseOwner = Course::where('id', $this->course_id)
                    ->where('teacher_id', $teacherProfile->id)
                    ->exists();
                if ($isCourseOwner) {
                    return true;
                }
            }

            // If uploaded by/targeted to a student taught by this teacher
            $targetStudentId = $this->student_user_id ?: ($this->uploader?->isStudent() ? $this->user_id : null);
            if ($targetStudentId) {
                $isTaught = CourseEnrollment::where('student_user_id', $targetStudentId)
                    ->whereHas('course', fn ($q) => $q->where('teacher_id', $teacherProfile->id))
                    ->exists();

                if ($isTaught) {
                    return true;
                }

                $hasSession = LiveSession::where('teacher_profile_id', $teacherProfile->id)
                    ->where('student_user_id', $targetStudentId)
                    ->exists();

                if ($hasSession) {
                    return true;
                }
            }
        }

        // 5. Current user is a Student
        if ($user->isStudent()) {
            // Targeted to this student
            if ($this->student_user_id && (int) $user->id === (int) $this->student_user_id) {
                return true;
            }

            // Attached to a course in which the student is currently enrolled
            if ($this->course_id) {
                $isEnrolled = CourseEnrollment::where('course_id', $this->course_id)
                    ->where('student_user_id', $user->id)
                    ->exists();
                if ($isEnrolled) {
                    return true;
                }
            }

            // Attached to a session attended by this student
            if ($this->live_session_id) {
                $inSession = LiveSession::where('id', $this->live_session_id)
                    ->where(function ($q) use ($user) {
                        $q->where('student_user_id', $user->id)
                            ->orWhereHas('studentSessions', fn ($sq) => $sq->where('student_user_id', $user->id));
                    })
                    ->exists();

                if ($inSession) {
                    return true;
                }
            }
        }

        // 6. Current user is a Parent
        if ($user->isParent()) {
            $linkedStudentUserIds = StudentProfile::where('parent_user_id', $user->id)->pluck('user_id')->toArray();
            if (! empty($linkedStudentUserIds)) {
                if ($this->student_user_id && in_array($this->student_user_id, $linkedStudentUserIds, true)) {
                    return true;
                }
                if ($this->course_id) {
                    return CourseEnrollment::where('course_id', $this->course_id)
                        ->whereIn('student_user_id', $linkedStudentUserIds)
                        ->exists();
                }
            }
        }

        return false;
    }

    /**
     * Scope a query to only include files accessible by the given user.
     */
    public function scopeAccessibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isTeacher()) {
            $teacherProfile = $user->teacherProfile;
            $teacherId = $teacherProfile?->id ?? 0;
            $teacherCourseIds = Course::where('teacher_id', $teacherId)->pluck('id')->toArray();

            return $query->where(function ($q) use ($user, $teacherId, $teacherCourseIds) {
                $q->where('user_id', $user->id)
                    ->orWhere('teacher_profile_id', $teacherId);

                if (! empty($teacherCourseIds)) {
                    $q->orWhereIn('course_id', $teacherCourseIds);
                }
            });
        }

        if ($user->isStudent()) {
            $enrolledCourseIds = CourseEnrollment::where('student_user_id', $user->id)->pluck('course_id')->toArray();

            return $query->where(function ($q) use ($user, $enrolledCourseIds) {
                $q->where('user_id', $user->id)
                    ->orWhere('student_user_id', $user->id);

                if (! empty($enrolledCourseIds)) {
                    $q->orWhereIn('course_id', $enrolledCourseIds);
                }
            });
        }

        if ($user->isParent()) {
            $linkedStudentUserIds = StudentProfile::where('parent_user_id', $user->id)->pluck('user_id')->toArray();
            $enrolledCourseIds = CourseEnrollment::whereIn('student_user_id', $linkedStudentUserIds)->pluck('course_id')->toArray();

            return $query->where(function ($q) use ($linkedStudentUserIds, $enrolledCourseIds) {
                $q->whereIn('student_user_id', $linkedStudentUserIds);
                if (! empty($enrolledCourseIds)) {
                    $q->orWhereIn('course_id', $enrolledCourseIds);
                }
            });
        }

        return $query->where('user_id', $user->id);
    }
}

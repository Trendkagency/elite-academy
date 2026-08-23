<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'rating_avg',
        'students_count',
        'video_lessons_count',
        'active_courses_count',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'rating_avg' => 'float',
        'students_count' => 'integer',
        'video_lessons_count' => 'integer',
        'active_courses_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(TeacherProfile::class, 'subject_teacher');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(StudentProfile::class, 'student_subject', 'subject_id', 'student_profile_id');
    }

    public function getLocalizedName(): string
    {
        return __($this->name ?? '');
    }

    public function getLocalizedDescription(): string
    {
        return __($this->description ?? '');
    }

    public function getActiveCoursesCount(): int
    {
        if ($this->active_courses_count !== null) {
            return (int) $this->active_courses_count;
        }

        return $this->courses()->where('is_active', true)->count();
    }

    public function getVideoLessonsCount(): int
    {
        if ($this->video_lessons_count !== null) {
            return (int) $this->video_lessons_count;
        }

        $activeCourseIds = $this->courses()->where('is_active', true)->pluck('id');
        $sessionsCount = CourseSession::whereIn('course_id', $activeCourseIds)->count();
        if ($sessionsCount > 0) {
            return $sessionsCount;
        }

        return (int) $this->courses()->where('is_active', true)->sum('sessions_count');
    }

    public function getActiveStudentsCount(): int
    {
        if ($this->students_count !== null) {
            return (int) $this->students_count;
        }

        $activeCourseIds = $this->courses()->where('is_active', true)->pluck('id');
        $enrolledStudents = CourseEnrollment::whereIn('course_id', $activeCourseIds)
            ->where('status', 'active')
            ->distinct('student_user_id')
            ->count('student_user_id');

        $subjectStudents = \Illuminate\Support\Facades\DB::table('student_subject')->where('subject_id', $this->id)->count();

        $uniqueTeachersStudents = (int) TeacherProfile::whereHas('courses', function ($q) {
            $q->where('subject_id', $this->id)->where('is_active', true);
        })->sum('students_count');

        $courseEnrollmentsSum = (int) $this->courses()->where('is_active', true)->sum('enrollments_count');

        return max($enrolledStudents, $subjectStudents, $uniqueTeachersStudents, $courseEnrollmentsSum);
    }

    public function getRatingAvg(): float
    {
        if ($this->rating_avg !== null && $this->rating_avg > 0) {
            return round((float) $this->rating_avg, 1);
        }

        $coursesAvg = $this->courses()
            ->where('is_active', true)
            ->where('rating_avg', '>', 0)
            ->avg('rating_avg');

        if ($coursesAvg && $coursesAvg > 0) {
            return round((float) $coursesAvg, 1);
        }

        $teacherAvg = TeacherProfile::whereHas('courses', function ($q) {
            $q->where('subject_id', $this->id)->where('is_active', true);
        })->where('rating_avg', '>', 0)->avg('rating_avg');

        if ($teacherAvg && $teacherAvg > 0) {
            return round((float) $teacherAvg, 1);
        }

        return 4.9;
    }
}

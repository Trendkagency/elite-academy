<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEducationalNote extends Model
{
    use SoftDeletes;

    protected $table = 'student_educational_notes';

    protected $fillable = [
        'teacher_profile_id',
        'student_user_id',
        'category',
        'note',
    ];

    protected static function booted(): void
    {
        static::created(function (StudentEducationalNote $note) {
            app(\App\Services\Notification\FcmNotificationService::class)->notifyStudentEducationalNote($note);
        });
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class);
    }

    public function studentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}

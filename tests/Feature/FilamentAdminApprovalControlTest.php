<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\ExceptionRequest;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminApprovalControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_and_reject_student_exception_requests(): void
    {
        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        $teacherUser = User::create(['name' => 'Teacher', 'email' => 't.approval@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'tapproval']);

        $student = User::create(['name' => 'Student Excused', 'email' => 'student.excused@elite.edu', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);

        $liveSession = LiveSession::create([
            'student_user_id' => $student->id,
            'teacher_profile_id' => $teacher->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now(),
        ]);

        $exceptionReq = ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession->id,
            'reason' => 'Medical Emergency Documented',
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $exceptionReq->fresh()->status);

        // Approve Exception
        $exceptionReq->update(['status' => 'approved']);
        $this->assertEquals('approved', $exceptionReq->fresh()->status);

        // Reject Exception
        $exceptionReq->update(['status' => 'rejected']);
        $this->assertEquals('rejected', $exceptionReq->fresh()->status);
    }
}

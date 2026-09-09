<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSoftDeletedProfileEditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin.profile@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->admin->id]);
    }

    public function test_editing_user_with_soft_deleted_teacher_profile_does_not_throw_duplicate_key_error(): void
    {
        $user = User::create([
            'name' => 'Ayaa Abd El Monaem',
            'email' => 'ayaa@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $teacher = TeacherProfile::create([
            'user_id' => $user->id,
            'slug' => 'ayaa-abd-el-monaem-' . $user->id,
            'title' => 'Biology Lecturer',
            'specialization' => 'Genetics',
            'years_experience' => 5,
        ]);

        // Simulate soft-deletion of the teacher profile
        $teacher->delete();
        $this->assertSoftDeleted('teacher_profiles', ['id' => $teacher->id]);

        // Simulate saving user as teacher in Filament EditUser
        $profile = TeacherProfile::withTrashed()->firstOrNew(['user_id' => $user->id]);
        if ($profile->trashed()) {
            $profile->restore();
        }
        $profile->fill([
            'slug' => $profile->slug ?: ('ayaa-abd-el-monaem-' . $user->id),
            'title' => 'Senior Biology Lecturer',
            'specialization' => 'Molecular Biology',
            'years_experience' => 6,
        ]);
        $profile->save();

        $this->assertDatabaseHas('teacher_profiles', [
            'user_id' => $user->id,
            'title' => 'Senior Biology Lecturer',
            'specialization' => 'Molecular Biology',
            'deleted_at' => null,
        ]);
    }

    public function test_editing_user_with_soft_deleted_student_profile_does_not_throw_duplicate_key_error(): void
    {
        $user = User::create([
            'name' => 'Student Test',
            'email' => 'student.soft@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);

        $student = StudentProfile::create([
            'user_id' => $user->id,
            'school_name' => 'Old School',
        ]);

        $student->delete();
        $this->assertSoftDeleted('student_profiles', ['id' => $student->id]);

        $profile = StudentProfile::withTrashed()->firstOrNew(['user_id' => $user->id]);
        if ($profile->trashed()) {
            $profile->restore();
        }
        $profile->fill([
            'school_name' => 'New International School',
        ]);
        $profile->save();

        $this->assertDatabaseHas('student_profiles', [
            'user_id' => $user->id,
            'school_name' => 'New International School',
            'deleted_at' => null,
        ]);
    }
}

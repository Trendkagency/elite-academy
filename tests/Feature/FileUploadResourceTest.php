<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\AdminProfile;
use App\Models\Category;
use App\Models\Course;
use App\Models\FileUpload;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileUploadResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'System Admin',
            'email' => 'admin@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        AdminProfile::create(['user_id' => $this->adminUser->id]);
    }

    public function test_admin_can_access_file_uploads_index_and_edit_page(): void
    {
        $category = Category::create(['name' => 'General Cat', 'slug' => 'gen-cat']);
        $subject = Subject::create(['name' => 'Math', 'slug' => 'math', 'category_id' => $category->id]);

        $teacherUser = User::create(['name' => 'Prof Ahmed', 'email' => 'ahmed@teacher.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'ahmed']);

        $student = User::create(['name' => 'Student Tarek', 'email' => 'tarek@student.com', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED]);
        StudentProfile::create(['user_id' => $student->id]);

        $course = Course::create(['title' => 'Math 101', 'slug' => 'math-101', 'subject_id' => $subject->id, 'teacher_id' => $teacher->id]);

        $fileUpload = FileUpload::create([
            'user_id' => $this->adminUser->id,
            'title' => 'Test Material PDF',
            'file_path' => 'educational_files/sample.pdf',
            'original_name' => 'sample.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'file_type' => 'pdf',
            'category' => 'material',
            'course_id' => $course->id,
            'teacher_profile_id' => $teacher->id,
            'student_user_id' => $student->id,
        ]);

        $this->actingAs($this->adminUser);

        // Test Edit page (the exact page that triggered the error in the user request: /admin/file-uploads/{id}/edit)
        $editResponse = $this->get("/admin/file-uploads/{$fileUpload->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Prof Ahmed');

        // Test Create page as well
        $createResponse = $this->get('/admin/file-uploads/create');
        $createResponse->assertStatus(200);
    }
}

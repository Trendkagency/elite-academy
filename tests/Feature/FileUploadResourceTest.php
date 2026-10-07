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
        $editResponse->assertSee('Math 101');

        // Test Create page as well
        $createResponse = $this->get('/admin/file-uploads/create');
        $createResponse->assertStatus(200);
        $createResponse->assertSee(__('File Purpose / Category (نوع الملف / الغرض)'));
    }

    public function test_select_search_works_for_course_teacher_and_student(): void
    {
        $category = Category::create(['name' => 'General Cat', 'slug' => 'gen-cat-2']);
        $subject = Subject::create(['name' => 'الفيزياء الحديثة', 'slug' => 'physics-mod', 'category_id' => $category->id]);

        $teacherUser = User::create([
            'name' => 'د. أحمد محمود',
            'email' => 'dr.ahmed@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'slug' => 'dr-ahmed',
            'specialization' => 'Quantum Physics',
        ]);

        $student = User::create([
            'name' => 'أحمد خالد',
            'email' => 'ahmed.khalid@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $student->id]);

        $course = Course::create([
            'title' => 'كورس أساسيات البرمجة',
            'slug' => 'prog-basics',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($this->adminUser);

        $test = \Livewire\Livewire::test(\App\Filament\Resources\FileUploads\Pages\CreateFileUpload::class);
        $schema = $test->instance()->getSchema('form');

        // Course search with without-hamza variant
        $courseComp = $schema->getComponent('course_id');
        $courseResults = $courseComp->getSearchResults('اساسيات');
        $this->assertArrayHasKey($course->id, $courseResults);

        // Course search by teacher name
        $courseByTeacher = $courseComp->getSearchResults('احمد');
        $this->assertArrayHasKey($course->id, $courseByTeacher);

        // Teacher search with without-hamza variant
        $teacherComp = $schema->getComponent('teacher_profile_id');
        $teacherResults = $teacherComp->getSearchResults('احمد');
        $this->assertArrayHasKey($teacher->id, $teacherResults);

        // Teacher search by specialization
        $teacherBySpec = $teacherComp->getSearchResults('Quantum');
        $this->assertArrayHasKey($teacher->id, $teacherBySpec);

        // Student search with without-hamza variant
        $studentComp = $schema->getComponent('student_user_id');
        $studentResults = $studentComp->getSearchResults('احمد');
        $this->assertArrayHasKey($student->id, $studentResults);
    }
}

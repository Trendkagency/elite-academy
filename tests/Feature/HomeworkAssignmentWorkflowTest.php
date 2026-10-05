<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\GradeLevel;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeworkAssignmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $teacherUser;
    protected TeacherProfile $teacherProfile;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $grade = GradeLevel::create(['name' => 'Grade 12', 'slug' => 'g12']);
        $cat = Category::create(['name' => 'Science', 'slug' => 'science']);
        $subject = Subject::create(['category_id' => $cat->id, 'name' => 'Physics', 'slug' => 'physics']);

        $this->teacherUser = User::create([
            'name' => 'Dr. Teacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $this->teacherProfile = TeacherProfile::create([
            'user_id' => $this->teacherUser->id,
            'slug' => 'dr-teacher',
        ]);

        $this->studentUser = User::create([
            'name' => 'Student Ahmed',
            'email' => 'ahmed@example.com',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $this->studentUser->id]);

        $this->course = Course::create([
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacherProfile->id,
            'grade_level_id' => $grade->id,
            'title' => 'Advanced Physics Course',
            'slug' => 'advanced-physics',
            'is_active' => true,
        ]);

        CourseEnrollment::create([
            'student_user_id' => $this->studentUser->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
    }

    public function test_student_sees_file_homework_view_when_assignment_has_no_msq_questions(): void
    {
        $assignment = Assignment::create([
            'title' => 'Physics Homework Sheet 1',
            'description' => 'Solve problems 1 to 5 from the attached PDF.',
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'status' => 'published',
            'attachment_file_path' => 'educational_files/sheet1.pdf',
            'attachment_file_name' => 'Sheet1-Physics.pdf',
            'passing_score' => 70,
            'due_at' => now()->addDays(2),
        ]);

        $response = $this->actingAs($this->studentUser)
            ->get(route('student.assignment.take', ['id' => $assignment->id]));

        $response->assertStatus(200);
        $response->assertSee('Physics Homework Sheet 1');
        $response->assertSee('Sheet1-Physics.pdf');
        $response->assertSee('homeworkUploadForm');
        $response->assertSee('homeworkFileInput');
        // Must NOT see the empty quiz message
        $response->assertDontSee('No questions configured for this assignment yet');
        $response->assertDontSee('لا توجد أسئلة مضافة لهذا الاختبار بعد');
    }

    public function test_student_can_upload_homework_solution_and_create_submission(): void
    {
        Storage::fake('public');

        $assignment = Assignment::create([
            'title' => 'Chemistry Worksheet 3',
            'course_id' => $this->course->id,
            'teacher_profile_id' => $this->teacherProfile->id,
            'status' => 'published',
            'attachment_file_path' => 'educational_files/chem3.pdf',
            'passing_score' => 70,
            'due_at' => now()->addDays(3),
        ]);

        $dummyPdf = UploadedFile::fake()->create('my_solution.pdf', 1024, 'application/pdf');

        $uploadResponse = $this->actingAs($this->studentUser)
            ->postJson(route('ajax.student.files.upload'), [
                'file' => $dummyPdf,
                'title' => 'حل واجب: Chemistry Worksheet 3',
                'description' => 'Here is my solved homework for worksheet 3.',
                'assignment_id' => $assignment->id,
                'course_id' => $this->course->id,
            ]);

        $uploadResponse->assertStatus(200);
        $uploadResponse->assertJson([
            'success' => true,
        ]);

        // Assert database has FileUpload of category 'submission'
        $this->assertDatabaseHas('file_uploads', [
            'assignment_id' => $assignment->id,
            'student_user_id' => $this->studentUser->id,
            'category' => 'submission',
            'original_name' => 'my_solution.pdf',
        ]);

        // Assert AssignmentSubmission was created/updated as 'submitted'
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'student_user_id' => $this->studentUser->id,
            'status' => 'submitted',
        ]);

        // Revisiting the assignment page should now show the submitted solution
        $pageResponse = $this->actingAs($this->studentUser)
            ->get(route('student.assignment.take', ['id' => $assignment->id]));

        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('my_solution.pdf');
    }
}

<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\PackageTemplate;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPackageOneTimeDistributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_creation_distributes_sessions_across_subjects_once_only(): void
    {
        $category = Category::create(['name' => 'General Science', 'slug' => 'general-science']);

        // Teacher
        $teacherUser = User::create([
            'name' => 'Dr. Teacher',
            'email' => 'teacher.dist@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-teacher']);

        // 1. Setup Student
        $student = User::create([
            'name' => 'Tariq Student',
            'email' => 'tariq.pkg@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $student->id]);

        // 2. Setup 3 Subjects and Courses
        $mathSubj = Subject::create(['name' => 'Mathematics', 'slug' => 'math', 'category_id' => $category->id]);
        $physSubj = Subject::create(['name' => 'Physics', 'slug' => 'phys', 'category_id' => $category->id]);
        $chemSubj = Subject::create(['name' => 'Chemistry', 'slug' => 'chem', 'category_id' => $category->id]);

        $mathCourse = Course::create(['title' => 'Calculus I', 'slug' => 'calc-1', 'subject_id' => $mathSubj->id, 'teacher_id' => $teacher->id]);
        $physCourse = Course::create(['title' => 'Mechanics', 'slug' => 'mech-1', 'subject_id' => $physSubj->id, 'teacher_id' => $teacher->id]);
        $chemCourse = Course::create(['title' => 'Organic Chem', 'slug' => 'org-chem', 'subject_id' => $chemSubj->id, 'teacher_id' => $teacher->id]);

        // Enroll in all 3 courses
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $mathCourse->id]);
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $physCourse->id]);
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $chemCourse->id]);

        // 3. Create Package with 12 sessions
        $package = StudentPackage::create([
            'student_user_id' => $student->id,
            'total_sessions' => 12,
            'used_sessions' => 0,
            'remaining_sessions' => 12,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        $fresh = $package->fresh();

        // Must be marked as distributed
        $this->assertTrue((bool) $fresh->is_distributed);
        $this->assertNotEmpty($fresh->subject_distribution);

        // 12 sessions distributed across 3 subjects = 4 each
        $dist = $fresh->subject_distribution;
        $this->assertEquals(4, $dist[(string) $mathSubj->id]);
        $this->assertEquals(4, $dist[(string) $physSubj->id]);
        $this->assertEquals(4, $dist[(string) $chemSubj->id]);
        $this->assertEquals(12, array_sum($dist));

        // 4. Test that distribution is NOT repeated or altered when a 4th subject is enrolled
        $bioSubj = Subject::create(['name' => 'Biology', 'slug' => 'bio', 'category_id' => $category->id]);
        $bioCourse = Course::create(['title' => 'Cell Biology', 'slug' => 'cell-bio', 'subject_id' => $bioSubj->id, 'teacher_id' => $teacher->id]);
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $bioCourse->id]);

        // Attempting to distribute again MUST return false and preserve the existing distribution
        $reDistributed = $fresh->distributeSessionsOnce();
        $this->assertFalse($reDistributed);

        $freshAgain = $fresh->fresh();
        $this->assertEquals(4, $freshAgain->subject_distribution[(string) $mathSubj->id]);
        $this->assertEquals(4, $freshAgain->subject_distribution[(string) $physSubj->id]);
        $this->assertEquals(4, $freshAgain->subject_distribution[(string) $chemSubj->id]);
        $this->assertArrayNotHasKey((string) $bioSubj->id, $freshAgain->subject_distribution);

        // 5. Test that Student Portal reads the one-time distribution
        $this->actingAs($student);
        $response = $this->withSession(['locale' => 'en'])->get('/student-portal');
        $response->assertStatus(200);

        // Package sessions distribution must still remain untouched
        $this->assertEquals(4, $package->fresh()->subject_distribution[(string) $mathSubj->id]);
    }

    public function test_course_restricted_package_distributes_all_sessions_to_that_course_subject(): void
    {
        $category = Category::create(['name' => 'Advanced Category', 'slug' => 'advanced-cat']);

        $teacherUser = User::create([
            'name' => 'Prof. Math',
            'email' => 'math.prof@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'prof-math']);

        $student = User::create([
            'name' => 'Salma Student',
            'email' => 'salma.pkg@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $student->id]);

        $mathSubj = Subject::create(['name' => 'Math Advanced', 'slug' => 'math-adv', 'category_id' => $category->id]);
        $mathCourse = Course::create(['title' => 'Pure Math', 'slug' => 'pure-math', 'subject_id' => $mathSubj->id, 'teacher_id' => $teacher->id]);

        $package = StudentPackage::create([
            'student_user_id' => $student->id,
            'course_id' => $mathCourse->id,
            'total_sessions' => 8,
            'used_sessions' => 0,
            'remaining_sessions' => 8,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        $fresh = $package->fresh();
        $this->assertTrue((bool) $fresh->is_distributed);
        $this->assertEquals(8, $fresh->subject_distribution[(string) $mathSubj->id]);
    }

    public function test_renewing_package_distributes_new_sessions_once(): void
    {
        $category = Category::create(['name' => 'Languages Category', 'slug' => 'languages-cat']);

        $teacherUser = User::create([
            'name' => 'Dr. Lang',
            'email' => 'lang.dr@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        $teacher = TeacherProfile::create(['user_id' => $teacherUser->id, 'slug' => 'dr-lang']);

        $student = User::create([
            'name' => 'Yasin Student',
            'email' => 'yasin.pkg@elite.edu',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
        ]);
        StudentProfile::create(['user_id' => $student->id]);

        $subj1 = Subject::create(['name' => 'Arabic Language', 'slug' => 'arabic', 'category_id' => $category->id]);
        $subj2 = Subject::create(['name' => 'English Language', 'slug' => 'english', 'category_id' => $category->id]);

        $c1 = Course::create(['title' => 'Arabic Grammar', 'slug' => 'arabic-grammar', 'subject_id' => $subj1->id, 'teacher_id' => $teacher->id]);
        $c2 = Course::create(['title' => 'English Literature', 'slug' => 'eng-lit', 'subject_id' => $subj2->id, 'teacher_id' => $teacher->id]);

        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $c1->id]);
        CourseEnrollment::create(['student_user_id' => $student->id, 'course_id' => $c2->id]);

        $package = StudentPackage::create([
            'student_user_id' => $student->id,
            'total_sessions' => 10,
            'used_sessions' => 10,
            'remaining_sessions' => 0,
            'status' => 'exhausted',
        ]);

        $this->assertEquals(5, $package->fresh()->subject_distribution[(string) $subj1->id]);
        $this->assertEquals(5, $package->fresh()->subject_distribution[(string) $subj2->id]);

        // Renew package with 16 sessions
        $package->renewPackage(newTotalSessions: 16);

        $renewed = $package->fresh();
        $this->assertEquals(8, $renewed->subject_distribution[(string) $subj1->id]);
        $this->assertEquals(8, $renewed->subject_distribution[(string) $subj2->id]);
        $this->assertTrue((bool) $renewed->is_distributed);

        // Cannot re-distribute again
        $this->assertFalse($renewed->distributeSessionsOnce());
    }
}

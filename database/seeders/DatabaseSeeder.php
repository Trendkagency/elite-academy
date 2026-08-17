<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;

use App\Models\AdminProfile;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;

use App\Models\GradeLevel;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Grade Levels
        $gradeHigh = GradeLevel::create([
            'name' => 'الصف الثالث الثانوي',
            'slug' => 'grade-12',
            'sort_order' => 1,
        ]);
        $gradeSec = GradeLevel::create([
            'name' => 'الصف الثاني الثانوي',
            'slug' => 'grade-11',
            'sort_order' => 2,
        ]);

        // 2. Create Users & Profiles
        $adminUser = User::create([
            'name' => 'المدير العام',
            'email' => 'admin@elite.edu',
            'phone' => '+201000000001',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);
        AdminProfile::create(['user_id' => $adminUser->id]);

        $teacherUser = User::create([
            'name' => 'د. أحمد محمود',
            'email' => 'teacher@elite.edu',
            'phone' => '+201000000002',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);
        $teacherProfile = TeacherProfile::create([
            'user_id' => $teacherUser->id,
            'slug' => 'dr-ahmed-mahmoud',
            'title' => 'أستاذ الفيزياء والرياضيات',
            'specialization' => 'الفيزياء الحديثة والميكانيكا',
            'bio' => 'خبرة أكثر من 15 عاماً في تدريس الثانوية العامة والجامعات.',
            'years_experience' => 15,
            'rating_avg' => 4.9,
            'students_count' => 1450,
            'is_featured' => true,
            'is_public' => true,
        ]);

        $studentUser = User::create([
            'name' => 'عمر خالد',
            'email' => 'student@elite.edu',
            'phone' => '+201000000003',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);
        $studentProfile = StudentProfile::create([
            'user_id' => $studentUser->id,
            'grade_level_id' => $gradeHigh->id,
            'school_name' => 'مدرسة المتفوقين للعلوم والتكنولوجيا',
            'has_used_free_session' => true,
        ]);

        $parentUser = User::create([
            'name' => 'خالد عبد الله',
            'email' => 'parent@elite.edu',
            'phone' => '+201000000004',
            'password' => bcrypt('password'),
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);
        $parentProfile = ParentProfile::create(['user_id' => $parentUser->id]);
        $parentProfile->students()->attach($studentUser->id, ['relationship' => 'Father']);

        // 3. Create Categories & Subjects
        $categoryScience = Category::create([
            'name' => 'العلوم الطبيعية والتطبيقية',
            'slug' => 'natural-sciences',
            'color_theme' => '#0D9488',
        ]);

        $subjectPhysics = Subject::create([
            'category_id' => $categoryScience->id,
            'name' => 'الفيزياء العامة والحديثة',
            'slug' => 'physics',
            'description' => 'دراسة الكهرومغناطيسية والفيزياء الذرية للمرحلة الثانوية.',
            'sort_order' => 1,
        ]);

        // 4. Create Course, Sessions & Assignments
        $course = Course::create([
            'subject_id' => $subjectPhysics->id,
            'teacher_id' => $teacherProfile->id,
            'grade_level_id' => $gradeHigh->id,
            'title' => 'كورس الفيزياء الكهربية والمغناطيسية الشامل',
            'slug' => 'comprehensive-physics-course',
            'description' => 'كورس تفاعلي يغطي الكهربية التيارية، قانون أوم، وقوانين كيرشوف بالتفصيل.',
            'is_active' => true,
        ]);

        $session1 = CourseSession::create([
            'course_id' => $course->id,
            'title' => 'الجلسة الأولى: مقدمة في التيار الكهربي وقانون أوم',
            'description' => 'شرح مفهوم شدة التيار الكهربي وفرق الجهد والمقاومة الكهربية.',
            'sort_order' => 1,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 60,
            'is_free_demo' => true,
        ]);

        $session2 = CourseSession::create([
            'course_id' => $course->id,
            'title' => 'الجلسة الثانية: قوانين كيرشوف وتطبيقات الدوائر',
            'description' => 'تطبيقات عملي لحل مسائل الدوائر المعقدة باستخدام قانون كيرشوف الأول والثاني.',
            'sort_order' => 2,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration_minutes' => 75,
            'is_free_demo' => false,
        ]);

        $assignment1 = Assignment::create([
            'course_session_id' => $session1->id,
            'title' => 'واجب الجلسة الأولى — مسائل قانون أوم وشبكات المقاومات',
            'description' => 'حل المسائل المرفقة وإعادة رفع ملف الحل للتصحيح.',
            'passing_grade' => 70,
        ]);

        // 5. Enroll Student & Initial Progress
        $enrollment = \App\Models\CourseEnrollment::create([
            'student_user_id' => $studentUser->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        // Initial progress: Session 1 is unlocked for student
        CourseSessionProgress::create([
            'course_enrollment_id' => $enrollment->id,
            'course_session_id' => $session1->id,
            'status' => \App\Enums\SessionProgressStatus::UNLOCKED,
            'unlocked_at' => now(),
        ]);

        // 6. Seed Additional Teachers & Articles
        $this->call([
            TeacherSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}

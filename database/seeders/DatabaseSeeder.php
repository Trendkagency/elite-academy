<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\SessionProgressStatus;
use App\Enums\SubmissionStatus;
use App\Models\AdminProfile;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
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
        // 1. Grade Levels (الصفوف الدراسية)
        $g12 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-12'],
            ['name' => 'الصف الثالث الثانوي', 'sort_order' => 1]
        );
        $g11 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-11'],
            ['name' => 'الصف الثاني الثانوي', 'sort_order' => 2]
        );
        $g10 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-10'],
            ['name' => 'الصف الأول الثانوي', 'sort_order' => 3]
        );

        // 2. Admin User (حساب مدير النظام)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@elite.edu'],
            [
                'name' => 'المدير العام — System Admin',
                'phone' => '+201000000001',
                'password' => bcrypt('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        AdminProfile::updateOrCreate(['user_id' => $adminUser->id]);

        // 3. Parent User & Multi-Child Linking (حساب ولي الأمر والأبناء)
        $parentUser = User::updateOrCreate(
            ['email' => 'parent@elite.edu'],
            [
                'name' => 'أ. خالد محمود — Khaled Mahmoud',
                'phone' => '+201000000004',
                'password' => bcrypt('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $parentProfile = ParentProfile::updateOrCreate(['user_id' => $parentUser->id]);

        // Children Accounts (أحمد، مريم، عمر)
        $ahmed = User::updateOrCreate(
            ['email' => 'ahmed@elite.edu'],
            [
                'name' => 'أحمد خالد — Ahmed Khaled',
                'phone' => '+201000000005',
                'password' => bcrypt('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $ahmedProfile = StudentProfile::updateOrCreate(
            ['user_id' => $ahmed->id],
            ['grade_level_id' => $g12->id, 'school_name' => 'مدرسة المتفوقين للعلوم والتكنولوجيا (STEM)', 'has_used_free_session' => true]
        );

        $mariam = User::updateOrCreate(
            ['email' => 'mariam@elite.edu'],
            [
                'name' => 'مريم خالد — Mariam Khaled',
                'phone' => '+201000000006',
                'password' => bcrypt('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $mariamProfile = StudentProfile::updateOrCreate(
            ['user_id' => $mariam->id],
            ['grade_level_id' => $g11->id, 'school_name' => 'مدرسة النيل الدولية (Nile International)', 'has_used_free_session' => true]
        );

        $omar = User::updateOrCreate(
            ['email' => 'omar@elite.edu'],
            [
                'name' => 'عمر خالد — Omar Khaled',
                'phone' => '+201000000007',
                'password' => bcrypt('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $omarProfile = StudentProfile::updateOrCreate(
            ['user_id' => $omar->id],
            ['grade_level_id' => $g10->id, 'school_name' => 'مدرسة الأورمان النموذجية', 'has_used_free_session' => false]
        );

        // Attach Children to Parent Profile
        $parentProfile->students()->syncWithoutDetaching([
            $ahmed->id => ['relationship' => 'Son'],
            $mariam->id => ['relationship' => 'Daughter'],
            $omar->id => ['relationship' => 'Son'],
        ]);

        // 4. Categories & Subjects (الأقسام والمواد)
        $catScience = Category::updateOrCreate(
            ['slug' => 'natural-sciences'],
            ['name' => 'العلوم الطبيعية والتطبيقية', 'color_theme' => '#0D9488']
        );
        $catMathTech = Category::updateOrCreate(
            ['slug' => 'math-and-tech'],
            ['name' => 'الرياضيات والذكاء الاصطناعي', 'color_theme' => '#2563EB']
        );
        $catLanguages = Category::updateOrCreate(
            ['slug' => 'languages-and-literature'],
            ['name' => 'اللغات والآداب', 'color_theme' => '#EA580C']
        );

        $subPhysics = Subject::updateOrCreate(
            ['slug' => 'physics'],
            ['category_id' => $catScience->id, 'name' => 'الفيزياء العامة والحديثة', 'description' => 'شرح الكهرومغناطيسية والفيزياء الذرية للمرحلة الثانوية.', 'sort_order' => 1]
        );
        $subChem = Subject::updateOrCreate(
            ['slug' => 'chemistry'],
            ['category_id' => $catScience->id, 'name' => 'الكيمياء العضوية والتحليلية', 'description' => 'دراسة تفاعلات المركبات العضوية وتجارب التحليل الكيميائي.', 'sort_order' => 2]
        );
        $subBio = Subject::updateOrCreate(
            ['slug' => 'biology'],
            ['category_id' => $catScience->id, 'name' => 'الأحياء وعلم الجينات', 'description' => 'دراسة الخلية، الحمض النووي DNA، وعلم وظائف الأعضاء.', 'sort_order' => 3]
        );
        $subMath = Subject::updateOrCreate(
            ['slug' => 'mathematics'],
            ['category_id' => $catMathTech->id, 'name' => 'الرياضيات البحتة والتطبيقية', 'description' => 'التفاضل والتكامل والجبر والهندسة الفراغية.', 'sort_order' => 4]
        );
        $subProg = Subject::updateOrCreate(
            ['slug' => 'programming'],
            ['category_id' => $catMathTech->id, 'name' => 'البرمجة والذكاء الاصطناعي', 'description' => 'لغات البرمجة Python والخوارزميات وتعلم الآلة.', 'sort_order' => 5]
        );
        $subEnglish = Subject::updateOrCreate(
            ['slug' => 'english'],
            ['category_id' => $catLanguages->id, 'name' => 'اللغة الإنجليزية وآدابها', 'description' => 'القواعد، البلاغة، القراءة النقدية والتعبير الأكاديمي.', 'sort_order' => 6]
        );
        $subArabic = Subject::updateOrCreate(
            ['slug' => 'arabic'],
            ['category_id' => $catLanguages->id, 'name' => 'اللغة العربية والنحو', 'description' => 'قواعد النحو والصرف، البلاغة والأدب العربي.', 'sort_order' => 7]
        );

        // 5. Seed Teachers
        $this->call(TeacherSeeder::class);

        $teacherDrAhmed = TeacherProfile::where('slug', 'dr-ahmed-mahmoud')->first();
        $teacherSarah = TeacherProfile::where('slug', 'sarah-mohamed')->first();
        $teacherDrOmar = TeacherProfile::where('slug', 'dr-omar-khaled')->first();
        $teacherKareem = TeacherProfile::where('slug', 'eng-kareem-zaki')->first();

        // 6. Seed Courses (المقررات)
        $cPhysics = Course::updateOrCreate(
            ['slug' => 'comprehensive-physics-course'],
            [
                'subject_id' => $subPhysics->id,
                'teacher_id' => $teacherDrAhmed ? $teacherDrAhmed->id : 1,
                'grade_level_id' => $g12->id,
                'title' => 'كورس الفيزياء الكهربية والمغناطيسية الشامل',
                'description' => 'كورس تفاعلي يعالج جميع أفكار الفيزياء الكهربية وقوانين كيرشوف والفيزياء الحديثة بأسلوب مبسط.',
                'is_active' => true,
            ]
        );

        $cChem = Course::updateOrCreate(
            ['slug' => 'organic-chemistry-masterclass'],
            [
                'subject_id' => $subChem->id,
                'teacher_id' => $teacherSarah ? $teacherSarah->id : 1,
                'grade_level_id' => $g12->id,
                'title' => 'كورس الكيمياء العضوية والتحليل الكهربي',
                'description' => 'تغطي الدورة مفاهيم الهيدروكربونات والمشتقات وتطبيقات التحليل الكيميائي التفاعلي.',
                'is_active' => true,
            ]
        );

        $cMath = Course::updateOrCreate(
            ['slug' => 'calculus-and-algebra-mastery'],
            [
                'subject_id' => $subMath->id,
                'teacher_id' => $teacherDrOmar ? $teacherDrOmar->id : 1,
                'grade_level_id' => $g11->id,
                'title' => 'كورس التفاضل والتكامل والجبر للثانوية',
                'description' => 'شرح مكثف لتفاضل الدوال المثلثية، النهايات، وحساب المساحات والحجوم.',
                'is_active' => true,
            ]
        );

        $cProg = Course::updateOrCreate(
            ['slug' => 'fullstack-python-ai-architecture'],
            [
                'subject_id' => $subProg->id,
                'teacher_id' => $teacherKareem ? $teacherKareem->id : 1,
                'grade_level_id' => $g10->id,
                'title' => 'كورس أساسيات البرمجة وتطبيقات الذكاء الاصطناعي',
                'description' => 'تعلم البرمجة بلغة بايثون، تصميم الخوارزميات، وبناء تطبيقات ويب ذكية تفاعلية.',
                'is_active' => true,
            ]
        );

        // 7. Sessions & Assignments (الجلسات والتكليفات)
        $s1 = CourseSession::updateOrCreate(
            ['course_id' => $cPhysics->id, 'sort_order' => 1],
            [
                'title' => 'الجلسة الأولى: التيار الكهربي وقانون أوم',
                'description' => 'شرح مفاهيم الشحنة، فرق الجهد، والمقاومة الكهربية مع تطبيقات الدوائر.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'duration_minutes' => 60,
                'is_free_demo' => true,
            ]
        );
        $s2 = CourseSession::updateOrCreate(
            ['course_id' => $cPhysics->id, 'sort_order' => 2],
            [
                'title' => 'الجلسة الثانية: قوانين كيرشوف والدوائر المركبة',
                'description' => 'تطبيق قانوني كيرشوف لحل مسائل الشبكات الكهربية المعقدة.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'duration_minutes' => 75,
                'is_free_demo' => false,
            ]
        );

        $a1 = Assignment::updateOrCreate(
            ['course_session_id' => $s1->id],
            [
                'title' => 'واجب الجلسة الأولى — تطبيقات قانون أوم وتوصيل المقاومات',
                'description' => 'حل مسائل توصيل التوالي والتوازي وإرسال الإجابات.',
                'passing_grade' => 70,
                'status' => 'published',
            ]
        );

        // 8. Enrollments & Submissions (الاشتراكات والواجبات المنجزة)
        $enrollmentAhmed = CourseEnrollment::updateOrCreate(
            ['student_user_id' => $ahmed->id, 'course_id' => $cPhysics->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );
        $enrollmentMariam = CourseEnrollment::updateOrCreate(
            ['student_user_id' => $mariam->id, 'course_id' => $cMath->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );
        $enrollmentOmar = CourseEnrollment::updateOrCreate(
            ['student_user_id' => $omar->id, 'course_id' => $cProg->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );

        // Progress status for Ahmed
        CourseSessionProgress::updateOrCreate(
            ['course_enrollment_id' => $enrollmentAhmed->id, 'course_session_id' => $s1->id],
            ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now(), 'completed_at' => now()]
        );
        CourseSessionProgress::updateOrCreate(
            ['course_enrollment_id' => $enrollmentAhmed->id, 'course_session_id' => $s2->id],
            ['status' => SessionProgressStatus::UNLOCKED, 'unlocked_at' => now()]
        );

        // Assignment Submissions (تسليم الواجبات)
        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $a1->id, 'student_user_id' => $ahmed->id],
            [
                'course_enrollment_id' => $enrollmentAhmed->id,
                'status' => SubmissionStatus::COMPLETED,
                'grade' => 95,
                'teacher_notes' => 'ممتاز جداً، حل دقيق ومرتب مع توضيح خطوات القوانين.',
                'submitted_at' => now(),
            ]
        );

        // 9. Call ArticleSeeder for Blog Posts
        $this->call(ArticleSeeder::class);
    }
}

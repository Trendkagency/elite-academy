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
use App\Models\ExceptionRequest;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Grade Levels (الصفوف الدراسية المصرية)
        $g12 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-12'],
            ['name' => 'الصف الثالث الثانوي (الثانوية العامة & STEM)', 'sort_order' => 1]
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
            ['grade_level_id' => $g12->id, 'school_name' => 'مدرسة المتفوقين للعلوم والتكنولوجيا (STEM Cairo)', 'has_used_free_session' => true]
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
            ['grade_level_id' => $g11->id, 'school_name' => 'مدرسة النيل الدولية (Nile International School)', 'has_used_free_session' => true]
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
            ['grade_level_id' => $g10->id, 'school_name' => 'مدرسة الأورمان النموذجية لغات', 'has_used_free_session' => false]
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

        // 6. Seed Package Templates (قوالب الباقات والأشراك)
        $pkgPro12 = PackageTemplate::updateOrCreate(
            ['name' => 'باقة التميز الشهري (12 حصة / شهرياً)'],
            ['sessions_count' => 12, 'price' => 450.00, 'description' => 'باقة متكاملة تغطي 12 حصة تفاعلية في الشهر مع متابعة الواجبات واختبارات التقييم.', 'is_active' => true]
        );
        $pkgFull24 = PackageTemplate::updateOrCreate(
            ['name' => 'باقة الثانوية العامة الفائقة (24 حصة + المراجعات النهائية)'],
            ['sessions_count' => 24, 'price' => 800.00, 'description' => 'باقة شاملة لجميع المواد الدراسية مع نماذج امتحانات الثانوية ومتابعة ولي الأمر.', 'is_active' => true]
        );
        $pkgSingle4 = PackageTemplate::updateOrCreate(
            ['name' => 'باقة المادة المنفردة (4 حصص)'],
            ['sessions_count' => 4, 'price' => 200.00, 'description' => 'باقة مخصصة لكورس مادة واحدة لتغطية وحدة دراسية معينة.', 'is_active' => true]
        );

        // 7. Seed Student Packages & Credit Transactions (رصيد محفظة الطلاب)
        $pkgAhmed = StudentPackage::updateOrCreate(
            ['student_user_id' => $ahmed->id],
            [
                'package_template_id' => $pkgPro12->id,
                'total_sessions' => 12,
                'used_sessions' => 4,
                'remaining_sessions' => 8,
                'status' => 'active',
                'activated_at' => now()->subDays(10),
                'expires_at' => now()->addDays(20),
            ]
        );
        PackageTransaction::firstOrCreate([
            'student_package_id' => $pkgAhmed->id,
            'type' => 'payment_activation',
        ], [
            'sessions_delta' => 12,
            'balance_before' => 0,
            'balance_after' => 12,
            'reason' => 'Payment confirmation & package activation',
            'created_at' => now()->subDays(10),
        ]);

        $pkgMariam = StudentPackage::updateOrCreate(
            ['student_user_id' => $mariam->id],
            [
                'package_template_id' => $pkgPro12->id,
                'total_sessions' => 12,
                'used_sessions' => 2,
                'remaining_sessions' => 10,
                'status' => 'active',
                'activated_at' => now()->subDays(5),
                'expires_at' => now()->addDays(25),
            ]
        );

        $pkgOmar = StudentPackage::updateOrCreate(
            ['student_user_id' => $omar->id],
            [
                'package_template_id' => $pkgSingle4->id,
                'total_sessions' => 4,
                'used_sessions' => 1,
                'remaining_sessions' => 3,
                'status' => 'active',
                'activated_at' => now()->subDays(2),
                'expires_at' => now()->addDays(28),
            ]
        );

        // 8. Seed Courses (المقررات الكبرى)
        $cPhysics = Course::updateOrCreate(
            ['slug' => 'comprehensive-physics-course'],
            [
                'subject_id' => $subPhysics->id,
                'teacher_id' => $teacherDrAhmed ? $teacherDrAhmed->id : 1,
                'grade_level_id' => $g12->id,
                'title' => 'كورس الفيزياء الكهربية والمغناطيسية والفيزياء الحديثة',
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
                'title' => 'كورس الكيمياء العضوية والتحليل الكيميائي للثانوية',
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
                'title' => 'كورس التفاضل والتكامل والجبر والهندسة الفراغية',
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

        // 9. Sessions & Assignments (الجلسات والتكليفات)
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
                'course_id' => $cPhysics->id,
                'title' => 'واجب الجلسة الأولى — تطبيقات قانون أوم وتوصيل المقاومات',
                'description' => 'حل مسائل توصيل التوالي والتوازي وإرسال الإجابات.',
                'passing_grade' => 70,
                'passing_score' => 70,
                'duration_minutes' => 30,
                'status' => 'published',
            ]
        );

        $qA1_1 = \App\Models\AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $a1->id, 'sort_order' => 1],
            [
                'question_text' => 'عند توصيل 3 مقاومات متماثلة قيمة كل منها 6 أوم على التوازي، فإن المقاومة المكافئة تكون:',
                'question_type' => 'text',
                'points' => 3.00,
                'is_multiple_choice' => false,
            ]
        );
        \App\Models\AssignmentQuestionOption::updateOrCreate(['question_id' => $qA1_1->id, 'option_text' => '2 أوم (2 Ω)'], ['sort_order' => 1, 'is_correct' => true]);
        \App\Models\AssignmentQuestionOption::updateOrCreate(['question_id' => $qA1_1->id, 'option_text' => '18 أوم (18 Ω)'], ['sort_order' => 2, 'is_correct' => false]);
        \App\Models\AssignmentQuestionOption::updateOrCreate(['question_id' => $qA1_1->id, 'option_text' => '6 أوم (6 Ω)'], ['sort_order' => 3, 'is_correct' => false]);

        $qA1_2 = \App\Models\AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $a1->id, 'sort_order' => 2],
            [
                'question_text' => 'وفقاً لقانون أوم، ما العلاقة بين شدة التيار (I) وفرق الجهد (V) عند ثبوت درجة الحرارة؟',
                'question_type' => 'text',
                'points' => 3.00,
                'is_multiple_choice' => false,
            ]
        );
        \App\Models\AssignmentQuestionOption::updateOrCreate(['question_id' => $qA1_2->id, 'option_text' => 'علاقة طردية خطية'], ['sort_order' => 1, 'is_correct' => true]);
        \App\Models\AssignmentQuestionOption::updateOrCreate(['question_id' => $qA1_2->id, 'option_text' => 'علاقة عكسية'], ['sort_order' => 2, 'is_correct' => false]);
        \App\Models\AssignmentQuestionOption::updateOrCreate(['question_id' => $qA1_2->id, 'option_text' => 'لا توجد علاقة بينهما'], ['sort_order' => 3, 'is_correct' => false]);

        // 10. Enrollments & Progress (الاشتراكات والتقدم الأكاديمي)
        $enrollmentAhmed = CourseEnrollment::updateOrCreate(
            ['student_user_id' => $ahmed->id, 'course_id' => $cPhysics->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );
        $enrollmentMariam = CourseEnrollment::updateOrCreate(
            ['student_user_id' => $mariam->id, 'course_id' => $cChem->id],
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

        // 11. Live Sessions (الحصص المباشرة والروابط - تغطي سيناريوهات منتصف وقت الحصة)
        LiveSession::updateOrCreate(
            ['course_session_id' => $s1->id],
            [
                'title' => 'الجلسة الأولى: التيار الكهربي وقانون أوم وتطبيقات المقاومات',
                'student_user_id' => $ahmed->id,
                'teacher_profile_id' => $teacherDrAhmed ? $teacherDrAhmed->id : 1,
                'subject_id' => $subPhysics->id,
                'course_id' => $cPhysics->id,
                'scheduled_at' => now()->subMinutes(15),
                'start_at' => now()->subMinutes(15),
                'end_at' => now()->addMinutes(45),
                'duration_minutes' => 60,
                'meeting_link' => 'https://zoom.us/j/84920481928?pwd=elite123',
                'status' => 'scheduled',
                'is_free_demo' => true,
            ]
        );

        LiveSession::updateOrCreate(
            ['course_session_id' => $s2->id],
            [
                'title' => 'الجلسة الثانية: قوانين كيرشوف والدوائر الكهربية المركبة',
                'student_user_id' => $ahmed->id,
                'teacher_profile_id' => $teacherDrAhmed ? $teacherDrAhmed->id : 1,
                'subject_id' => $subPhysics->id,
                'course_id' => $cPhysics->id,
                'scheduled_at' => now()->addDay(),
                'start_at' => now()->addDay(),
                'end_at' => now()->addDay()->addMinutes(60),
                'duration_minutes' => 60,
                'meeting_link' => 'https://meet.google.com/future-physics-session',
                'status' => 'scheduled',
                'is_free_demo' => false,
            ]
        );

        // Session 3: Completed Session (Started 2h ago)
        LiveSession::updateOrCreate(
            ['title' => 'الجلسة الأولى: مقدمة الكيمياء العضوية والألكانات'],
            [
                'student_user_id' => $mariam->id,
                'teacher_profile_id' => $teacherSarah ? $teacherSarah->id : 1,
                'subject_id' => $subChem->id,
                'course_id' => $cChem->id,
                'scheduled_at' => now()->subHours(2),
                'start_at' => now()->subHours(2),
                'end_at' => now()->subHour(),
                'duration_minutes' => 60,
                'meeting_link' => 'https://meet.google.com/completed-session',
                'status' => 'completed',
            ]
        );

        // Session 4: Future Scheduled Session (In 5 hours)
        LiveSession::updateOrCreate(
            ['title' => 'الجلسة الثانية: تفاعلات الألكينات وميكانيكية الإضافة'],
            [
                'student_user_id' => $mariam->id,
                'teacher_profile_id' => $teacherSarah ? $teacherSarah->id : 1,
                'subject_id' => $subChem->id,
                'course_id' => $cChem->id,
                'scheduled_at' => now()->addHours(5),
                'start_at' => now()->addHours(5),
                'end_at' => now()->addHours(6),
                'duration_minutes' => 60,
                'meeting_link' => 'https://meet.google.com/future-session',
                'status' => 'scheduled',
            ]
        );

        // 12. Exception Requests (طلبات الاستثناء للأعذار)
        ExceptionRequest::updateOrCreate(
            ['student_user_id' => $ahmed->id, 'reason' => 'ظرف صحي طارئ ومستند طبي مرفق للتأكيد'],
            [
                'course_id' => $cPhysics->id,
                'scope' => 'course',
                'is_global' => false,
                'status' => 'approved',
                'reviewed_at' => now(),
            ]
        );

        // 13. Call CMSSeeder, ArticleSeeder, MSQAssignmentSeeder & TranslationSystemSeeder
        $this->call(CMSSeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(MSQAssignmentSeeder::class);
        $this->call(TranslationSystemSeeder::class);
    }
}

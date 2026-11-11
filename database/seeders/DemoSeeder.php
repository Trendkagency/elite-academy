<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\CourseStatus;
use App\Enums\LiveSessionState;
use App\Enums\MeetingAttendanceStatus;
use App\Enums\MeetingProviderSlug;
use App\Enums\PackageTransactionType;
use App\Enums\PaymentStatus;
use App\Enums\SessionProgressStatus;
use App\Enums\SubmissionStatus;
use App\Models\AdminProfile;
use App\Models\Article;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseSession;
use App\Models\CourseSessionProgress;
use App\Models\ExceptionRequest;
use App\Models\GradeLevel;
use App\Models\HeroSlide;
use App\Models\LiveSession;
use App\Models\MeetingAttendance;
use App\Models\MeetingProvider;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\ParentProfile;
use App\Models\SessionMeeting;
use App\Models\SiteSetting;
use App\Models\StudentEducationalNote;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    /**
     * Run the demo database seeds.
     */
    public function run(): void
    {
        if (! demo_enabled()) {
            $this->command?->error('Cannot run DemoSeeder when DEMO=false in .env. Aborting.');
            return;
        }

        $this->command?->info('🌱 Starting Elite Academy Master Demo Seeder (Trendk Standard)...');

        $demoPassword = config('demo.credentials.password', 'password');
        $hashedDemoPassword = Hash::make($demoPassword);

        // -------------------------------------------------------------
        // 0. Ensure Spatie RBAC Roles Exist
        // -------------------------------------------------------------
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $roleTeacher = Role::firstOrCreate(['name' => 'Teacher', 'guard_name' => 'web']);
        $roleStudent = Role::firstOrCreate(['name' => 'Student', 'guard_name' => 'web']);
        $roleParent = Role::firstOrCreate(['name' => 'Parent', 'guard_name' => 'web']);

        // -------------------------------------------------------------
        // 1. Trendk Standard 3.1: Hidden Super Admin
        // -------------------------------------------------------------
        $supportUser = User::updateOrCreate(
            ['email' => 'support@trendk.com'],
            [
                'name' => 'Trendk Support — الإدارة الفنية والتقنية',
                'phone' => '+201000000000',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        AdminProfile::firstOrCreate(['user_id' => $supportUser->id]);
        $supportUser->syncRoles([$roleSuperAdmin]);

        // -------------------------------------------------------------
        // 2. Demo Target Accounts (@trendk.com)
        // -------------------------------------------------------------
        // Admin (Principal)
        $demoAdmin = User::updateOrCreate(
            ['email' => 'admin@trendk.com'],
            [
                'name' => 'د. عبد الرحمن شاهين — المدير العام الأكاديمي',
                'phone' => '+201000000001',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        AdminProfile::firstOrCreate(['user_id' => $demoAdmin->id]);
        $demoAdmin->syncRoles([$roleSuperAdmin, $roleAdmin]);

        // Teacher (Senior Physics Lead)
        $demoTeacherUser = User::updateOrCreate(
            ['email' => 'teacher@trendk.com'],
            [
                'name' => 'د. طارق الشناوي — كبير معلمي الفيزياء',
                'phone' => '+201000000002',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $demoTeacherUser->syncRoles([$roleTeacher]);
        $demoTeacherProfile = TeacherProfile::updateOrCreate(
            ['slug' => 'demo-teacher-tarek'],
            [
                'user_id' => $demoTeacherUser->id,
                'title' => 'كبير معلمي الفيزياء والفيزياء الحديثة للثانوية العامة',
                'specialization' => 'Physics',
                'bio' => 'معلم أول معتمد لدى أكاديمية النخبة التعليمية، حاصل على دكتوراه في الفيزياء التطبيقية مع أكثر من 18 عاماً في تأهيل أوائل الجمهورية ومدارس المتفوقين STEM.',
                'years_experience' => 18,
                'rating_avg' => 4.98,
                'students_count' => 3850,
                'photo' => 'images/instructor_portrait.png',
                'is_featured' => true,
                'is_public' => true,
            ]
        );

        // Student (Top STEM Student)
        $demoStudentUser = User::updateOrCreate(
            ['email' => 'student@trendk.com'],
            [
                'name' => 'عمر شريف إبراهيم — طالب متفوق (STEM)',
                'phone' => '+201000000003',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $demoStudentUser->syncRoles([$roleStudent]);

        // Parent (Active Guardian)
        $demoParentUser = User::updateOrCreate(
            ['email' => 'parent@trendk.com'],
            [
                'name' => 'م. شريف إبراهيم غالي — ولي أمر الطالب',
                'phone' => '+201000000004',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $demoParentUser->syncRoles([$roleParent]);
        $demoParentProfile = ParentProfile::firstOrCreate(['user_id' => $demoParentUser->id]);

        // Link Demo Parent to Demo Student
        $demoParentProfile->students()->syncWithoutDetaching([
            $demoStudentUser->id => ['relationship' => 'Father'],
        ]);

        // -------------------------------------------------------------
        // 3. Grade Levels (المراحل والصفوف الدراسية المصرية)
        // -------------------------------------------------------------
        $g12 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-12'],
            ['name' => 'الصف الثالث الثانوي (الثانوية العامة & STEM)', 'sort_order' => 1]
        );
        $g11 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-11'],
            ['name' => 'الصف الثاني الثانوي (علمي وأدبي)', 'sort_order' => 2]
        );
        $g10 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-10'],
            ['name' => 'الصف الأول الثانوي', 'sort_order' => 3]
        );
        $g9 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-9'],
            ['name' => 'الصف الثالث الإعدادي (الشهادة الإعدادية)', 'sort_order' => 4]
        );

        $allGrades = collect([$g12, $g11, $g10, $g9]);

        // Student Profile for demo student
        StudentProfile::updateOrCreate(
            ['user_id' => $demoStudentUser->id],
            [
                'grade_level_id' => $g12->id,
                'school_name' => 'مدرسة المتفوقين للعلوم والتكنولوجيا (STEM October)',
                'has_used_free_session' => true,
            ]
        );

        // -------------------------------------------------------------
        // 4. Categories & Subjects (الأقسام والمواد الدراسية)
        // -------------------------------------------------------------
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
            ['name' => 'اللغات والعلوم الإنسانية', 'color_theme' => '#EA580C']
        );

        $subPhysics = Subject::updateOrCreate(
            ['slug' => 'physics'],
            ['category_id' => $catScience->id, 'name' => 'الفيزياء الكهربية والحديثة', 'description' => 'قوانين كيرشوف، الحث الكهرومغناطيسي، الدوائر المهتزة وفيزياء الكم.', 'sort_order' => 1]
        );
        $subChem = Subject::updateOrCreate(
            ['slug' => 'chemistry'],
            ['category_id' => $catScience->id, 'name' => 'الكيمياء العضوية والتحليلية', 'description' => 'الهيدروكربونات، الاتزان الأيوني والكيميائي، والكيمياء الكهربية والتحليلية.', 'sort_order' => 2]
        );
        $subBio = Subject::updateOrCreate(
            ['slug' => 'biology'],
            ['category_id' => $catScience->id, 'name' => 'الأحياء وعلم الوراثة الجزيئية', 'description' => 'المناعة في الكائنات الحية، DNA وRNA، وتخليق البروتين والتكاثر.', 'sort_order' => 3]
        );
        $subMath = Subject::updateOrCreate(
            ['slug' => 'mathematics'],
            ['category_id' => $catMathTech->id, 'name' => 'الرياضيات البحتة (تفاضل وتكامل وجبر)', 'description' => 'اشتقاق وتكامل الدوال الأسية واللوغاريتمية، المصفوفات والهندسة الفراغية.', 'sort_order' => 4]
        );
        $subAppMath = Subject::updateOrCreate(
            ['slug' => 'applied-math'],
            ['category_id' => $catMathTech->id, 'name' => 'الرياضيات التطبيقية (استاتيكا وديناميكا)', 'description' => 'عزوم القوى، الازدواج، قوانين نيوتن، الدفع والتصادم وتعيين طاقة الحركة.', 'sort_order' => 5]
        );
        $subProg = Subject::updateOrCreate(
            ['slug' => 'programming-ai'],
            ['category_id' => $catMathTech->id, 'name' => 'البرمجة وهندسة الذكاء الاصطناعي', 'description' => 'Python للمبتدئين، خوارزميات تعلم الآلة وتطوير تطبيقات الويب الحديثة.', 'sort_order' => 6]
        );
        $subArabic = Subject::updateOrCreate(
            ['slug' => 'arabic'],
            ['category_id' => $catLanguages->id, 'name' => 'اللغة العربية والبلاغة والنقد', 'description' => 'النحو والصرف التفاعلي، مهارات البلاغة الثلاث، والأدب وقراءة النصوص المحررة.', 'sort_order' => 7]
        );
        $subEnglish = Subject::updateOrCreate(
            ['slug' => 'english'],
            ['category_id' => $catLanguages->id, 'name' => 'اللغة الإنجليزية المتقدمة وIELTS', 'description' => 'إتقان القواعد، مهارات المقال الأكاديمي، والمفردات اللغوية واختبارات القبول.', 'sort_order' => 8]
        );

        // -------------------------------------------------------------
        // 5. Teachers Pool (All with @trendk.com)
        // -------------------------------------------------------------
        $teachersData = [
            [
                'email' => 'teacher@trendk.com',
                'name' => 'د. طارق الشناوي',
                'title' => 'كبير معلمي الفيزياء والكهرومغناطيسية للثانوية العامة',
                'spec' => 'Physics',
                'bio' => 'دكتوراه في الفيزياء التطبيقية. 18 عاماً من الخبرة في تبسيط المفاهيم المعقدة وتأهيل أوائل الجمهورية.',
                'photo' => 'images/instructor_portrait.png',
                'exp' => 18,
                'rating' => 4.98,
                'students' => 3850,
            ],
            [
                'email' => 'teacher.sarah@trendk.com',
                'name' => 'أ. سارة عبد المنعم',
                'title' => 'أستاذة الكيمياء العضوية والتحليلية',
                'spec' => 'Chemistry',
                'bio' => 'ماجستير الكيمياء الحيوية. خبرة 14 عاماً في تدريس كيمياء الثانوية العامة واللغات وربطها بالتجارب التفاعلية.',
                'photo' => 'images/instructor_female.png',
                'exp' => 14,
                'rating' => 4.92,
                'students' => 2950,
            ],
            [
                'email' => 'teacher.omar@trendk.com',
                'name' => 'د. عمر عبد العزيز',
                'title' => 'أستاذ الرياضيات البحتة والتطبيقية',
                'spec' => 'Mathematics',
                'bio' => 'دكتوراه في التحليل الرياضي. درّب أوائل الجمهورية في الثانوية العامة وحاصل على جائزة التميز التربوي.',
                'photo' => 'images/instructor_male.png',
                'exp' => 19,
                'rating' => 4.96,
                'students' => 4200,
            ],
            [
                'email' => 'teacher.kareem@trendk.com',
                'name' => 'م. كريم زكي',
                'title' => 'خبير هندسة البرمجيات والذكاء الاصطناعي',
                'spec' => 'Programming',
                'bio' => 'مهندس برمجيات أول ومحاضر دولي معتمد في Python وتطبيقات تعلم الآلة وتدريب الشباب على التقنيات الحديثة.',
                'photo' => 'images/instructor_male.png',
                'exp' => 11,
                'rating' => 4.95,
                'students' => 3100,
            ],
            [
                'email' => 'teacher.mona@trendk.com',
                'name' => 'د. منى رضوان',
                'title' => 'استشارية الأحياء وعلم الوراثة الجزيئية',
                'spec' => 'Biology',
                'bio' => 'دكتوراه في البيولوجيا الجزيئية. شرح تفاعلي بنماذج ثلاثية الأبعاد وخرائط ذهنية مبسطة لـ DNA والمناعة.',
                'photo' => 'images/instructor_female.png',
                'exp' => 15,
                'rating' => 4.90,
                'students' => 2600,
            ],
            [
                'email' => 'teacher.hassan@trendk.com',
                'name' => 'د. حسام الشريف',
                'title' => 'كبير معلمي اللغة الإنجليزية وآدابها',
                'spec' => 'English',
                'bio' => 'حاصل على شهادات تدريس كامبريدج DELTA. متخصص في تدريب مهارات الكتابة والمحادثة والتحضير لاختبارات القبول.',
                'photo' => 'images/instructor_portrait.png',
                'exp' => 16,
                'rating' => 4.88,
                'students' => 2400,
            ],
        ];

        $teacherProfiles = collect([$demoTeacherProfile]);

        for ($i = 1; $i < count($teachersData); $i++) {
            $tData = $teachersData[$i];
            $tUser = User::updateOrCreate(
                ['email' => $tData['email']],
                [
                    'name' => $tData['name'],
                    'phone' => '+2010' . str_pad((string)(20000000 + $i), 8, '0', STR_PAD_LEFT),
                    'password' => $hashedDemoPassword,
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => now(),
                ]
            );
            $tUser->syncRoles([$roleTeacher]);

            $slug = Str::slug($tData['email']);
            $tProfile = TeacherProfile::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $tUser->id,
                    'title' => $tData['title'],
                    'specialization' => $tData['spec'],
                    'bio' => $tData['bio'],
                    'years_experience' => $tData['exp'],
                    'rating_avg' => $tData['rating'],
                    'students_count' => $tData['students'],
                    'photo' => $tData['photo'],
                    'is_featured' => true,
                    'is_public' => true,
                ]
            );

            $teacherProfiles->push($tProfile);
        }

        // -------------------------------------------------------------
        // 6. Realistic Package Templates (Accounting & Pricing Model)
        // -------------------------------------------------------------
        $packagesDefinitions = [
            [
                'name' => 'باقة المراجعة المركزة (4 حصص تفاعلية)',
                'sessions_count' => 4,
                'price' => 450.00,
                'description' => 'باقة مكثفة مخصصة للمراجعات النهائية قبل الامتحانات الشهرية وحل بنوك الأسئلة الوزارية.',
            ],
            [
                'name' => 'باقة الشهر الدراسي الأساسية (8 حصص تفاعلية)',
                'sessions_count' => 8,
                'price' => 850.00,
                'description' => 'تغطي الحصص الأسبوعية المباشرة للمادة مع التقييم التلقائي للواجبات ومتابعة ولي الأمر اللحظية.',
            ],
            [
                'name' => 'باقة الفصل الدراسي المتكاملة (16 حصة + نماذج الامتحانات)',
                'sessions_count' => 16,
                'price' => 1600.00,
                'description' => 'الباقة الأكثر طلباً لطلاب الثانوية العامة تشمل المحاضرات والمذكرات التفاعلية وورش العمل الدورية.',
            ],
            [
                'name' => 'باقة النخبة السنوية الشاملة (32 حصة + المراجعات النهائية)',
                'sessions_count' => 32,
                'price' => 3000.00,
                'description' => 'تأهيل كامل للعام الدراسي حتى ليلة الامتحان مع متابعة أسبوعية خاصة وتقارير أداء دورية لولي الأمر.',
            ],
            [
                'name' => 'مسار هندسة البرمجيات والذكاء الاصطناعي (16 حصة عملية)',
                'sessions_count' => 16,
                'price' => 1200.00,
                'description' => 'تطوير مشاريع حقيقية بلغة Python وتطبيقات الذكاء الاصطناعي مع إشراف مهندسين معتمدين.',
            ],
            [
                'name' => 'باقة الدعم الفردي VIP 1-on-1 (6 حصص خاصة)',
                'sessions_count' => 6,
                'price' => 1500.00,
                'description' => 'جلسات خصوصية فردية مباشرة عبر الفيديو مع كبير المعلمين للإجابة عن أدق التساؤلات وسد الفجوات التعليمية.',
            ],
        ];

        $createdPackages = collect();
        foreach ($packagesDefinitions as $pDef) {
            $pkg = PackageTemplate::updateOrCreate(
                ['name' => $pDef['name']],
                [
                    'sessions_count' => $pDef['sessions_count'],
                    'price' => $pDef['price'],
                    'description' => $pDef['description'],
                    'is_active' => true,
                ]
            );
            $createdPackages->push($pkg);
        }

        // -------------------------------------------------------------
        // 7. Student Packages & Realistic Accounting Ledgers
        // -------------------------------------------------------------
        $termPackage = $createdPackages->where('sessions_count', 16)->first() ?? $createdPackages->first();
        $studentPkg = StudentPackage::updateOrCreate(
            ['student_user_id' => $demoStudentUser->id],
            [
                'package_template_id' => $termPackage->id,
                'total_sessions' => 16,
                'used_sessions' => 3,
                'remaining_sessions' => 13,
                'status' => 'active',
                'activated_at' => now()->subDays(14),
                'expires_at' => now()->addDays(45),
            ]
        );

        // Clean slate for demo transactions to avoid duplication
        PackageTransaction::where('student_package_id', $studentPkg->id)->delete();

        $ledgerEntries = [
            [
                'type' => 'payment_activation',
                'sessions_delta' => 16,
                'balance_before' => 0,
                'balance_after' => 16,
                'reason' => 'شحن وتفعيل باقة الفصل الدراسي المتكاملة (16 حصة) عبر InstaPay (مرجع: IPN-9842104)',
                'created_at' => now()->subDays(14),
            ],
            [
                'type' => 'session_deduct',
                'sessions_delta' => -1,
                'balance_before' => 16,
                'balance_after' => 15,
                'reason' => 'خصم حصة: فيزياء — التيار الكهربي وقانون أوم وتطبيقات كيرشوف',
                'created_at' => now()->subDays(11),
            ],
            [
                'type' => 'session_deduct',
                'sessions_delta' => -1,
                'balance_before' => 15,
                'balance_after' => 14,
                'reason' => 'خصم حصة: كيمياء — تسمية وتفاعلات الهيدروكربونات العضوية',
                'created_at' => now()->subDays(8),
            ],
            [
                'type' => 'session_deduct',
                'sessions_delta' => -1,
                'balance_before' => 14,
                'balance_after' => 13,
                'reason' => 'خصم حصة: رياضيات — نهايات الدوال والاتصال وحساب التفاضل',
                'created_at' => now()->subDays(6),
            ],
            [
                'type' => 'session_refund',
                'sessions_delta' => 1,
                'balance_before' => 13,
                'balance_after' => 14,
                'reason' => 'استرداد رصيد حصة: اعتماد عذر غياب رسمي معتمد من إدارة الأكاديمية',
                'created_at' => now()->subDays(4),
            ],
            [
                'type' => 'session_deduct',
                'sessions_delta' => -1,
                'balance_before' => 14,
                'balance_after' => 13,
                'reason' => 'خصم حصة: فيزياء — الحث الكهرومغناطيسي وقاعدة لنز والمحول الكهربي',
                'created_at' => now()->subDays(2),
            ],
        ];

        foreach ($ledgerEntries as $entry) {
            PackageTransaction::create(array_merge($entry, [
                'student_package_id' => $studentPkg->id,
                'performed_by' => $demoAdmin->id,
            ]));
        }

        // -------------------------------------------------------------
        // 8. Meeting Provider
        // -------------------------------------------------------------
        $zoomProvider = MeetingProvider::updateOrCreate(
            ['slug' => 'zoom'],
            [
                'name' => 'Zoom Meetings Pro',
                'is_active' => true,
                'supports_embedding' => true,
                'encrypted_settings' => ['encrypted' => true, 'watermark' => true],
            ]
        );

        // -------------------------------------------------------------
        // 9. Comprehensive Courses & Sessions
        // -------------------------------------------------------------
        $coursesCatalog = [
            [
                'slug' => 'physics-electricity-magnetism-g12',
                'title' => 'الفيزياء الكهربية والمغناطيسية والفيزياء الحديثة — الثانوية العامة',
                'subject' => $subPhysics,
                'teacher' => $demoTeacherProfile,
                'grade' => $g12,
                'description' => 'شرح تفصيلي معتمد لكافة وحدات الفيزياء الكهربية، قوانين كيرشوف، الحث الكهرومغناطيسي، الدوائر المترددة والفيزياء النووية مع حل نماذج الامتحانات الوزارية السابقة.',
                'sessions' => [
                    ['title' => 'المحاضرة 1: شدة التيار، فرق الجهد، والمقاومة وقانون أوم للدائرة المغلقة', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 2: توصيل المقاومات على التوالي والتوازي وقوانين كيرشوف المعقدة', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 3: التأثير المغناطيسي للتيار الكهربي والقوة وعزم الازدواج المغناطيسي', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 4: الحث الكهرومغناطيسي، قانون فاراداي، وقاعدة لنز والمولد الكهربي', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 5: المحول الكهربي والمحرك، ودوائر التيار المتردد والرنين', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 6: مقدمة الفيزياء الحديثة: إشعاع الجسم الأسود والتأثير الكهروضوئي', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                ],
            ],
            [
                'slug' => 'organic-chemistry-mastery-g12',
                'title' => 'الكيمياء العضوية والتحليلية التفاعلية — الثانوية العامة',
                'subject' => $subChem,
                'teacher' => $teacherProfiles->get(1) ?? $demoTeacherProfile,
                'grade' => $g12,
                'description' => 'دراسة الهيدروكربونات الأليفاتية والأروماتية، مشتقات الهيدروكربونات، الكحولات والفينولات، مع خرائط مفاهيمية وحل مسائل الاتزان والأكسدة والاختزال.',
                'sessions' => [
                    ['title' => 'المحاضرة 1: مدخل الكيمياء العضوية، الألكانات وتسمية IUPAC', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 2: الألكينات والألكاينات وتفاعلات الإضافة وقاعدة ماركونيكوف', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 3: البنزين العطري والتفاعلات الأروماتية وتوجيه المجموعات', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 4: الكحولات والفينولات واستخداماتها الصناعية والتفاعلات المميزة', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                ],
            ],
            [
                'slug' => 'calculus-algebra-excellence-g12',
                'title' => 'التفاضل والتكامل والجبر والهندسة الفراغية — الثانوية العامة',
                'subject' => $subMath,
                'teacher' => $teacherProfiles->get(2) ?? $demoTeacherProfile,
                'grade' => $g12,
                'description' => 'اشتقاق الدوال المثلثية والأسية، تطبيقات القيم العظمى والصغرى، التكامل بالتجزيء والتعويض، والمحددات والمصفوفات ومعادلة الكرة والمستقيم في الفراغ.',
                'sessions' => [
                    ['title' => 'المحاضرة 1: اشتقاق الدوال المثلثية والبارامترية والاشتقاق الضمني', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 2: نهايات الدوال المرتبطة بالعدد الطبيعي e والمشتقات العليا', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 3: طرق التكامل المتقدمة (التجزيء والكسور الجزئية)', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 4: الجبر الخطي: المصفوفات والمحددات ونظرية كرامر', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                ],
            ],
            [
                'slug' => 'python-ai-future-engineers',
                'title' => 'مسار هندسة البرمجيات والذكاء الاصطناعي العملي',
                'subject' => $subProg,
                'teacher' => $teacherProfiles->get(3) ?? $demoTeacherProfile,
                'grade' => $g11,
                'description' => 'بناء مشاريع عملية بلغة Python، أساسيات الخوارزميات، هياكل البيانات، ومقدمة تطبيقية لنماذج تعلم الآلة ومعالجة اللغات الطبيعية.',
                'sessions' => [
                    ['title' => 'المحاضرة 1: بيئة العمل في Python، المتغيرات، والجمل الشرطية والحلقات', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 2: الدوال، البرمجة كائنية التوجه (OOP)، والتعامل مع الملفات', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 3: تحليل البيانات باستخدام NumPy وPandas وتصور البيانات', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 4: بناء أول نموذج ذكاء اصطناعي تصنيفي باستخدام Scikit-Learn', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                ],
            ],
            [
                'slug' => 'molecular-biology-genetics-g12',
                'title' => 'الأحياء وعلم الوراثة الجزيئية والمناعة — الثانوية العامة',
                'subject' => $subBio,
                'teacher' => $teacherProfiles->get(4) ?? $demoTeacherProfile,
                'grade' => $g12,
                'description' => 'دراسة شاملة للجهاز المناعي في الإنسان، تركيب DNA وتضاعفه، تخليق البروتين والهندسة الوراثية وتطبيقات التكنولوجيا الحيوية.',
                'sessions' => [
                    ['title' => 'المحاضرة 1: تركيب الجهاز المناعي والخلايا الليمفاوية والأجسام المضادة', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 2: آليات عمل الجهاز المناعي والاستجابة المناعية الخلوية والخلطية', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 3: التركيب الجزيئي لـ DNA وتضاعفه وإصلاح عيوب المادة الوراثية', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 4: أنواع RNA وتخليق البروتين وطفرات الجينات والهندسة الوراثية', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                ],
            ],
            [
                'slug' => 'advanced-english-academic-writing',
                'title' => 'اللغة الإنجليزية المتقدمة ومهارات الكتابة الأكاديمية للثانوية',
                'subject' => $subEnglish,
                'teacher' => $teacherProfiles->get(5) ?? $demoTeacherProfile,
                'grade' => $g12,
                'description' => 'تطوير المفردات المتقدمة والقواعد المركبة، احتراف كتابة المقال الأكاديمي، وحل قطع الفهم المحررة ومواكبة أحدث مواصفات الورقة الامتحانية.',
                'sessions' => [
                    ['title' => 'المحاضرة 1: Advanced Inversion, Conditionals, and Complex Syntax', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 2: Academic Essay Structure & Argumentative Writing Mastery', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                    ['title' => 'المحاضرة 3: Reading Comprehension Strategies & Contextual Vocabulary Clues', 'video' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4'],
                ],
            ],
        ];

        $createdCourses = collect();

        foreach ($coursesCatalog as $cData) {
            $course = Course::updateOrCreate(
                ['slug' => $cData['slug']],
                [
                    'subject_id' => $cData['subject']->id,
                    'teacher_id' => $cData['teacher']->id,
                    'grade_level_id' => $cData['grade']->id,
                    'title' => $cData['title'],
                    'description' => $cData['description'],
                    'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
                    'is_active' => true,
                    'has_free_demo' => true,
                    'is_accredited' => true,
                    'sessions_count' => count($cData['sessions']),
                    'session_duration_minutes' => 60,
                ]
            );
            $createdCourses->push($course);

            foreach ($cData['sessions'] as $idx => $sInfo) {
                $cSession = CourseSession::updateOrCreate(
                    ['course_id' => $course->id, 'sort_order' => $idx + 1],
                    [
                        'title' => $sInfo['title'],
                        'description' => "محتوى وشرح تفصيلي مع تدريبات تطبيقية للمحاضرة رقم " . ($idx + 1),
                        'video_url' => $sInfo['video'],
                        'duration_minutes' => 60,
                        'is_free_demo' => ($idx === 0),
                    ]
                );

                // Add Assignment for every session
                $assignment = Assignment::updateOrCreate(
                    ['course_session_id' => $cSession->id],
                    [
                        'course_id' => $course->id,
                        'teacher_profile_id' => $course->teacher_id,
                        'title' => 'واجب تفاعلي واختبار تقييمي: ' . $sInfo['title'],
                        'description' => 'اختبار إلكتروني لقياس استيعاب المفاهيم الأساسية ومهارات التفكير العليا.',
                        'total_questions' => 2,
                        'passing_score' => 70,
                        'duration_minutes' => 45,
                        'due_at' => now()->addDays(7),
                        'status' => 'published',
                    ]
                );

                // Add 2 realistic questions
                $q1 = AssignmentQuestion::updateOrCreate(
                    ['assignment_id' => $assignment->id, 'sort_order' => 1],
                    [
                        'question_text' => 'وفقاً للمبادئ والقوانين الفيزيائية المشروحة، ما العلاقة التي تربط المتغيرات الأساسية عند ثبوت درجة الحرارة؟',
                        'question_type' => 'text',
                        'points' => 10.00,
                        'is_multiple_choice' => true,
                    ]
                );
                AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'sort_order' => 1], ['option_text' => 'يتناسب طردياً مع القوة الدافعة الكهربية وعكسياً مع المقاومة الكلية (قانون أوم)', 'is_correct' => true]);
                AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'sort_order' => 2], ['option_text' => 'يتناسب عكسياً مع التردد وطردياً مع السعة الكهربية', 'is_correct' => false]);
                AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'sort_order' => 3], ['option_text' => 'يظل ثابتاً ولا يتأثر بتغير المقاومة الخارجية للدائرة', 'is_correct' => false]);

                $q2 = AssignmentQuestion::updateOrCreate(
                    ['assignment_id' => $assignment->id, 'sort_order' => 2],
                    [
                        'question_text' => 'ما هي وحدة القياس الدولية المعيارية (SI) المعتمدة للكمية الفيزيائية المستنتجة من القانون؟',
                        'question_type' => 'text',
                        'points' => 10.00,
                        'is_multiple_choice' => true,
                    ]
                );
                AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'sort_order' => 1], ['option_text' => 'فولت / أمبير (أوم Ω المعياري)', 'is_correct' => true]);
                AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'sort_order' => 2], ['option_text' => 'تسلا . متر مربع (ويبر Wb)', 'is_correct' => false]);
                AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'sort_order' => 3], ['option_text' => 'هنري / متر (معامل النفاذية)', 'is_correct' => false]);
            }
        }

        // -------------------------------------------------------------
        // 10. Live Sessions (Live Now, Upcoming, Completed)
        // -------------------------------------------------------------
        $primaryCourse = $createdCourses->first();
        if ($primaryCourse) {
            $sess1 = $primaryCourse->sessions()->first();
            $sess2 = $primaryCourse->sessions()->skip(1)->first();
            $sess3 = $primaryCourse->sessions()->skip(2)->first();

            // 1) Live Session happening RIGHT NOW
            $liveNow = LiveSession::updateOrCreate(
                ['title' => 'بث مباشر تفاعلي: حل تدريبات ومسائل الفيزياء المتقدمة (مباشر الآن)'],
                [
                    'course_session_id' => $sess2?->id,
                    'student_user_id' => $demoStudentUser->id,
                    'teacher_profile_id' => $demoTeacherProfile->id,
                    'subject_id' => $subPhysics->id,
                    'course_id' => $primaryCourse->id,
                    'scheduled_at' => now()->subMinutes(15),
                    'start_at' => now()->subMinutes(15),
                    'end_at' => now()->addMinutes(45),
                    'duration_minutes' => 60,
                    'meeting_link' => 'https://zoom.us/j/84920481928?pwd=elite-demo',
                    'status' => 'live',
                    'lifecycle_state' => LiveSessionState::LIVE->value,
                    'is_free_demo' => false,
                ]
            );

            MeetingAttendance::updateOrCreate(
                ['live_session_id' => $liveNow->id, 'student_user_id' => $demoStudentUser->id],
                [
                    'status' => 'present',
                    'joined_at' => now()->subMinutes(12),
                    'duration_seconds' => 720,
                    'provider_slug' => 'zoom',
                ]
            );

            // 2) Upcoming Live Session (Tomorrow)
            LiveSession::updateOrCreate(
                ['title' => 'ورشة المراجعة الشاملة ومسائل التفكير العليا (الحث الكهرومغناطيسي)'],
                [
                    'course_session_id' => $sess3?->id,
                    'student_user_id' => $demoStudentUser->id,
                    'teacher_profile_id' => $demoTeacherProfile->id,
                    'subject_id' => $subPhysics->id,
                    'course_id' => $primaryCourse->id,
                    'scheduled_at' => now()->addDay()->setHour(18)->setMinute(0),
                    'start_at' => now()->addDay()->setHour(18)->setMinute(0),
                    'end_at' => now()->addDay()->setHour(19)->setMinute(30),
                    'duration_minutes' => 90,
                    'meeting_link' => 'https://meet.google.com/elite-live-session',
                    'status' => 'scheduled',
                    'lifecycle_state' => LiveSessionState::BEFORE_JOINABLE->value,
                    'is_free_demo' => false,
                ]
            );

            // 3) Completed Live Session
            $completedSession = LiveSession::updateOrCreate(
                ['title' => 'محاضرة تأسيسية: قوانين كيرشوف وقواعد حل الدوائر المتفرعة (مسجلة)'],
                [
                    'course_session_id' => $sess1?->id,
                    'student_user_id' => $demoStudentUser->id,
                    'teacher_profile_id' => $demoTeacherProfile->id,
                    'subject_id' => $subPhysics->id,
                    'course_id' => $primaryCourse->id,
                    'scheduled_at' => now()->subDays(3),
                    'start_at' => now()->subDays(3),
                    'end_at' => now()->subDays(3)->addMinutes(60),
                    'duration_minutes' => 60,
                    'meeting_link' => 'https://meet.google.com/elite-completed-archive',
                    'status' => 'completed',
                    'lifecycle_state' => LiveSessionState::ENDED->value,
                    'is_free_demo' => true,
                ]
            );

            MeetingAttendance::updateOrCreate(
                ['live_session_id' => $completedSession->id, 'student_user_id' => $demoStudentUser->id],
                [
                    'status' => 'present',
                    'joined_at' => now()->subDays(3)->addMinutes(2),
                    'duration_seconds' => 3480,
                    'provider_slug' => 'zoom',
                ]
            );
        }

        // -------------------------------------------------------------
        // 11. Enrollments, Progress & Graded Submissions for Demo Student
        // -------------------------------------------------------------
        foreach ($createdCourses->take(3) as $cIdx => $enrolledCourse) {
            $enrollment = CourseEnrollment::updateOrCreate(
                ['student_user_id' => $demoStudentUser->id, 'course_id' => $enrolledCourse->id],
                ['status' => 'active', 'enrolled_at' => now()->subDays(15 - ($cIdx * 3))]
            );

            $cSessions = $enrolledCourse->sessions()->orderBy('sort_order')->take(3)->get();
            foreach ($cSessions as $sIdx => $cSess) {
                if ($sIdx === 0) {
                    CourseSessionProgress::updateOrCreate(
                        ['course_enrollment_id' => $enrollment->id, 'course_session_id' => $cSess->id],
                        ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(10), 'completed_at' => now()->subDays(8)]
                    );

                    $assign = $cSess->assignments()->first();
                    if ($assign) {
                        AssignmentSubmission::updateOrCreate(
                            ['assignment_id' => $assign->id, 'student_user_id' => $demoStudentUser->id],
                            [
                                'course_enrollment_id' => $enrollment->id,
                                'status' => SubmissionStatus::COMPLETED,
                                'score' => 20,
                                'grade' => 98,
                                'percentage' => 98.0,
                                'total_points' => 20,
                                'passing_score' => 70,
                                'teacher_notes' => 'إجابة نموذجية ممتازة وتطبيق رائع لقوانين الدوائر الكهربية. استمر في هذا المستوى المتميز.',
                                'submitted_at' => now()->subDays(8),
                            ]
                        );
                    }
                } elseif ($sIdx === 1) {
                    CourseSessionProgress::updateOrCreate(
                        ['course_enrollment_id' => $enrollment->id, 'course_session_id' => $cSess->id],
                        ['status' => SessionProgressStatus::UNLOCKED, 'unlocked_at' => now()->subDays(3)]
                    );

                    $assign = $cSess->assignments()->first();
                    if ($assign) {
                        AssignmentSubmission::updateOrCreate(
                            ['assignment_id' => $assign->id, 'student_user_id' => $demoStudentUser->id],
                            [
                                'course_enrollment_id' => $enrollment->id,
                                'status' => SubmissionStatus::SUBMITTED,
                                'grade' => null,
                                'score' => null,
                                'total_points' => 20,
                                'passing_score' => 70,
                                'teacher_notes' => null,
                                'submitted_at' => now()->subHours(4),
                            ]
                        );
                    }
                } else {
                    CourseSessionProgress::updateOrCreate(
                        ['course_enrollment_id' => $enrollment->id, 'course_session_id' => $cSess->id],
                        ['status' => SessionProgressStatus::LOCKED]
                    );
                }
            }
        }

        // Add Educational Note from Teacher
        StudentEducationalNote::flushEventListeners();
        StudentEducationalNote::updateOrCreate(
            ['student_user_id' => $demoStudentUser->id, 'teacher_profile_id' => $demoTeacherProfile->id],
            [
                'category' => 'academic_excellence',
                'note' => 'الطالب عمر يظهر شغفاً وتفوقاً استثنائياً في حل مسائل كيرشوف والدوائر المعقدة. تم ترشيحه للمشاركة في تصفيات أولمبياد الفيزياء الوطني.',
                'created_at' => now()->subDays(2),
            ]
        );

        // Add Approved Exception Request (Absence excuse)
        ExceptionRequest::flushEventListeners();
        ExceptionRequest::updateOrCreate(
            ['student_user_id' => $demoStudentUser->id, 'reason' => 'عذر طبي رسمي: وعكة صحية طارئة مع تقرير معتمد'],
            [
                'status' => 'approved',
                'admin_notes' => 'تمت الموافقة من الإدارة وإعادة رصيد الحصة لمحفظة الطالب تلقائياً.',
                'reviewed_by' => $demoAdmin->id,
                'reviewed_at' => now()->subDays(4),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ]
        );

        // -------------------------------------------------------------
        // 12. Additional Students & Parents (@trendk.com)
        // -------------------------------------------------------------
        $studentNames = [
            'كريم حسام الدين', 'نور الهدى أحمد', 'زياد طارق الشناوي', 'هنا مصطفى كامل',
            'عمر عبد الرحمن محمود', 'سلمى شريف إبراهيم', 'مروان خالد عبد الله', 'فرح ياسر جلال',
            'حمزة وائل توفيق', 'لينا أشرف عبد الفتاح', 'يحيى ماجد الصاوي', 'جنى تامر رضوان',
            'أحمد باسم عبد الخالق', 'رنا هاني سراج', 'مصطفى علاء الديب', 'ملك أسامة الشامي',
            'بلال رامي عاشور', 'ندى سمير فهمي', 'حازم شادي قنديل', 'مايا وليد زكريا',
        ];

        $parentNames = [
            'أ. حسام الدين عبد السلام', 'أ. أحمد علي بركات', 'أ. طارق الشناوي', 'أ. مصطفى كامل عبد الهادي',
            'أ. عبد الرحمن محمود فريد', 'أ. شريف إبراهيم غالي', 'أ. خالد عبد الله سويلم', 'أ. ياسر جلال العزازي',
            'أ. وائل توفيق هلال', 'أ. أشرف عبد الفتاح رضوان', 'أ. ماجد الصاوي الدسوقي', 'أ. تامر رضوان شلبي',
        ];

        $createdStudents = collect([$demoStudentUser]);
        foreach ($studentNames as $i => $sName) {
            $num = $i + 1;
            $sEmail = "student{$num}@trendk.com";
            $stUser = User::updateOrCreate(
                ['email' => $sEmail],
                [
                    'name' => $sName,
                    'phone' => '+2011' . str_pad((string)(30000000 + $num), 8, '0', STR_PAD_LEFT),
                    'password' => $hashedDemoPassword,
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => now(),
                ]
            );
            $stUser->syncRoles([$roleStudent]);

            $grade = $allGrades->get($i % $allGrades->count());
            StudentProfile::updateOrCreate(
                ['user_id' => $stUser->id],
                [
                    'grade_level_id' => $grade->id,
                    'school_name' => 'مدرسة النيل الدولية للغات',
                    'has_used_free_session' => ($i % 2 === 0),
                ]
            );

            $createdStudents->push($stUser);
        }

        foreach ($parentNames as $j => $pName) {
            $pNum = $j + 1;
            $pEmail = "parent{$pNum}@trendk.com";
            $pUser = User::updateOrCreate(
                ['email' => $pEmail],
                [
                    'name' => $pName,
                    'phone' => '+2012' . str_pad((string)(40000000 + $pNum), 8, '0', STR_PAD_LEFT),
                    'password' => $hashedDemoPassword,
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => now(),
                ]
            );
            $pUser->syncRoles([$roleParent]);

            $pProfile = ParentProfile::firstOrCreate(['user_id' => $pUser->id]);

            $child = $createdStudents->get($pNum % $createdStudents->count());
            if ($child && $child->id !== $demoStudentUser->id) {
                $pProfile->students()->syncWithoutDetaching([
                    $child->id => ['relationship' => ($j % 2 === 0 ? 'Father' : 'Mother')],
                ]);
            }
        }

        // -------------------------------------------------------------
        // 13. Articles & Academic Insights
        // -------------------------------------------------------------
        $articles = [
            [
                'title' => 'استراتيجيات التفوق في امتحانات الثانوية العامة وفق النظام الحديث',
                'category' => 'إرشادات أكاديمية',
                'excerpt' => 'دليلك المتكامل لإدارة الوقت في المذاكرة والتعامل مع أسئلة مهارات التفكير العليا في الفيزياء والرياضيات.',
                'content' => "النجاح في الثانوية العامة لم يعد معتمداً على الحفظ المجرد، بل على الفهم العميق وربط المفاهيم الفيزيائية والرياضية بنماذج تطبيقية.\n\nخطوات عملية للتفوق:\n1. التكرار المتباعد للمفاهيم الأساسية.\n2. حل نماذج بنوك الأسئلة الوزارية وتوثيق الأخطاء في كراس خاص.\n3. حضور الحلقات التفاعلية المباشرة ومناقشة المعلم في خطوات التفكير.",
                'read_time' => 5,
            ],
            [
                'title' => 'أهمية تعلم لغة بايثون ومفاهيم الذكاء الاصطناعي لطلاب المدارس',
                'category' => 'تكنولوجيا المستقبل',
                'excerpt' => 'كيف تؤهل أبناءك لسوق العمل الحديث من خلال البرمجة التفاعلية والتفكير الخوارزمي المنطقي.',
                'content' => "البرمجة أصبحت مهارة أساسية كالقراءة والكتابة. تعلم لغة Python يفتح آفاقاً واسعة للطلاب لبناء تطبيقات حقيقية ونماذج ذكاء اصطناعي تفيدهم في دراستهم الجامعية ومستقبلهم المهني.",
                'read_time' => 7,
            ],
            [
                'title' => 'دليل ولي الأمر: المتابعة الإيجابية للتحصيل الدراسي دون ضغط نفسي',
                'category' => 'إرشاد أولياء الأمور',
                'excerpt' => 'نصائح عملية للأمهات والآباء لمتابعة تقارير الحضور والدرجات عبر البوابة ودعم الطالب نفسياً.',
                'content' => "الدعم النفسي الإيجابي هو الركيزة الأساسية لثقة الطالب في قدراته. من خلال بوابة ولي الأمر، يمكنك الاطلاع على درجات الواجبات ونسبة الحضور لحظياً وتقديم التشجيع المناسب.",
                'read_time' => 4,
            ],
            [
                'title' => 'أسرار إتقان الكيمياء العضوية وتخطي عقبة شبكة التفاعلات',
                'category' => 'العلوم التطبيقية',
                'excerpt' => 'طرق مبتكرة لربط المجموعات الوظيفية والهيدروكربونات باستخدام الخرائط الذهنية التفاعلية.',
                'content' => "الكيمياء العضوية مادة ممتعة عندما يتم التعامل معها كقصة منطقية مترابطة. ابدأ دائماً برسم شبكة العلاقات بين المجموعات الوظيفية وميكانيكية الإضافة والاستبدال.",
                'read_time' => 6,
            ],
        ];

        foreach ($articles as $aIdx => $aData) {
            $slug = 'elite-article-' . ($aIdx + 1);
            Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'author_user_id' => $demoAdmin->id,
                    'title' => $aData['title'],
                    'category' => $aData['category'],
                    'excerpt' => $aData['excerpt'],
                    'content' => $aData['content'],
                    'image' => 'images/course_ai.png',
                    'read_time_minutes' => $aData['read_time'],
                    'is_published' => true,
                    'published_at' => now()->subDays($aIdx * 2 + 1),
                ]
            );
        }

        // -------------------------------------------------------------
        // 14. Hero Slides (Elite Academy Prestige Brand)
        // -------------------------------------------------------------
        $slides = [
            [
                'title' => 'أكاديمية النخبة التعليمية: استثمر في تفوقك الدراسي مع كبار معلمي الجمهورية',
                'subtitle' => 'منظومة تعليمية تفاعلية معتمدة تجمع بين الحصص المباشرة عالية الدقة، والتقييم الآلي للواجبات، ومتابعة ولي الأمر اللحظية.',
                'track' => '🏛️ الصرح التعليمي الرقمي الأقوى في مصر',
                'image' => 'images/hero_student.png',
                'cta1' => '/subjects',
                'cta2' => '/register',
            ],
            [
                'title' => 'مسارات البرمجة والذكاء الاصطناعي وبناء مهندسي الغد',
                'subtitle' => 'اكتسب مهارات العصر الرقمي وابنِ مشاريعك الأولى بلغة بايثون وتطبيقات تعلم الآلة تحت إشراف مهندسين معتمدين.',
                'track' => '🧠 مسار البرمجة والتكنولوجيا المتقدمة',
                'image' => 'images/course_ai.png',
                'cta1' => '/courses',
                'cta2' => '/subjects',
            ],
            [
                'title' => 'فصول افتراضية تفاعلية ودعم أكاديمي مباشر وشامل',
                'subtitle' => 'تفاعل صوتاً وصورة مع أفضل معلمي مصر مع تسجيلات دائمة ومذكرات بصيغة PDF واختبارات قياس مستوى دورية.',
                'track' => '🎯 تجربة تعليمية رائدة لأوائل الجمهورية',
                'image' => 'images/academy_campus.png',
                'cta1' => '/login',
                'cta2' => '/register',
            ],
        ];

        foreach ($slides as $sIdx => $sData) {
            HeroSlide::updateOrCreate(
                ['sort_order' => $sIdx + 1],
                [
                    'title' => $sData['title'],
                    'subtitle' => $sData['subtitle'],
                    'track_label' => $sData['track'],
                    'image' => $sData['image'],
                    'cta_primary_url' => $sData['cta1'],
                    'cta_secondary_url' => $sData['cta2'],
                    'is_active' => true,
                ]
            );
        }

        // -------------------------------------------------------------
        // 15. Site Settings (Prestigious Educational Institution)
        // -------------------------------------------------------------
        $settings = [
            'site_name' => 'أكاديمية النخبة التعليمية',
            'site_tagline' => 'نحو تفوق دراسي حقيقي ومستقبل تعليمي رائد',
            'contact_hero_badge' => 'خدمة الطلاب وأولياء الأمور 24/7',
            'contact_hero_title' => 'نحن هنا دائماً لدعم مسيرتك التعليمية',
            'contact_hero_subtitle' => 'فريق الاستشارات الأكاديمية وخدمة العملاء جاهز للرد على استفساراتكم بخصوص باقات الاشتراك وجدول الحصص.',
            'contact_phone' => '+20 100 000 0000',
            'contact_whatsapp' => '+20 100 000 0000',
            'owner_whatsapp' => '+20 100 000 0000',
            'contact_email' => 'support@trendk.com',
            'contact_address' => 'مجمع الصروح التعليمية، التجمع الخامس، القاهرة، جمهورية مصر العربية',
            'contact_address_ar' => 'مجمع الصروح التعليمية، التجمع الخامس، القاهرة، جمهورية مصر العربية',
            'contact_address_en' => 'Educational Centers Complex, New Cairo, Egypt',
            'about_hero_badge' => 'ريادة تعليمية معتمدة • منذ 2020',
            'about_hero_title' => 'إعادة صياغة التعليم التفاعلي وبناء أجيال من المتفوقين',
            'about_hero_subtitle' => 'تجمع أكاديمية النخبة بين أحدث تقنيات التعليم الرقمي ونخبة من ألمع الأساتذة لتقديم تجربة تعليمية رائدة.',
            'about_badge' => 'ريادة تعليمية معتمدة • منذ 2020',
            'about_title' => 'صرح تعليمي متكامل يصنع التفوق الحقيقي',
            'about_content' => 'تجمع أكاديمية النخبة التعليمية بين المناهج المعتمدة وحصص البث المباشر التفاعلية مع نخبة الأساتذة لتأهيل جيل جديد من رواد المستقبل.',
            'about_mission_title' => 'رسالتنا الأكاديمية',
            'about_mission_text' => 'تمكين كل طالب من استثمار كامل قدراته الفكرية وتحقيق أعلى المراتب في الشهادات الثانوية والجامعية.',
            'about_vision_title' => 'رؤيتنا المستقبلية',
            'about_vision_text' => 'أن نكون المنصة التعليمية الأكثر موثوقية وجودة في العالم العربي لبناء رواد الغد.',
            'about_stat_students' => '25,000+',
            'about_stat_courses' => '120+',
            'about_stat_teachers' => '45+',
            'about_stat_pass_rate' => '99.2%',
            'why_badge' => 'لماذا تختار النخبة؟',
            'why_title' => 'لماذا يختار أوائل الطلاب أكاديمية النخبة؟',
            'why_subtitle' => 'تجمع المنصة بين قوة المنهج الأكاديمي، كفاءة نخبة المعلمين، والتكنولوجيا الذكية الداعمة للتميز.',
            'cta_headline' => 'جاهز لبدء رحلة التفوق مع نخبة معلمي مصر؟',
            'cta_subtitle' => 'انضم إلى آلاف الطلاب المتفوقين في أكاديمية النخبة واستفد من الحصص المباشرة والواجبات المصلحة آلياً.',
            'footer_tagline_ar' => 'أكاديمية النخبة التعليمية — الصرح الأكاديمي الرقمي الأول في مصر لطلاب الثانوية العامة والمتفوقين، يجمع بين البث المباشر التفاعلي والتقييم الذكي للواجبات ومتابعة أولياء الأمور.',
            'footer_tagline_en' => 'Elite Academy — Egypt premier interactive EdTech platform for K-12 and Thanaweya Amma high achievers, featuring live streaming, auto-graded assignments, and parent progress tracking.',
            'footer_rights_ar' => '© 2026 أكاديمية النخبة التعليمية (Trendk Demo). جميع الحقوق محفوظة.',
            'footer_rights_en' => '© 2026 Elite Academy (Trendk Demo). All rights reserved.',
            'footer_quick_links_title_ar' => 'روابط سريعة',
            'footer_quick_links_title_en' => 'Quick Links',
            'footer_subjects_title_ar' => 'المسارات الأكاديمية',
            'footer_subjects_title_en' => 'Academic Tracks',
            'footer_contact_title_ar' => 'تواصل معنا',
            'footer_contact_title_en' => 'Get In Touch',
            'footer_working_hours_ar' => 'السبت - الخميس: 9:00 ص - 10:00 م',
            'footer_working_hours_en' => 'Sat - Thu: 9:00 AM - 10:00 PM',
            'landing_stats_counters' => json_encode([
                ['count' => '25,000+', 'label_ar' => 'طالب متفوق ومسجل', 'label_en' => 'Active High Achievers', 'color' => 'teal'],
                ['count' => '120+', 'label_ar' => 'كورس ومنهج معتمد', 'label_en' => 'Accredited Courses', 'color' => 'teal'],
                ['count' => '45+', 'label_ar' => 'من كبار معلمي الجمهورية', 'label_en' => 'Renowned Master Teachers', 'color' => 'teal'],
                ['count' => '99.2%', 'label_ar' => 'نسبة النجاح والتفوق', 'label_en' => 'Success & Excellence Rate', 'color' => 'orange'],
                ['count' => '100%', 'label_ar' => 'اعتماد وتدريس تفاعلي', 'label_en' => 'Live Interactive Accreditation', 'color' => 'teal'],
            ], JSON_UNESCAPED_UNICODE),
            'landing_why_items' => json_encode([
                [
                    'title_ar' => 'نخبة من كبار معلمي الجمهورية',
                    'title_en' => 'Renowned Master Teachers',
                    'desc_ar' => 'أساتذة ودكاترة متخصصون في تأهيل أوائل الثانوية العامة ومدارس المتفوقين STEM.',
                    'desc_en' => 'PhD and senior educators preparing top national rankers.',
                ],
                [
                    'title_ar' => 'فصول افتراضية تفاعلية HD',
                    'title_en' => 'Interactive HD Classrooms',
                    'desc_ar' => 'بث مباشر فائق الوضوح مع تسجيلات للحصص ومذكرات رقمية شاملة.',
                    'desc_en' => 'Low-latency encrypted live classes with full session recordings.',
                ],
                [
                    'title_ar' => 'تصحيح إلكتروني فوري للواجبات',
                    'title_en' => 'Instant Assignment Auto-Grading',
                    'desc_ar' => 'نظام MSQ الذكي للتقييم الفوري للحلول وتقديم التغذية الراجعة الدقيقة.',
                    'desc_en' => 'Step-by-step MSQ engine with instant feedback and grade analysis.',
                ],
                [
                    'title_ar' => 'بوابة متكاملة لمتابعة ولي الأمر',
                    'title_en' => 'Parent Follow-up Portal',
                    'desc_ar' => 'تقارير حضور ولحظية ورصد درجات وتنبيهات مستمرة للأداء الأكاديمي.',
                    'desc_en' => 'Real-time attendance telemetry, grade tracking, and instant alerts.',
                ],
            ], JSON_UNESCAPED_UNICODE),
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value, str_starts_with($key, 'contact_') ? 'contact' : (str_starts_with($key, 'about_') ? 'about' : 'general'));
        }

        // -------------------------------------------------------------
        // 16. Testimonials
        // -------------------------------------------------------------
        Testimonial::updateOrCreate(
            ['name' => 'عمر شريف إبراهيم'],
            [
                'avatar' => 'images/hero_student.png',
                'content' => 'بفضل الله ثم شرح د. طارق الشناوي في أكاديمية النخبة استطعت فهم أدق تفاصيل قوانين كيرشوف والفيزياء الحديثة والحصول على الدرجة النهائية.',
                'course_name' => 'الفيزياء الكهربية والحديثة (ثانوية عامة)',
                'rating' => 5,
                'reviewer_type' => 'student',
                'is_verified' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        Testimonial::updateOrCreate(
            ['name' => 'م. شريف إبراهيم غالي'],
            [
                'avatar' => 'images/instructor_male.png',
                'content' => 'بوابة ولي الأمر أحدثت فارقاً حقيقياً في متابعة ابني؛ إشعارات الحضور الفورية، تقارير الواجبات، ووضوح المعاملات المالية يمنح راحة بال تامة.',
                'course_name' => 'بوابة ولي الأمر الأكاديمية',
                'rating' => 5,
                'reviewer_type' => 'parent',
                'is_verified' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        Testimonial::updateOrCreate(
            ['name' => 'سلمى عبد الرحمن'],
            [
                'avatar' => 'images/instructor_female.png',
                'content' => 'كورس الكيمياء العضوية مع أ. سارة فاق كل توقعاتي، الخرائط الذهنية سهلت حفظ وفهم التفاعلات المعقدة بدون أي عناء.',
                'course_name' => 'الكيمياء العضوية والتحليلية',
                'rating' => 5,
                'reviewer_type' => 'student',
                'is_verified' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ]
        );

        $this->command?->info('🎉 Elite Academy Master Demo Seeder completed successfully!');
        $this->command?->table(
            ['Role', 'Email', 'Password', 'Target Portal'],
            [
                ['Admin (Principal)', 'admin@trendk.com', $demoPassword, '/admin (Filament Admin)'],
                ['Senior Teacher', 'teacher@trendk.com', $demoPassword, '/teacher (teacher-portal)'],
                ['Top Student', 'student@trendk.com', $demoPassword, '/student (student-portal)'],
                ['Active Parent', 'parent@trendk.com', $demoPassword, '/parent (parent-portal)'],
                ['Support (Hidden)', 'support@trendk.com', $demoPassword, 'Gate::before Super Admin'],
            ]
        );
    }
}

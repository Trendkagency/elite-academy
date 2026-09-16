<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\SessionProgressStatus;
use App\Enums\SubmissionStatus;
use App\Models\AdminProfile;
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
use App\Models\LiveSession;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;

class EgyptianEduSeeder extends Seeder
{
    private static ?string $hashedPassword = null;

    private function getPassword(): string
    {
        if (!self::$hashedPassword) {
            self::$hashedPassword = bcrypt('password');
        }
        return self::$hashedPassword;
    }

    public function run(): void
    {
        User::flushEventListeners();
        Course::flushEventListeners();
        CourseSession::flushEventListeners();
        LiveSession::flushEventListeners();
        ExceptionRequest::flushEventListeners();
        AssignmentSubmission::flushEventListeners();

        if ($this->command) {
            $this->command->info('🏫 Starting Full Egyptian Educational Dummy Data Seeding...');
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 1. Grade Levels (الصفوف والمراحل الدراسية المصرية)
        // ─────────────────────────────────────────────────────────────────────────
        $g12 = GradeLevel::updateOrCreate(['slug' => 'grade-12'], ['name' => 'الصف الثالث الثانوي (الثانوية العامة & STEM)', 'sort_order' => 1]);
        $g11 = GradeLevel::updateOrCreate(['slug' => 'grade-11'], ['name' => 'الصف الثاني الثانوي (علمي وأدبي)', 'sort_order' => 2]);
        $g10 = GradeLevel::updateOrCreate(['slug' => 'grade-10'], ['name' => 'الصف الأول الثانوي', 'sort_order' => 3]);
        $g9  = GradeLevel::updateOrCreate(['slug' => 'grade-9'],  ['name' => 'الصف الثالث الإعدادي (الشهادة الإعدادية)', 'sort_order' => 4]);

        // ─────────────────────────────────────────────────────────────────────────
        // 2. Categories & Egyptian Curriculum Subjects
        // ─────────────────────────────────────────────────────────────────────────
        $catSci  = Category::updateOrCreate(['slug' => 'natural-sciences'],         ['name' => 'العلوم الطبيعية والتطبيقية', 'color_theme' => '#0D9488']);
        $catMath = Category::updateOrCreate(['slug' => 'math-and-tech'],            ['name' => 'الرياضيات والتكنولوجيا',     'color_theme' => '#2563EB']);
        $catLang = Category::updateOrCreate(['slug' => 'languages-and-literature'],    ['name' => 'اللغات والعلوم الإنسانية',    'color_theme' => '#EA580C']);

        $subPhysics = Subject::updateOrCreate(['slug' => 'physics'],     ['category_id' => $catSci->id,  'name' => 'الفيزياء',                  'description' => 'الكهرومغناطيسية والدوائر الكهربية والفيزياء الحديثة والنووية.', 'sort_order' => 1]);
        $subChem    = Subject::updateOrCreate(['slug' => 'chemistry'],   ['category_id' => $catSci->id,  'name' => 'الكيمياء',                  'description' => 'الكيمياء العضوية والتحليلية والاتزان الكيميائي والكيمياء الكهربية.', 'sort_order' => 2]);
        $subBio     = Subject::updateOrCreate(['slug' => 'biology'],     ['category_id' => $catSci->id,  'name' => 'الأحياء',                   'description' => 'البيولوجيا الجزيئية وDNA والتكاثر والمناعة ووظائف الأعضاء.', 'sort_order' => 3]);
        $subGeo     = Subject::updateOrCreate(['slug' => 'geology'],     ['category_id' => $catSci->id,  'name' => 'الجيولوجيا وعلوم البيئة',  'description' => 'تراكيب القشرة الأرضية، المعادن والصخور والتوازن البيئي.', 'sort_order' => 4]);
        
        $subMath    = Subject::updateOrCreate(['slug' => 'mathematics'], ['category_id' => $catMath->id, 'name' => 'الرياضيات البحتة',          'description' => 'التفاضل والتكامل والجبر والهندسة الفراغية للثانوية العامة.', 'sort_order' => 5]);
        $subAppMath = Subject::updateOrCreate(['slug' => 'applied-math'],['category_id' => $catMath->id, 'name' => 'الرياضيات التطبيقية',      'description' => 'الاستاتيكا والديناميكا وعزوم القوى وقوانين نيوتن في الحركة.', 'sort_order' => 6]);
        $subProg    = Subject::updateOrCreate(['slug' => 'programming'], ['category_id' => $catMath->id, 'name' => 'البرمجة والذكاء الاصطناعي', 'description' => 'Python والخوارزميات وتعلم الآلة وتطوير البرمجيات.', 'sort_order' => 7]);
        
        $subAr      = Subject::updateOrCreate(['slug' => 'arabic'],      ['category_id' => $catLang->id, 'name' => 'اللغة العربية',             'description' => 'النحو والصرف والبلاغة والأدب العربي وقراءة النصوص.', 'sort_order' => 8]);
        $subEng     = Subject::updateOrCreate(['slug' => 'english'],     ['category_id' => $catLang->id, 'name' => 'اللغة الإنجليزية',          'description' => 'القواعد المتقدمة، المقال الأكاديمي، والمفردات اللغوية.', 'sort_order' => 9]);
        $subFr      = Subject::updateOrCreate(['slug' => 'french'],      ['category_id' => $catLang->id, 'name' => 'اللغة الفرنسية',           'description' => 'قواعد اللغة الفرنسية والمحادثة وفهم النصوص المكتوبة.', 'sort_order' => 10]);
        $subHist    = Subject::updateOrCreate(['slug' => 'history'],     ['category_id' => $catLang->id, 'name' => 'التاريخ والجغرافيا',        'description' => 'تاريخ مصر الحديث والمعاصر والجغرافيا السياسية.', 'sort_order' => 11]);

        // ─────────────────────────────────────────────────────────────────────────
        // 3. Admin User
        // ─────────────────────────────────────────────────────────────────────────
        $admin = User::updateOrCreate(['email' => 'admin@elite.edu'], [
            'name' => 'المدير العام — إدارة الأكاديمية',
            'phone' => '+201000000001',
            'password' => $this->getPassword(),
            'status' => AccountStatus::APPROVED,
            'email_verified_at' => now(),
        ]);
        AdminProfile::updateOrCreate(['user_id' => $admin->id]);

        // ─────────────────────────────────────────────────────────────────────────
        // 4. Renowned Egyptian Teachers
        // ─────────────────────────────────────────────────────────────────────────
        $tAhmed   = $this->upsertTeacher('dr-ahmed-mahmoud', 'د. أحمد محمود',   'dr.ahmed.mahmoud@elite.edu',  '+201000000101', 'كبير معلمي الفيزياء والكهرومغناطيسية', 'Physics',          'دكتوراه في الفيزياء التطبيقية. خبرة 18 عاماً في تدريس الثانوية العامة ومدارس STEM.', 18, 4.95, 3850, true);
        $tSarah   = $this->upsertTeacher('sarah-mohamed',    'أ. سارة محمد',    'sarah.mohamed@elite.edu',     '+201000000102', 'أستاذة الكيمياء العضوية ومنسقة الأولمبياد', 'Chemistry',  'ماجستير كيمياء عضوية. خبرة 14 عاماً في تبسيط المعادلات والتحليل الكيميائي.', 14, 4.90, 2900, true);
        $tOmar    = $this->upsertTeacher('dr-omar-khaled',   'د. عمر خالد',     'dr.omar.khaled@elite.edu',    '+201000000103', 'أستاذ الرياضيات البحتة والتطبيقية',       'Mathematics',    'دكتوراه في التحليل الرياضي. درّب أوائل الجمهورية في الثانوية العامة لأكثر من 15 عاماً.', 20, 4.98, 4600, true);
        $tFatma   = $this->upsertTeacher('dr-fatma-ali',     'د. فاطمة علي',    'fatma.ali@elite.edu',         '+201000000104', 'استشارية الأحياء وعلم الجينات والوراثة',   'Biology',        'دكتوراه في البيولوجيا الجزيئية. شرح تفاعلي بنماذج ثلاثية الأبعاد.', 12, 4.92, 2400, true);
        $tTarek   = $this->upsertTeacher('dr-tarek-fouad',   'د. طارق فؤاد',    'tarek.fouad@elite.edu',       '+201000000105', 'كبير موجهي اللغة العربية والبلاغة والنقد', 'Arabic',         'دكتوراه في البلاغة والنقد الأدبي. 24 عاماً في إعداد نماذج امتحانات الثانوية العامة.', 24, 5.00, 5200, true);
        $tHoda    = $this->upsertTeacher('hoda-mahmoud',     'أ. هدى محمود',    'hoda.mahmoud@elite.edu',      '+201000000106', 'أستاذة اللغة الإنجليزية والترجمة الأكاديمية', 'English',   'خبيرة مناهج اللغات وIELTS وIGCSE. خبرة 15 عاماً في تدريس الثانوية.', 15, 4.88, 2650, true);
        $tKareem  = $this->upsertTeacher('eng-kareem-zaki',  'م. كريم زكي',     'kareem.zaki@elite.edu',       '+201000000107', 'خبير البرمجيات وهندسة الذكاء الاصطناعي',   'Programming',    'مهندس برمجيات أول ومحاضر Python والذكاء الاصطناعي والخوارزميات.', 10, 4.96, 3100, true);
        $tMedhat  = $this->upsertTeacher('medhat-elshenawy', 'أ. مدحت الشناوي', 'medhat.shenawy@elite.edu',    '+201000000108', 'كبير معلمي الجيولوجيا وعلوم البيئة',       'Geology',        'خبرة 16 عاماً في شرح التراكيب الجيولوجية والمعادن وعلوم الأرض.', 16, 4.89, 1950, false);
        $tHossam  = $this->upsertTeacher('dr-hossam-hosny',  'د. حسام حسني',    'hossam.hosny@elite.edu',      '+201000000109', 'أستاذ التاريخ المعاصر والجغرافيا السياسية', 'History',       'دكتوراه في التاريخ الحديث والمعاصر وخرائط الجغرافيا السياسية.', 19, 4.85, 1800, false);
        $tNivine  = $this->upsertTeacher('nevine-roushdy',   'مدام نيفين رشدي', 'nevine.roushdy@elite.edu',    '+201000000110', 'أستاذة اللغة الفرنسية والمناهج المتقدمة',   'French',         'ليسانس آداب فرنسي وترجمة فورية. خبرة 13 عاماً في مدارس اللغات.', 13, 4.87, 1600, false);

        // ─────────────────────────────────────────────────────────────────────────
        // 5. Package Templates
        // ─────────────────────────────────────────────────────────────────────────
        $pkgS  = PackageTemplate::updateOrCreate(['name' => 'باقة المادة المنفردة (4 حصص تفاعلية)'],         ['sessions_count' => 4,  'price' => 220.00, 'description' => 'باقة مركزة تغطي كورس مادة دراسية واحدة أو مراجعة شهرية مكثفة.', 'is_active' => true]);
        $pkgM  = PackageTemplate::updateOrCreate(['name' => 'باقة التميز الشهري (12 حصة تفاعلية)'],          ['sessions_count' => 12, 'price' => 550.00, 'description' => 'باقة شهرية متكاملة لجميع الحصص المباشرة والواجبات والمتابعة الدورية.', 'is_active' => true]);
        $pkgL  = PackageTemplate::updateOrCreate(['name' => 'باقة الثانوية العامة الفائقة (24 حصة تفاعلية)'], ['sessions_count' => 24, 'price' => 980.00, 'description' => 'باقة شاملة تغطي جميع المواد الأساسية مع بنوك الأسئلة والاختبارات المباشرة.', 'is_active' => true]);
        $pkgXL = PackageTemplate::updateOrCreate(['name' => 'باقة النخبة السنوية المكثفة (36 حصة تفاعلية)'], ['sessions_count' => 36, 'price' => 1400.00,'description' => 'باقة سنوية كاملة تغطي كافة الحصص والاختبارات والمراجعات النهائية وأولمبياد العلوم.', 'is_active' => true]);

        // ─────────────────────────────────────────────────────────────────────────
        // 6. Parents & Egyptian Students
        // ─────────────────────────────────────────────────────────────────────────
        $parent1 = User::updateOrCreate(['email' => 'parent@elite.edu'],  ['name' => 'أ. خالد محمود السعيد', 'phone' => '+201000000004', 'password' => $this->getPassword(), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        $pp1     = ParentProfile::updateOrCreate(['user_id' => $parent1->id]);
        $parent2 = User::updateOrCreate(['email' => 'parent2@elite.edu'], ['name' => 'م. سامي عبدالرحمن الصاوي', 'phone' => '+201000000008', 'password' => $this->getPassword(), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        $pp2     = ParentProfile::updateOrCreate(['user_id' => $parent2->id]);

        [$sAhmed,   $pAhmed]   = $this->upsertStudent('ahmed@elite.edu',   'أحمد خالد السعيد',   '+201000000005', $g12->id, 'مدرسة المتفوقين للعلوم والتكنولوجيا STEM أكتوبر', true,  [$subPhysics->id, $subMath->id, $subChem->id, $subBio->id, $subAr->id, $subEng->id]);
        [$sMariam,  $pMariam]  = $this->upsertStudent('mariam@elite.edu',  'مريم خالد السعيد',   '+201000000006', $g11->id, 'مدرسة النيل المصرية الدولية الشيخ زايد',      true,  [$subChem->id, $subBio->id, $subMath->id, $subEng->id]);
        [$sOmar,    $pOmar]    = $this->upsertStudent('omar@elite.edu',    'عمر خالد السعيد',    '+201000000007', $g10->id, 'مدرسة الأورمان الثانوية لغات بالدقي',           false, [$subProg->id, $subPhysics->id, $subMath->id]);
        [$sNada,    $pNada]    = $this->upsertStudent('nada@elite.edu',    'ندى سامي الصاوي',    '+201000000009', $g12->id, 'مدرسة المستقبل الرسمية المتميزة لغات',           true,  [$subPhysics->id, $subChem->id, $subAr->id, $subGeo->id, $subMath->id]);
        [$sYoussef, $pYoussef] = $this->upsertStudent('youssef@elite.edu', 'يوسف سامي الصاوي',  '+20100000010', $g11->id, 'مدرسة دار التربية الحديثة لغات',               false, [$subMath->id, $subAppMath->id, $subEng->id]);
        [$sLaila,   $pLaila]   = $this->upsertStudent('laila@elite.edu',   'ليلى أحمد توفيق',   '+20100000011', $g9->id,  'مدرسة الإبداع الإعدادية النموذجية بنات',         false, [$subProg->id, $subEng->id]);

        $pp1->students()->syncWithoutDetaching([$sAhmed->id => ['relationship' => 'Son'], $sMariam->id => ['relationship' => 'Daughter'], $sOmar->id => ['relationship' => 'Son']]);
        $pp2->students()->syncWithoutDetaching([$sNada->id  => ['relationship' => 'Daughter'], $sYoussef->id => ['relationship' => 'Son']]);

        // ─────────────────────────────────────────────────────────────────────────
        // 7. Student Packages & Detailed Transaction History
        // ─────────────────────────────────────────────────────────────────────────
        $pkgAhmed = $this->upsertStudentPackage($sAhmed->id, $pkgL->id, 24, 6, 18, 'active', 15, 45);
        $this->upsertStudentPackage($sMariam->id,  $pkgM->id, 12, 3, 9,  'active', 10, 20);
        $this->upsertStudentPackage($sOmar->id,    $pkgS->id, 4,  1, 3,  'active', 4,  26);
        $this->upsertStudentPackage($sNada->id,    $pkgXL->id,36, 10, 26,'active', 20, 60);
        $this->upsertStudentPackage($sYoussef->id, $pkgM->id, 12, 2, 10, 'active', 5,  25);
        $this->upsertStudentPackage($sLaila->id,   $pkgS->id, 4,  0, 4,  'active', 2,  28);

        PackageTransaction::where('student_package_id', $pkgAhmed->id)->delete();
        $transactionsData = [
            ['type' => 'payment_activation', 'sessions_delta' => 24, 'balance_before' => 0,  'balance_after' => 24, 'reason' => 'تفعيل باقة الثانوية العامة الفائقة (24 حصة)', 'created_at' => now()->subDays(15)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 24, 'balance_after' => 23, 'reason' => 'حضور حصة: فيزياء — التيار الكهربي وقانون أوم', 'created_at' => now()->subDays(13)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 23, 'balance_after' => 22, 'reason' => 'حضور حصة: كيمياء — تسمية الهيدروكربونات والألكانات', 'created_at' => now()->subDays(11)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 22, 'balance_after' => 21, 'reason' => 'حضور حصة: رياضيات — نهايات الدوال والاتصال', 'created_at' => now()->subDays(9)],
            ['type' => 'session_refund',     'sessions_delta' => 1,  'balance_before' => 21, 'balance_after' => 22, 'reason' => 'استرداد حصة: قبول عذر غياب معتمد من الإدارة', 'created_at' => now()->subDays(7)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 22, 'balance_after' => 21, 'reason' => 'حضور حصة: لغة عربية — الجملة الاسمية والخبر المقدم', 'created_at' => now()->subDays(6)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 21, 'balance_after' => 20, 'reason' => 'حضور حصة: أحياء — الانقسام الخلوي والمادة الوراثية', 'created_at' => now()->subDays(4)],
            ['type' => 'session_bonus',      'sessions_delta' => 2,  'balance_before' => 20, 'balance_after' => 22, 'reason' => 'مكافأة تفوق دراسي: الحصول على 100% في اختبار الفيزياء', 'created_at' => now()->subDays(3)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 22, 'balance_after' => 21, 'reason' => 'حضور حصة: جيولوجيا — التراكيب الجيولوجية الأولية', 'created_at' => now()->subDays(2)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 21, 'balance_after' => 20, 'reason' => 'حضور حصة: إنجليزي — Essay Writing & Advanced Structures', 'created_at' => now()->subDays(1)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 20, 'balance_after' => 19, 'reason' => 'حضور حصة: فيزياء — قانون كيرشوف الأول والثاني', 'created_at' => now()->subHours(18)],
            ['type' => 'session_deduct',     'sessions_delta' => -1, 'balance_before' => 19, 'balance_after' => 18, 'reason' => 'حضور حصة: رياضيات — مشتقات الدوال المثلثية', 'created_at' => now()->subHours(6)],
        ];
        foreach ($transactionsData as $t) {
            PackageTransaction::create(array_merge($t, ['student_package_id' => $pkgAhmed->id]));
        }

        // ─────────────────────────────────────────────────────────────────────────
        // 8. Courses
        // ─────────────────────────────────────────────────────────────────────────
        $cPhysics = Course::updateOrCreate(['slug' => 'physics-electricity-g12'], [
            'subject_id' => $subPhysics->id, 'teacher_id' => $tAhmed->id, 'grade_level_id' => $g12->id,
            'title' => 'الفيزياء الكهربية والمغناطيسية والحديثة — الصف الثالث الثانوي',
            'description' => 'شرح تفصيلي معتمد لقوانين كيرشوف، الحث الكهرومغناطيسي، دوائر التيار المتردد والفيزياء الحديثة النووية.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 10, 'session_duration_minutes' => 60
        ]);

        $cChem = Course::updateOrCreate(['slug' => 'organic-chemistry-g12'], [
            'subject_id' => $subChem->id, 'teacher_id' => $tSarah->id, 'grade_level_id' => $g12->id,
            'title' => 'الكيمياء العضوية والتحليل الكيميائي التفاعلي — الصف الثالث الثانوي',
            'description' => 'دراسة الهيدروكربونات، مشتقات الهيدروكربونات، الألكانات والألكينات، المركبات الأروماتية وتجارب المعامل الافتراضية.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 8, 'session_duration_minutes' => 60
        ]);

        $cMath = Course::updateOrCreate(['slug' => 'calculus-algebra-g12'], [
            'subject_id' => $subMath->id, 'teacher_id' => $tOmar->id, 'grade_level_id' => $g12->id,
            'title' => 'التفاضل والتكامل والجبر والهندسة الفراغية — الصف الثالث الثانوي',
            'description' => 'اشتقاق الدوال الأسية واللوغاريتمية، التكامل بالتجزيء والتعويض، حجوم الأجسام الدورانية، والمحددات والمصفوفات.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 10, 'session_duration_minutes' => 75
        ]);

        $cBio = Course::updateOrCreate(['slug' => 'biology-genetics-g12'], [
            'subject_id' => $subBio->id, 'teacher_id' => $tFatma->id, 'grade_level_id' => $g12->id,
            'title' => 'الأحياء والبيولوجيا الجزيئية وعلم الجينات — الصف الثالث الثانوي',
            'description' => 'دراسة الحمض النووي DNA وتخليق البروتين، الهندسة الوراثية، المناعة في الإنسان، والتكاثر في الكائنات الحية.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => false, 'is_accredited' => true, 'sessions_count' => 8, 'session_duration_minutes' => 60
        ]);

        $cArabic = Course::updateOrCreate(['slug' => 'arabic-grammar-g12'], [
            'subject_id' => $subAr->id, 'teacher_id' => $tTarek->id, 'grade_level_id' => $g12->id,
            'title' => 'اللغة العربية — النحو والصرف والبلاغة والأدب — الصف الثالث الثانوي',
            'description' => 'موسوعة النحو والبلاغة والأدب العربي الحديث ومدارس الشعر مع حلول بنك الأسئلة ونماذج امتحانات الثانوية العامة.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 8, 'session_duration_minutes' => 60
        ]);

        $cGeo = Course::updateOrCreate(['slug' => 'geology-earth-g12'], [
            'subject_id' => $subGeo->id, 'teacher_id' => $tMedhat->id, 'grade_level_id' => $g12->id,
            'title' => 'الجيولوجيا وعلوم البيئة والمياه الجوفية — الصف الثالث الثانوي',
            'description' => 'تراكيب القشرة الأرضية، الفوالق والطيات، دورة الصخور، الموارد البيئية واستنزاف الطاقة.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 6, 'session_duration_minutes' => 60
        ]);

        $cAppMath = Course::updateOrCreate(['slug' => 'applied-math-g11'], [
            'subject_id' => $subAppMath->id, 'teacher_id' => $tOmar->id, 'grade_level_id' => $g11->id,
            'title' => 'الرياضيات التطبيقية (الاستاتيكا والديناميكا) — الصف الثاني الثانوي',
            'description' => 'محصلة قوتين متلاقيتين، الاتزان العام، قوانين نيوتن، الدفع والتصادم والشغل والطاقة.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 8, 'session_duration_minutes' => 75
        ]);

        $cEnglish = Course::updateOrCreate(['slug' => 'english-advanced-g11'], [
            'subject_id' => $subEng->id, 'teacher_id' => $tHoda->id, 'grade_level_id' => $g11->id,
            'title' => 'اللغة الإنجليزية المتقدمة — مهارات الكتابة والقواعد والأدب',
            'description' => 'Academic Writing, Advanced Tenses, Inversion Structures, Reading Comprehension and Literature Themes.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => false, 'is_accredited' => true, 'sessions_count' => 6, 'session_duration_minutes' => 60
        ]);

        $cProg = Course::updateOrCreate(['slug' => 'python-ai-g10'], [
            'subject_id' => $subProg->id, 'teacher_id' => $tKareem->id, 'grade_level_id' => $g10->id,
            'title' => 'أساسيات البرمجة بـ Python والذكاء الاصطناعي — الصف الأول الثانوي',
            'description' => 'تعلم Python من الصفر حتى بناء أول نظام ذكاء اصطناعي وتحليل البيانات باستخدام NumPy وPandas.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => false, 'sessions_count' => 8, 'session_duration_minutes' => 90
        ]);

        $cHistory = Course::updateOrCreate(['slug' => 'history-modern-egypt-g12'], [
            'subject_id' => $subHist->id, 'teacher_id' => $tHossam->id, 'grade_level_id' => $g12->id,
            'title' => 'تاريخ مصر الحديث والمعاصر والجغرافيا السياسية — الصف الثالث الثانوي',
            'description' => 'شرح شامل للحملة الفرنسية، عهد محمد علي، ثورة 1919، وحرب أكتوبر والجغرافيا السياسية للدول.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 6, 'session_duration_minutes' => 60
        ]);

        $cFrench = Course::updateOrCreate(['slug' => 'french-lang-g12'], [
            'subject_id' => $subFr->id, 'teacher_id' => $tNivine->id, 'grade_level_id' => $g12->id,
            'title' => 'اللغة الفرنسية الشاملة والمحادثة — الصف الثالث الثانوي',
            'description' => 'قواعد اللغة الفرنسية، المواقف اليومية، فهم المقروء، والإنتاج الكتابي والشفهي.',
            'demo_video_url' => 'https://digitability.com/wp-content/uploads/appropriate-sharing.mp4',
            'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 6, 'session_duration_minutes' => 60
        ]);

        // 9. Recorded Sessions
        $physSess = $this->createSessions($cPhysics->id, [
            [1, 'الدرس 1: التيار الكهربي وقانون أوم وتوصيل المقاومات', 'شرح شدة التيار، فرق الجهد، المقاومة النوعية والتوصيلية الكهربية مع حل مسائل التوالي والتوازي.', true, 60],
            [2, 'الدرس 2: قانون كيرشوف الأول والثاني وتطبيقات الشبكات', 'تطبيق KCL وKVL على الدوائر الكهربية المعقدة وحساب جهود النقاط.', false, 75],
            [3, 'الدرس 3: التأثير المغناطيسي للتيار الكهربي وقوة لورنتز', 'المجال المغناطيسي لسلك مستقيم وملف دائري ولولبي، عزم الازدواج وعزم ثنائي القطب.', false, 60],
            [4, 'الدرس 4: أجهزة القياس الكهربي — الجلفانومتر والأميتر والفولتميتر', 'حساب مجزئ التيار ومضاعف الجهد ومعايرة الأوميتر وحل المسائل المقالية.', false, 60],
            [5, 'الدرس 5: الحث الكهرومغناطيسي وقانون فاراداي وقاعدة لينز', 'القوة الدافعة الكهربية المستحثة في سلك مستقيم وتطبيقات الحث الذاتي والمتبادل.', false, 75],
            [6, 'الدرس 6: المولد الكهربي (الدينامو) والمحول الكهربي والمحرك', 'القدرة الكهربية الفعالة والعظمى ونقل الطاقة الكهربية وكفاءة المحول.', false, 75],
            [7, 'الدرس 7: دوائر التيار المتردد — المعاوقة والرنين', 'دوائر R-L-C، دائرة الرنين والدائرة المهتزة وحساب التردد الرنيني.', false, 60],
            [8, 'الدرس 8: ازدواجية الموجة والجسيم وظاهرة كومتون', 'إشعاع الجسم الأسود، الظاهرة الكهروضوئية، فرض بلانك وتفسير أينشتاين.', false, 60],
            [9, 'الدرس 9: الأطياف الذرية والليزر وتطبيقات الهولوجرام', 'طيف ذرة الهيدروجين، سلاسل ليمان وبالمر، الليزر والإنتاج الهولوغرافي.', false, 60],
            [10,'الدرس 10: الإلكترونيات الحديثة والفيزياء النووية', 'الوصلة الثنائية والترانزستور، البوابات المنطقية Logic Gates والانشطار النووي.', false, 60],
        ]);

        $chemSess = $this->createSessions($cChem->id, [
            [1, 'الدرس 1: الهيدروكربونات ونظام التسمية العالمي IUPAC', 'مقدمة الكيمياء العضوية، الصيغ الجزيئية والبنائية، والألكانات وخصائصها.', true, 60],
            [2, 'الدرس 2: الألكينات والألكاينات وتفاعلات الإضافة', 'تفاعلات الهدرجة، الهلجنة، وتطبيق قاعدة ماركوفنيكوف في الإضافة غير المتماثلة.', false, 60],
            [3, 'الدرس 3: المركبات الهيدروكربونية الحلقية والبنزين العطري', 'ظاهرة الرنين في حلقة البنزين وتفاعلات الفريدل كرافتس والنترتة والسلفنة.', false, 75],
            [4, 'الدرس 4: الكحولات والفينولات والأكسدة التامة والجزئية', 'تصنيف الكحولات الأولية والثانوية والثالثية وتفاعلات التمييز المخبري.', false, 60],
            [5, 'الدرس 5: الأحماض الكربوكسيلية والإسترات وزيوت التشحيم', 'تحضير الإسترات، التحلل المائي والنشادري، والصابونة والمنظفات الصناعية.', false, 60],
            [6, 'الدرس 6: التحليل الكيميائي الوصفي — الكشف عن الكاتيونات والأنيونات', 'تجارب التأكيد والكواشف الكيميائية لأملاح الكربونات والكبريتات والنيتريت.', false, 60],
            [7, 'الدرس 7: التحليل الكيميائي الكمي — المعايرة الحجمية والترسيب', 'حساب التركيز المولاري والنسبة المئوية للمركبات في العينات التجارية.', false, 60],
            [8, 'الدرس 8: نماذج امتحانات الثانوية العامة في الكيمياء الشاملة', 'حل اختبارات الثانوية العامة للأعوام السابقة مع مناقشة تريكات الأسئلة.', false, 75],
        ]);

        $mathSess = $this->createSessions($cMath->id, [
            [1, 'الدرس 1: نهايات الدوال وقواعد الاشتقاق الأساسية', 'قواعد اشتقاق الدوال الجبرية، الدوال المثلثية ومشتقة دالة الدالة (قاعدة السلسلة).', true, 75],
            [2, 'الدرس 2: الاشتقاق الضمني والبارامتري والمشتقات العليا', 'حساب الميل والمماس والمعادلات البارامترية مع تطبيقات هندسية وفيزيائية.', false, 75],
            [3, 'الدرس 3: النهايات المرتبطة بالعدد النيبيري e والدوال اللوغاريتمية', 'نهايات الدوال الأسية واللوغاريتمية واشتقاقها وتكاملها المباشر.', false, 75],
            [4, 'الدرس 4: سلوك الدالة ورسم المنحنيات والقيم العظمى والصغرى', 'نقاط الانقلاب، فترات التزايد والتناقص، والتحدب لأعلى وأسفل.', false, 75],
            [5, 'الدرس 5: المعدلات الزمنية المرتبطة وتطبيقات القيم القصوى', 'مسائل السلالم، الأشكال الهندسية، والمخاريط والحجوم التطبيقية.', false, 75],
            [6, 'الدرس 6: طرق التكامل المتقدمة — بالتجزيء وبالتعويض', 'تكامل حواصل ضرب الدوال الجبرية في المثلثية أو الأسية وحالات خاصة.', false, 75],
            [7, 'الدرس 7: التكامل المحدود وحساب المساحات وحجوم الأجسام الدورانية', 'تطبيقات المساحات بين منحنيين وحجوم الدوران حول محوري السينات والصادات.', false, 75],
            [8, 'الدرس 8: التباديل والتوافيق ونظرية ذات الحدين', 'إيجاد الحد العام، الحد الأوسط، وأكبر معامل في مفكوك ذات الحدين.', false, 60],
            [9, 'الدرس 9: الأعداد المركبة والصورة المثلثية والأسيّة (دي موافر)', 'الجذور النونية للواحد الصحيح (أوميجا) ونظرية دي موافر في التبسيط.', false, 75],
            [10,'الدرس 10: الهندسة الفراغية — معادلة المستقيم والمستوى في الفراغ', 'الضرب القياسي والاتجاهي للمتجهات ومعادلة المستوى والكرة في الفراغ الثلاثي.', false, 75],
        ]);

        $arSess = $this->createSessions($cArabic->id, [
            [1, 'الدرس 1: النحو — الوحدة الأولى (همزتا الوصل والقطع والألف اللينة)', 'قواعد الهمزات ورسم الكلمات وتطبيقات الإملاء والنطق الصحيح.', true, 60],
            [2, 'الدرس 2: النحو — الوحدة الثانية (المشتقات العاملة والمصادر)', 'اسم الفاعل، اسم المفعول، صيغ المبالغة، وعمل المشتقات وإعراب معمولها.', false, 60],
            [3, 'الدرس 3: النحو — الوحدة الثالثة (النواسخ: كان وكاد وإنّ ولا النافية للجنس)', 'أحكام تقديم الخبر، كان التامة والناقصة، وأحكام كسر همزة إنّ وفتحها.', false, 60],
            [4, 'الدرس 4: النحو — الوحدة الرابعة (المنصوبات: المفاعيل الخمسة والتمييز والحال)', 'المفعول به، المطلق، لأجله، فيه، ومعه، وتمييز العدد وإعرابه المفصل.', false, 60],
            [5, 'الدرس 5: البلاغة — علم البيان (التشبيه والاستعارة والكناية والمجاز)', 'أسرار الجمال البلاغي وسر بلاغة المجاز المرسل وعلاقاته.', false, 60],
            [6, 'الدرس 6: البلاغة — علم البديع والمعاني والإيجاز والإطناب', 'المحسنات اللفظية والمعنوية، أسلوب القصر، والتوكيد والتقديم والتأخير.', false, 60],
            [7, 'الدرس 7: الأدب الحديث — مدارس الشعر (الإحياء والوجدانية والواقعية)', 'شوقي والبارودي ومطران ومدرسة الديوان وأبولو والمهجر والواقعية الجديدة.', false, 60],
            [8, 'الدرس 8: مراجعة شاملة وحل نماذج الوزارة للثانوية العامة', 'تدريب عملي على 50 سؤال اختيار من متعدد من بنك المعرفة ونماذج الوزارة.', false, 75],
        ]);

        $bioSess = $this->createSessions($cBio->id, [
            [1, 'الدرس 1: الدعامة والحركة في الكائنات الحية', 'الدعامة الفسيولوجية والتركيبية في النبات، والهيكل العظمي والمفاصل في الإنسان.', false, 60],
            [2, 'الدرس 2: الانقباض العضلي وآلية الانزلاق (نظرية هكسلي)', 'الوحدة الحركية، الإجهاد العضلي، والشد العضلي ودور أيونات الكالسيوم وATP.', false, 60],
            [3, 'الدرس 3: التنسيق الهرموني في الكائنات الحية', 'الغدة النخامية، الدرقية، الكظرية، وجزر لانجرهانس في البنكرياس والأمراض الهرمونية.', false, 60],
            [4, 'الدرس 4: التكاثر في النباتات الزهرية والإنسان', 'دورة الطمث، الإخصاب، تكوين الجنين، والتوائم وتطبيقات أطفال الأنابيب.', false, 75],
            [5, 'الدرس 5: المناعة في الكائنات الحية والإنسان', 'المناعة الطبيعية والمكتسبة، الخلايا الليمفاوية B وT، والأجسام المضادة.', false, 75],
            [6, 'الدرس 6: الحمض النووي DNA وتركيب الجينوم', 'تجارب إثبات أن DNA مادة الوراثة، نموذج واتسون وكريك وتضاعف DNA.', false, 75],
            [7, 'الدرس 7: الحمض النووي RNA وتخليق البروتين', 'أنواع RNA الثلاثة، الشفرة الوراثية وتخليق سلاسل عديد الببتيد والريبوسوم.', false, 75],
            [8, 'الدرس 8: الهندسة الوراثية والتكنولوجيا الحيوية', 'إنزيمات القصر، البلازميدات، واستنساخ الجينات والعلاج الجيني الحديث.', false, 60],
        ]);

        // 10. Enrollments
        $enrAPhys = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id, 'course_id' => $cPhysics->id], ['status' => 'active', 'enrolled_at' => now()->subDays(20)]);
        $enrAChem = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id, 'course_id' => $cChem->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(18)]);
        $enrAMath = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id, 'course_id' => $cMath->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(16)]);
        $enrABio  = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id, 'course_id' => $cBio->id],     ['status' => 'active', 'enrolled_at' => now()->subDays(14)]);
        $enrAAr   = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id, 'course_id' => $cArabic->id],  ['status' => 'active', 'enrolled_at' => now()->subDays(12)]);
        $enrAGeo  = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id, 'course_id' => $cGeo->id],     ['status' => 'active', 'enrolled_at' => now()->subDays(10)]);
        $enrAEng  = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id, 'course_id' => $cEnglish->id], ['status' => 'active', 'enrolled_at' => now()->subDays(8)]);

        CourseEnrollment::updateOrCreate(['student_user_id' => $sMariam->id, 'course_id' => $cChem->id],   ['status' => 'active', 'enrolled_at' => now()->subDays(10)]);
        CourseEnrollment::updateOrCreate(['student_user_id' => $sMariam->id, 'course_id' => $cBio->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(8)]);
        CourseEnrollment::updateOrCreate(['student_user_id' => $sNada->id,   'course_id' => $cPhysics->id],['status' => 'active', 'enrolled_at' => now()->subDays(15)]);
        CourseEnrollment::updateOrCreate(['student_user_id' => $sNada->id,   'course_id' => $cChem->id],   ['status' => 'active', 'enrolled_at' => now()->subDays(12)]);
        CourseEnrollment::updateOrCreate(['student_user_id' => $sOmar->id,   'course_id' => $cProg->id],   ['status' => 'active', 'enrolled_at' => now()->subDays(6)]);

        // 11. Progress
        foreach (array_slice($physSess, 0, 5) as $s) {
            CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAPhys->id, 'course_session_id' => $s->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(15), 'completed_at' => now()->subDays(10)]);
        }
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAPhys->id, 'course_session_id' => $physSess[5]->id], ['status' => SessionProgressStatus::UNLOCKED, 'unlocked_at' => now()->subDays(2)]);

        foreach (array_slice($chemSess, 0, 3) as $s) {
            CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAChem->id, 'course_session_id' => $s->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(12), 'completed_at' => now()->subDays(8)]);
        }
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAChem->id, 'course_session_id' => $chemSess[3]->id], ['status' => SessionProgressStatus::UNLOCKED, 'unlocked_at' => now()->subDays(1)]);

        foreach (array_slice($mathSess, 0, 4) as $s) {
            CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAMath->id, 'course_session_id' => $s->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(14), 'completed_at' => now()->subDays(9)]);
        }

        // 12. Assignments
        $aPhys1 = $this->makeAssignment($physSess[0]->id, $cPhysics->id, 'واجب 1: قانون أوم وتوصيل المقاومات بالدوائر', 30, now()->subDays(12), [
            ['عند توصيل ثلاثة مقاومات متماثلة قيمة كل منها 9 أوم على التوازي، فإن المقاومة المكافئة تكون:', 5, ['3 أوم' => true, '27 أوم' => false, '9 أوم' => false, '1 أوم' => false]],
            ['وفق قانون أوم عند ثبوت درجة الحرارة، تتناسب شدة التيار المار في موصل مع فرق الجهد بين طرفيه تناسباً:', 5, ['طردياً' => true, 'عكسياً' => false, 'تربيعياً' => false, 'ثابتاً لا يتغير' => false]],
            ['موصل مقاومته 6 أوم يمر به تيار 2 أمبير. إذا زاد فرق الجهد للضعف، فإن مقاومة الموصل:', 5, ['تظل ثابتة (6 أوم)' => true, 'تصبح 12 أوم' => false, 'تصبح 3 أوم' => false, 'تصبح 24 أوم' => false]],
            ['وحدة قياس التوصيلية الكهربية في النظام الدولي هي:', 5, ['أوم⁻¹ . متر⁻¹ (سيمنز/م)' => true, 'أوم . متر' => false, 'فولت / أمبير' => false, 'جول / ثانية' => false]],
        ]);

        $aPhys2 = $this->makeAssignment($physSess[1]->id, $cPhysics->id, 'واجب 2: قوانين كيرشوف وشبكات التيار المعقدة', 40, now()->subDays(10), [
            ['ينص قانون كيرشوف الأول (KCL) للتيارات عند نقطة تفرع على أن:', 5, ['المجموع الجبري للتيارات الداخلة = الخارجة (حفظ الشحنة)' => true, 'المجموع الجبري للجهود = صفر' => false, 'القدرة المستهلكة = صفر' => false]],
            ['ينص قانون كيرشوف الثاني (KVL) في أي مسار مغلق على تطبيق مبدأ حفظ:', 5, ['الطاقة الكهربية' => true, 'الشحنة الكهربية' => false, 'كمية الحركة' => false, 'المادة' => false]],
            ['في دائرة كهربية تحتوي على بطاريتين 12V و6V متصلتين على التضاد، فإن القوة الدافعة المحصلة:', 5, ['6 فولت' => true, '18 فولت' => false, '12 فولت' => false, 'صفر' => false]],
        ]);

        $aPhys3 = $this->makeAssignment($physSess[2]->id, $cPhysics->id, 'اختبار 3: المجال المغناطيسي وقوة لورنتز', 35, now()->subDays(7), [
            ['اتجاه القوة المغناطيسية المؤثرة على سلك مستقيم يحمل تياراً عمودياً على مجال مغناطيسي يحدد بقاعدة:', 5, ['فلمنج لليد اليسرى' => true, 'فلمنج لليد اليمنى' => false, 'أمبير لليد اليمنى' => false, 'البريمة اليمنى' => false]],
            ['وحدة قياس كثافة الفيض المغناطيسي (التسلا) تكافئ:', 5, ['ويبر / متر²' => true, 'ويبر . متر' => false, 'نيوتن . أمبير' => false, 'فولت . ثانية' => false]],
        ]);

        $aChem1 = $this->makeAssignment($chemSess[0]->id, $cChem->id, 'واجب 1: تسمية الهيدروكربونات والألكانات والألكينات', 30, now()->subDays(11), [
            ['الصيغة العامة لسلسلة الألكانات المشبعة ذات السلاسل المفتوحة هي:', 5, ['CnH2n+2' => true, 'CnH2n' => false, 'CnH2n-2' => false, 'CnH2n-6' => false]],
            ['الاسم الصحيح للمركب 2,2-Dimethylpropane وفق نظام IUPAC يسمى تجارياً:', 5, ['نيوبنتان' => true, 'أيزوبنتان' => false, 'بنتان عادي' => false, 'بيوتان' => false]],
        ]);

        $aChem2 = $this->makeAssignment($chemSess[1]->id, $cChem->id, 'اختبار 2: تفاعلات الإضافة وقاعدة ماركوفنيكوف', 35, now()->subDays(8), [
            ['عند إضافة بروميد الهيدروجين HBr إلى البروبين، فإن الناتج الرئيسي المتكون هو:', 5, ['2-بروموبروبان' => true, '1-بروموبروبان' => false, '1,2-ثنائي بروموبروبان' => false]],
            ['تفاعل هيدرة الإيثاين (الأسيتيلين) الحفزية يعطي مركب:', 5, ['الأسيتالدهيد (الإيثانال)' => true, 'الإيثانول' => false, 'حمض الأسيتيك' => false, 'الأسيتون' => false]],
        ]);

        $aMath1 = $this->makeAssignment($mathSess[0]->id, $cMath->id, 'واجب 1: النهايات وقواعد الاشتقاق والاتصال', 30, now()->subDays(9), [
            ['قيمة النهاية: lim(x→0) [sin(3x) / (2x)] تساوي:', 5, ['3/2' => true, '1' => false, '0' => false, 'غير معينة' => false]],
            ['مشتقة الدالة f(x) = tan(2x) بالنسبة إلى x هي:', 5, ['2 sec²(2x)' => true, 'sec²(2x)' => false, '2 cot(2x)' => false, 'cos²(2x)' => false]],
        ]);

        $aMath2 = $this->makeAssignment($mathSess[1]->id, $cMath->id, 'اختبار 2: الاشتقاق الضمني والبارامتري والمشتقات العليا', 45, now()->subDays(5), [
            ['إذا كان x² + y² = 25، فإن dy/dx عند النقطة (3, 4) تساوي:', 5, ['-3/4' => true, '3/4' => false, '-4/3' => false, '4/3' => false]],
            ['المشتقة الثانية للدالة y = e^(3x) هي:', 5, ['9 e^(3x)' => true, '3 e^(3x)' => false, 'e^(3x)' => false, '6 e^(3x)' => false]],
        ]);

        $aAr1 = $this->makeAssignment($arSess[0]->id, $cArabic->id, 'واجب 1: النحو — المشتقات العاملة وأعمالها', 25, now()->subDays(6), [
            ['في جملة: "أفاهمٌ الطالبُ الدرسَ؟" إعراب كلمة "الطالب":', 5, ['فاعل لاسم الفاعل سد مسد الخبر' => true, 'مبتدأ مؤخر' => false, 'خبر مرفوع' => false, 'مفعول به' => false]],
            ['في جملة: "المؤمن محمودٌ خلقُه"، إعراب "خلقُه":', 5, ['نائب فاعل مرفوع' => true, 'فاعل مرفوع' => false, 'مضاف إليه' => false, 'مفعول به' => false]],
        ]);

        $aBio1 = $this->makeAssignment($bioSess[0]->id, $cBio->id, 'واجب 1: الدعامة والحركة وآلية الانقباض العضلي', 30, now()->subDays(4), [
            ['أيون المعدن المسؤول عن نقل السيال العصبي من النهاية العصبية إلى غشاء الليفة العضلية:', 5, ['الكالسيوم Ca²⁺' => true, 'الصوديوم Na⁺' => false, 'البوتاسيوم K⁺' => false, 'الحديد Fe²⁺' => false]],
            ['الوحدة الوظيفية للعضلة الهيكلية هي:', 5, ['الوحدة الحركية' => true, 'القطعة العضلية (الساركومير)' => false, 'اللييفة العضلية' => false, 'الميوسين' => false]],
        ]);

        // 13. Submissions History
        AssignmentSubmission::where('student_user_id', $sAhmed->id)->forceDelete();
        $submissionsData = [
            ['assignment_id' => $aPhys1->id, 'course_enrollment_id' => $enrAPhys->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 95, 'grade' => 95, 'teacher_notes' => 'ممتاز جداً يا أحمد! حل نموذجي لدوائر التوالي والتوازي واستنتاج دقيق للمقاومة النوعية.', 'submitted_at' => now()->subDays(12)],
            ['assignment_id' => $aPhys2->id, 'course_enrollment_id' => $enrAPhys->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 88, 'grade' => 88, 'teacher_notes' => 'أداء ممتاز، راجع فقط إشارة القوة الدافعة في الحلقة المغلقة الثانية بقانون كيرشوف.', 'submitted_at' => now()->subDays(10)],
            ['assignment_id' => $aPhys3->id, 'course_enrollment_id' => $enrAPhys->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 100,'grade' => 100,'teacher_notes' => 'علامة كاملة 100%! إجابات رائعة وتطبيق صحيح لقاعدة فلمنج لليد اليسرى.', 'submitted_at' => now()->subDays(7)],
            ['assignment_id' => $aChem1->id, 'course_enrollment_id' => $enrAChem->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 92, 'grade' => 92, 'teacher_notes' => 'أحسنت في تسمية المركبات وفق الأيوباك وتمييز الألكانات المتفرعة بدقة.', 'submitted_at' => now()->subDays(11)],
            ['assignment_id' => $aChem2->id, 'course_enrollment_id' => $enrAChem->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 85, 'grade' => 85, 'teacher_notes' => 'جيد جداً، تطبيق ممتاز لقاعدة ماركوفنيكوف مع تريكات هدرجة الإيثاين.', 'submitted_at' => now()->subDays(8)],
            ['assignment_id' => $aMath1->id, 'course_enrollment_id' => $enrAMath->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 96, 'grade' => 96, 'teacher_notes' => 'عبقري يا أحمد في حساب نهايات الدوال المثلثية واشتقاق الدوال المركبة.', 'submitted_at' => now()->subDays(9)],
            ['assignment_id' => $aMath2->id, 'course_enrollment_id' => $enrAMath->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 90, 'grade' => 90, 'teacher_notes' => 'حل هندسي بارع لمعادلة المماس والمشتقات الضمنية العليا.', 'submitted_at' => now()->subDays(5)],
            ['assignment_id' => $aAr1->id,   'course_enrollment_id' => $enrAAr->id,   'status' => SubmissionStatus::COMPLETED, 'score' => 94, 'grade' => 94, 'teacher_notes' => 'إعراب دقيق وشامل لأعمال اسم الفاعل ونائب الفاعل. بارك الله فيك.', 'submitted_at' => now()->subDays(6)],
            ['assignment_id' => $aBio1->id,  'course_enrollment_id' => $enrABio->id,  'status' => SubmissionStatus::COMPLETED, 'score' => 88, 'grade' => 88, 'teacher_notes' => 'شرح ممتاز لآلية انزلاق الخيوط العضلية ودور الكالسيوم وATP.', 'submitted_at' => now()->subDays(4)],
        ];
        foreach ($submissionsData as $sub) {
            AssignmentSubmission::updateOrCreate(
                ['assignment_id' => $sub['assignment_id'], 'student_user_id' => $sAhmed->id],
                $sub
            );
        }

        // 14. Live Sessions
        LiveSession::withoutEvents(function () use ($sAhmed, $sMariam, $sOmar, $sNada, $tAhmed, $tSarah, $tOmar, $tFatma, $tTarek, $tHoda, $tKareem, $tMedhat, $tHossam, $subPhysics, $subChem, $subMath, $subBio, $subAr, $subGeo, $subEng, $subAppMath, $subProg, $subHist, $cPhysics, $cChem, $cMath, $cBio, $cArabic, $cGeo, $cEnglish, $cAppMath, $cProg, $cHistory) {
            LiveSession::query()->forceDelete();

            $liveSessionsList = [
                [
                    'title' => 'فيزياء — مراجعة كيرشوف وشبكات الجهد الكهربي (LIVE الآن)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tAhmed->id, 'subject_id' => $subPhysics->id, 'course_id' => $cPhysics->id,
                    'start_at' => now()->subMinutes(15), 'end_at' => now()->addMinutes(45), 'scheduled_at' => now()->subMinutes(15),
                    'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/phys-kirchhoff-live', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'كيمياء عضوية — الألكينات وتفاعلات الإضافة (خلال 15 دقيقة)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tSarah->id, 'subject_id' => $subChem->id, 'course_id' => $cChem->id,
                    'start_at' => now()->addMinutes(15), 'end_at' => now()->addMinutes(75), 'scheduled_at' => now()->addMinutes(15),
                    'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/chem-alkenes-soon', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'كيمياء — حصة تجريبية مجانية: أسرار التفكير النقدي في الكيمياء',
                    'student_user_id' => $sMariam->id, 'teacher_profile_id' => $tSarah->id, 'subject_id' => $subChem->id, 'course_id' => $cChem->id,
                    'start_at' => now()->addMinutes(25), 'end_at' => now()->addMinutes(85), 'scheduled_at' => now()->addMinutes(25),
                    'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => true,
                    'meeting_link' => 'https://meet.google.com/chem-free-demo', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'تفاضل وتكامل — نهايات الدوال المثلثية والاشتقاق الضمني (بعد ساعتين)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tOmar->id, 'subject_id' => $subMath->id, 'course_id' => $cMath->id,
                    'start_at' => now()->addHours(2), 'end_at' => now()->addHours(3)->addMinutes(15), 'scheduled_at' => now()->addHours(2),
                    'duration_minutes' => 75, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/math-calculus-today', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'أحياء — تضاعف الحمض النووي DNA وتخليق البروتين (الليلة الساعة 8)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tFatma->id, 'subject_id' => $subBio->id, 'course_id' => $cBio->id,
                    'start_at' => now()->setTime(20, 0), 'end_at' => now()->setTime(21, 15), 'scheduled_at' => now()->setTime(20, 0),
                    'duration_minutes' => 75, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/bio-dna-tonight', 'meeting_platform' => 'zoom'
                ],
                [
                    'title' => 'لغة عربية — أسرار إعراب المنصوبات وشواهد البلاغة (غداً الساعة 4)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tTarek->id, 'subject_id' => $subAr->id, 'course_id' => $cArabic->id,
                    'start_at' => now()->addDay()->setTime(16, 0), 'end_at' => now()->addDay()->setTime(17, 15), 'scheduled_at' => now()->addDay()->setTime(16, 0),
                    'duration_minutes' => 75, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/arabic-grammar-tmrw', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'جيولوجيا — التراكيب الجيولوجية الأولية والفوالق والطيات (غداً الساعة 6)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tMedhat->id, 'subject_id' => $subGeo->id, 'course_id' => $cGeo->id,
                    'start_at' => now()->addDay()->setTime(18, 0), 'end_at' => now()->addDay()->setTime(19, 0), 'scheduled_at' => now()->addDay()->setTime(18, 0),
                    'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/geology-folds-tmrw', 'meeting_platform' => 'zoom'
                ],
                [
                    'title' => 'English — Academic Essay Writing & Advanced Inversions (بعد يومين)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tHoda->id, 'subject_id' => $subEng->id, 'course_id' => $cEnglish->id,
                    'start_at' => now()->addDays(2)->setTime(17, 0), 'end_at' => now()->addDays(2)->setTime(18, 0), 'scheduled_at' => now()->addDays(2)->setTime(17, 0),
                    'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/eng-essay-live', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'استاتيكا — اتزان القوى المتلاقية ونظرية لامي (بعد 3 أيام)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tOmar->id, 'subject_id' => $subAppMath->id, 'course_id' => $cAppMath->id,
                    'start_at' => now()->addDays(3)->setTime(19, 0), 'end_at' => now()->addDays(3)->setTime(20, 15), 'scheduled_at' => now()->addDays(3)->setTime(19, 0),
                    'duration_minutes' => 75, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/math-statics-live', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'Python & AI — خوارزميات تعلم الآلة ونماذج التصنيف (بعد 4 أيام)',
                    'student_user_id' => $sOmar->id, 'teacher_profile_id' => $tKareem->id, 'subject_id' => $subProg->id, 'course_id' => $cProg->id,
                    'start_at' => now()->addDays(4)->setTime(16, 0), 'end_at' => now()->addDays(4)->setTime(17, 30), 'scheduled_at' => now()->addDays(4)->setTime(16, 0),
                    'duration_minutes' => 90, 'status' => 'scheduled', 'is_free_demo' => true,
                    'meeting_link' => 'https://meet.google.com/python-ai-demo', 'meeting_platform' => 'google_meet'
                ],
                [
                    'title' => 'تاريخ — بناء الدولة الحديثة في عهد محمد علي والنهضة المصرية (بعد 5 أيام)',
                    'student_user_id' => $sNada->id, 'teacher_profile_id' => $tHossam->id, 'subject_id' => $subHist->id, 'course_id' => $cHistory->id,
                    'start_at' => now()->addDays(5)->setTime(18, 0), 'end_at' => now()->addDays(5)->setTime(19, 0), 'scheduled_at' => now()->addDays(5)->setTime(18, 0),
                    'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/hist-modern-egypt', 'meeting_platform' => 'zoom'
                ],
                [
                    'title' => 'فيزياء — قانون أوم وحساب المقاومة المكافئة لمجموعات التوازي',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tAhmed->id, 'subject_id' => $subPhysics->id, 'course_id' => $cPhysics->id,
                    'start_at' => now()->subDays(13)->setTime(18, 0), 'end_at' => now()->subDays(13)->setTime(19, 0), 'scheduled_at' => now()->subDays(13)->setTime(18, 0),
                    'duration_minutes' => 60, 'status' => 'completed', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/phys-hist-1', 'meeting_platform' => 'google_meet',
                    'attendance_status' => 'present'
                ],
                [
                    'title' => 'كيمياء — تسمية الهيدروكربونات وفق نظام الأيوباك IUPAC',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tSarah->id, 'subject_id' => $subChem->id, 'course_id' => $cChem->id,
                    'start_at' => now()->subDays(11)->setTime(17, 0), 'end_at' => now()->subDays(11)->setTime(18, 0), 'scheduled_at' => now()->subDays(11)->setTime(17, 0),
                    'duration_minutes' => 60, 'status' => 'completed', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/chem-hist-1', 'meeting_platform' => 'google_meet',
                    'attendance_status' => 'present'
                ],
                [
                    'title' => 'رياضيات — مفهوم المماس والمشتقة الأولى هندسياً وفيزيائياً',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tOmar->id, 'subject_id' => $subMath->id, 'course_id' => $cMath->id,
                    'start_at' => now()->subDays(9)->setTime(19, 0), 'end_at' => now()->subDays(9)->setTime(20, 15), 'scheduled_at' => now()->subDays(9)->setTime(19, 0),
                    'duration_minutes' => 75, 'status' => 'completed', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/math-hist-1', 'meeting_platform' => 'zoom',
                    'attendance_status' => 'present'
                ],
                [
                    'title' => 'لغة عربية — الجملة الاسمية والخبر المقدم وجوباً وجوازاً',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tTarek->id, 'subject_id' => $subAr->id, 'course_id' => $cArabic->id,
                    'start_at' => now()->subDays(6)->setTime(16, 0), 'end_at' => now()->subDays(6)->setTime(17, 15), 'scheduled_at' => now()->subDays(6)->setTime(16, 0),
                    'duration_minutes' => 75, 'status' => 'completed', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/ar-hist-1', 'meeting_platform' => 'google_meet',
                    'attendance_status' => 'present'
                ],
                [
                    'title' => 'أحياء — الانقسام الميوزي وتكوين الأمشاج والكروموسومات (عذر مقبول)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tFatma->id, 'subject_id' => $subBio->id, 'course_id' => $cBio->id,
                    'start_at' => now()->subDays(7)->setTime(20, 0), 'end_at' => now()->subDays(7)->setTime(21, 0), 'scheduled_at' => now()->subDays(7)->setTime(20, 0),
                    'duration_minutes' => 60, 'status' => 'completed', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/bio-excused', 'meeting_platform' => 'google_meet',
                    'attendance_status' => 'excused'
                ],
                [
                    'title' => 'جيولوجيا — دورة الصخور وتكتونية الألواح القارية والمحيطية',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tMedhat->id, 'subject_id' => $subGeo->id, 'course_id' => $cGeo->id,
                    'start_at' => now()->subDays(2)->setTime(18, 0), 'end_at' => now()->subDays(2)->setTime(19, 0), 'scheduled_at' => now()->subDays(2)->setTime(18, 0),
                    'duration_minutes' => 60, 'status' => 'completed', 'is_free_demo' => false,
                    'meeting_link' => 'https://meet.google.com/geo-hist-1', 'meeting_platform' => 'google_meet',
                    'attendance_status' => 'present'
                ],
                [
                    'title' => 'فيزياء — دوائر التيار المتردد والرنين المغناطيسي (ملغاة من المعلم)',
                    'student_user_id' => $sAhmed->id, 'teacher_profile_id' => $tAhmed->id, 'subject_id' => $subPhysics->id, 'course_id' => $cPhysics->id,
                    'start_at' => now()->subDays(5)->setTime(18, 0), 'end_at' => now()->subDays(5)->setTime(19, 0), 'scheduled_at' => now()->subDays(5)->setTime(18, 0),
                    'duration_minutes' => 60, 'status' => 'cancelled_by_teacher', 'is_free_demo' => false,
                    'meeting_link' => '', 'meeting_platform' => 'google_meet'
                ],
            ];

            foreach ($liveSessionsList as $sessData) {
                LiveSession::create($sessData);
            }
        });

        // 15. Exceptions
        ExceptionRequest::withoutEvents(function () use ($sAhmed, $cBio, $cPhysics, $cMath, $cArabic) {
            ExceptionRequest::where('student_user_id', $sAhmed->id)->forceDelete();
            $exceptionsData = [
                ['student_user_id' => $sAhmed->id, 'course_id' => $cBio->id,     'scope' => 'course', 'is_global' => false, 'status' => 'approved', 'reason' => 'ظرف صحي طارئ وإجراء فحص طبي بالمستشفى مرفق التقرير المعتمد.', 'reviewed_at' => now()->subDays(7), 'admin_notes' => 'تم قبول العذر الطبي واسترداد الحصة لرصيد الطالب.'],
                ['student_user_id' => $sAhmed->id, 'course_id' => $cPhysics->id, 'scope' => 'course', 'is_global' => false, 'status' => 'pending',  'reason' => 'مشاركة رسمية في تصفيات مسابقة أولمبياد الفيزياء والرياضيات بمدارس STEM.', 'reviewed_at' => null, 'admin_notes' => null],
                ['student_user_id' => $sAhmed->id, 'course_id' => $cMath->id,    'scope' => 'course', 'is_global' => false, 'status' => 'approved', 'reason' => 'تزامن موعد الحصة مع امتحان شهر رسمي بالمدرسة.', 'reviewed_at' => now()->subDays(3), 'admin_notes' => 'تمت الموافقة والتنسيق لحضور جلسة تعويضية.'],
                ['student_user_id' => $sAhmed->id, 'course_id' => $cArabic->id,  'scope' => 'course', 'is_global' => false, 'status' => 'rejected', 'reason' => 'انقطاع خدمة الإنترنت المنزلي لعدة دقائق.', 'reviewed_at' => now()->subDays(1), 'admin_notes' => 'تم الرفض لعدم تقديم إشعار مسبق قبل الحصة بمدة كافية وفق اللائحة.'],
            ];
            foreach ($exceptionsData as $exc) {
                ExceptionRequest::create($exc);
            }
        });

        // 16. Notifications
        UserNotification::where('user_id', $sAhmed->id)->delete();
        $notifsList = [
            [$sAhmed->id, 'session_reminder',     '⏰ حصة الفيزياء المباشرة بدأت الآن',              'حصة قوانين كيرشوف وشبكات الجهد بدأت الآن مع د. أحمد محمود. انضم للبث فوراً!', false, now()->subMinutes(14)],
            [$sAhmed->id, 'session_reminder',     '⚡ حصة الكيمياء العضوية تبدأ خلال 15 دقيقة',       'استعد لحصة الألكينات وتفاعلات الإضافة مع أ. سارة محمد. جهز دفتر الملاحظات!', false, now()->subMinutes(5)],
            [$sAhmed->id, 'assignment_graded',    '🏆 علامة كاملة 100% في اختبار الفيزياء 3!',         'تهانينا يا أحمد! أحرزت الدرجة النهائية (100/100) في اختبار المجال المغناطيسي.', true,  now()->subDays(7)],
            [$sAhmed->id, 'package_info',         '🎁 تم إضافة حصتين بونص لرصيدك لتفوقك الدراسي',     'تقديراً لتفوقك في الفيزياء تم إضافة 2 حصة مجانية لرصيد باقتك.', true,  now()->subDays(3)],
            [$sAhmed->id, 'assignment_graded',    '📝 تم تصحيح واجب التفاضل والتكامل — درجتك: 96%',   'أداء عبقري في نهايات الدوال المثلثية. ملاحظات د. عمر خالد متوفرة بصفحتك.', true,  now()->subDays(9)],
            [$sAhmed->id, 'exception_status',     '✅ تم قبول عذر الغياب الطبي لحصة الأحياء',         'تمت مراجعة تقريرك الطبي وقبول العذر وإعادة الحصة لرصيد باقتك تلقائياً.', true,  now()->subDays(7)],
            [$sAhmed->id, 'exception_status',     '📋 طلب استثناء أولمبياد العلوم قيد المراجعة',      'طلبك للمشاركة في مسابقة STEM قيد دراسة الإدارة وسنوافيك بالرد قريباً.', false, now()->subHours(8)],
            [$sAhmed->id, 'enrollment_confirmed', '🎓 تم تأكيد تسجيلك في كورس الجيولوجيا الشامل',      'أهلاً بك في كورس الجيولوجيا مع أ. مدحت الشناوي. تم فتح المنهج بنجاح.', true,  now()->subDays(10)],
            [$sAhmed->id, 'session_reminder',     '📅 حصة الرياضيات الليلة الساعة 8 مساءاً',         'تكامل الدوال الأسية واللوغاريتمية مع د. عمر خالد. نراك في الموعد!', false, now()->subHours(4)],
            [$sAhmed->id, 'assignment_graded',    '📖 تم تصحيح واجب اللغة العربية — درجتك: 94%',      'أحسنت في إعراب المشتقات العاملة والبلاغة مع د. طارق فؤاد.', true,  now()->subDays(6)],
            [$sAhmed->id, 'package_info',         '📦 باقتك نشطة: 18 حصة متبقية صالحة لـ 45 يوماً',   'باقة الثانوية العامة الفائقة تعمل بكفاءة ويمكنك استخدام رصيدك بكافة الكورسات.', true,  now()->subDays(2)],
        ];

        foreach ($notifsList as [$uid, $type, $title, $body, $read, $at]) {
            UserNotification::create([
                'user_id' => $uid,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'is_read' => $read,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }

        if ($this->command) {
            $this->command->info('🎉 All Egyptian Educational Dummy Data Seeded Successfully & Ready for Viewing!');
        }
    }

    private function upsertTeacher(string $slug, string $name, string $email, string $phone, string $title, string $spec, string $bio, int $exp, float $rating, int $students, bool $featured): TeacherProfile
    {
        $user = User::updateOrCreate(['email' => $email], [
            'name' => $name, 'phone' => $phone, 'password' => $this->getPassword(), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()
        ]);
        return TeacherProfile::updateOrCreate(['slug' => $slug], [
            'user_id' => $user->id, 'title' => $title, 'specialization' => $spec, 'bio' => $bio, 'years_experience' => $exp, 'rating_avg' => $rating, 'students_count' => $students, 'photo' => 'images/instructor_portrait.png', 'is_featured' => $featured, 'is_public' => true, 'show_contact_info' => true
        ]);
    }

    private function upsertStudent(string $email, string $name, string $phone, int $gradeId, string $school, bool $usedFree, array $subjectIds = []): array
    {
        $user    = User::updateOrCreate(['email' => $email], ['name' => $name, 'phone' => $phone, 'password' => $this->getPassword(), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        $profile = StudentProfile::updateOrCreate(['user_id' => $user->id], ['grade_level_id' => $gradeId, 'school_name' => $school, 'has_used_free_session' => $usedFree]);
        if (!empty($subjectIds)) {
            $profile->subjects()->sync($subjectIds);
        }
        return [$user, $profile];
    }

    private function upsertStudentPackage(int $userId, int $tplId, int $total, int $used, int $remaining, string $status, int $daysAgo, int $expDays): StudentPackage
    {
        return StudentPackage::updateOrCreate(
            ['student_user_id' => $userId, 'package_template_id' => $tplId],
            ['total_sessions' => $total, 'used_sessions' => $used, 'remaining_sessions' => $remaining, 'status' => $status, 'activated_at' => now()->subDays($daysAgo), 'expires_at' => now()->addDays($expDays)]
        );
    }

    private function createSessions(int $courseId, array $sessions): array
    {
        return CourseSession::withoutEvents(function () use ($courseId, $sessions) {
            $result = [];
            foreach ($sessions as [$order, $title, $desc, $freeDemo, $duration]) {
                $result[] = CourseSession::updateOrCreate(
                    ['course_id' => $courseId, 'sort_order' => $order],
                    ['title' => $title, 'description' => $desc, 'duration_minutes' => $duration, 'is_free_demo' => $freeDemo, 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
                );
            }
            return $result;
        });
    }

    private function makeAssignment(int $sessionId, int $courseId, string $title, int $durationMins, $startAt, array $questions): Assignment
    {
        $a = Assignment::updateOrCreate(
            ['course_session_id' => $sessionId],
            ['course_id' => $courseId, 'title' => $title, 'description' => 'أجب على الأسئلة التالية بدقة وعناية وفق المنهج المصري.', 'passing_grade' => 70, 'passing_score' => 70, 'duration_minutes' => $durationMins, 'status' => 'published', 'start_at' => $startAt]
        );
        $order = 1;
        foreach ($questions as [$text, $pts, $options]) {
            $q = AssignmentQuestion::updateOrCreate(
                ['assignment_id' => $a->id, 'sort_order' => $order++],
                ['question_text' => $text, 'question_type' => 'text', 'points' => $pts, 'is_multiple_choice' => false]
            );
            $optOrder = 1;
            foreach ($options as $optText => $isCorrect) {
                AssignmentQuestionOption::updateOrCreate(
                    ['question_id' => $q->id, 'option_text' => $optText],
                    ['sort_order' => $optOrder++, 'is_correct' => $isCorrect]
                );
            }
        }
        return $a;
    }
}

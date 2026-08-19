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
    public function run(): void
    {
        $this->command->info('🏫 Starting Egyptian Educational Dummy Data Seeding...');

        // 1. Grade Levels
        $g12 = GradeLevel::updateOrCreate(['slug' => 'grade-12'], ['name' => 'الصف الثالث الثانوي (الثانوية العامة & STEM)', 'sort_order' => 1]);
        $g11 = GradeLevel::updateOrCreate(['slug' => 'grade-11'], ['name' => 'الصف الثاني الثانوي', 'sort_order' => 2]);
        $g10 = GradeLevel::updateOrCreate(['slug' => 'grade-10'], ['name' => 'الصف الأول الثانوي', 'sort_order' => 3]);
        $g9  = GradeLevel::updateOrCreate(['slug' => 'grade-9'],  ['name' => 'الصف الثالث الإعدادي', 'sort_order' => 4]);

        // 2. Categories & Subjects
        $catSci  = Category::updateOrCreate(['slug' => 'natural-sciences'],      ['name' => 'العلوم الطبيعية والتطبيقية', 'color_theme' => '#0D9488']);
        $catMath = Category::updateOrCreate(['slug' => 'math-and-tech'],         ['name' => 'الرياضيات والتكنولوجيا',     'color_theme' => '#2563EB']);
        $catLang = Category::updateOrCreate(['slug' => 'languages-and-literature'], ['name' => 'اللغات والآداب',          'color_theme' => '#EA580C']);

        $subPhysics = Subject::updateOrCreate(['slug' => 'physics'],     ['category_id' => $catSci->id,  'name' => 'الفيزياء',                  'description' => 'الكهرومغناطيسية والفيزياء الحديثة والنووية.', 'sort_order' => 1]);
        $subChem    = Subject::updateOrCreate(['slug' => 'chemistry'],   ['category_id' => $catSci->id,  'name' => 'الكيمياء',                  'description' => 'الكيمياء العضوية والتحليل الكيميائي التفاعلي.', 'sort_order' => 2]);
        $subBio     = Subject::updateOrCreate(['slug' => 'biology'],     ['category_id' => $catSci->id,  'name' => 'الأحياء',                   'description' => 'البيولوجيا الخلوية والجينات ووظائف الأعضاء.', 'sort_order' => 3]);
        $subMath    = Subject::updateOrCreate(['slug' => 'mathematics'], ['category_id' => $catMath->id, 'name' => 'الرياضيات',                 'description' => 'التفاضل والتكامل والجبر والهندسة الفراغية.', 'sort_order' => 4]);
        $subProg    = Subject::updateOrCreate(['slug' => 'programming'], ['category_id' => $catMath->id, 'name' => 'البرمجة والذكاء الاصطناعي', 'description' => 'Python والخوارزميات وتعلم الآلة.', 'sort_order' => 5]);
        $subEng     = Subject::updateOrCreate(['slug' => 'english'],     ['category_id' => $catLang->id, 'name' => 'اللغة الإنجليزية',          'description' => 'القواعد والبلاغة والقراءة الأكاديمية.', 'sort_order' => 6]);
        $subAr      = Subject::updateOrCreate(['slug' => 'arabic'],      ['category_id' => $catLang->id, 'name' => 'اللغة العربية',             'description' => 'النحو والصرف والبلاغة والأدب العربي.', 'sort_order' => 7]);

        // 3. Admin
        $admin = User::updateOrCreate(['email' => 'admin@elite.edu'], ['name' => 'المدير العام', 'phone' => '+201000000001', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        AdminProfile::updateOrCreate(['user_id' => $admin->id]);

        // 4. Teachers
        $tAhmed  = $this->upsertTeacher('dr-ahmed-mahmoud', 'د. أحمد محمود',  'dr.ahmed.mahmoud@elite.edu',  '+201000000101', 'أستاذ الفيزياء',               'Physics',     'دكتوراه في الفيزياء التطبيقية. خبرة 15 عاماً في تدريس الثانوية.',            15, 4.9, 2450, true);
        $tSarah  = $this->upsertTeacher('sarah-mohamed',    'أ. سارة محمد',   'sarah.mohamed@elite.edu',     '+201000000102', 'أستاذة الكيمياء العضوية',     'Chemistry',   'خبرة 12 عاماً في شرح الكيمياء بأساليب التفكير النقدي وتجارب المعامل.',       12, 4.8, 1890, true);
        $tOmar   = $this->upsertTeacher('dr-omar-khaled',   'د. عمر خالد',    'dr.omar.khaled@elite.edu',    '+201000000103', 'أستاذ الرياضيات',              'Mathematics', 'متخصص في التفاضل والتكامل. درّب أكثر من 10 آلاف طالب.',                     18, 4.9, 3100, true);
        $tFatma  = $this->upsertTeacher('dr-fatma-ali',     'د. فاطمة علي',   'fatma.ali@elite.edu',         '+201000000104', 'أستاذة الأحياء',               'Biology',     'ماجستير في العلوم البيولوجية. شرح بالرسوم ثلاثية الأبعاد.',                  10, 4.9, 1650, true);
        $tKareem = $this->upsertTeacher('eng-kareem-zaki',  'م. كريم زكي',    'kareem.zaki@elite.edu',       '+201000000105', 'خبير البرمجيات والذكاء الاصطناعي', 'Programming', 'مهندس برمجيات ومحاضر في Python وخوارزميات وتعلم الآلة.',              8, 4.9, 2200, true);
        $tHoda   = $this->upsertTeacher('hoda-mahmoud',     'أ. هدى محمود',   'hoda.mahmoud@elite.edu',      '+201000000106', 'أستاذة اللغة الإنجليزية',     'English',     'خبير المناهج البريطانية. قواعد وبلاغة وقراءة مستفيضة.',                      14, 4.8, 1780, false);
        $tTarek  = $this->upsertTeacher('dr-tarek-fouad',   'د. طارق فؤاد',   'tarek.fouad@elite.edu',       '+201000000107', 'استشاري اللغة العربية',       'Arabic',      'دكتوراه في البلاغة والنقد الأدبي. 22 عاماً تدريس.',                          22, 5.0, 4200, true);
        $tNouran = $this->upsertTeacher('nouran-samy',      'أ. نوران سامي',  'nouran.samy@elite.edu',       '+201000000108', 'محاضرة الذكاء الاصطناعي',     'AI & Tech',   'مدربة معتمدة في الذكاء الاصطناعي والروبوتيك.',                               7, 4.9, 1350, false);

        // 5. Package Templates
        $pkgS  = PackageTemplate::updateOrCreate(['name' => 'باقة المادة المنفردة (4 حصص)'],            ['sessions_count' => 4,  'price' => 200.00, 'description' => 'باقة لكورس مادة واحدة.', 'is_active' => true]);
        $pkgM  = PackageTemplate::updateOrCreate(['name' => 'باقة التميز الشهري (12 حصة)'],             ['sessions_count' => 12, 'price' => 450.00, 'description' => 'باقة شهرية تغطي 12 حصة تفاعلية.', 'is_active' => true]);
        $pkgL  = PackageTemplate::updateOrCreate(['name' => 'باقة الثانوية العامة الفائقة (24 حصة)'],  ['sessions_count' => 24, 'price' => 800.00, 'description' => 'باقة شاملة لجميع المواد.', 'is_active' => true]);

        // 6. Parents & Students
        $parent1 = User::updateOrCreate(['email' => 'parent@elite.edu'],  ['name' => 'أ. خالد محمود', 'phone' => '+201000000004', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        $pp1     = ParentProfile::updateOrCreate(['user_id' => $parent1->id]);
        $parent2 = User::updateOrCreate(['email' => 'parent2@elite.edu'], ['name' => 'م. سامي عبدالرحمن', 'phone' => '+201000000008', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        $pp2     = ParentProfile::updateOrCreate(['user_id' => $parent2->id]);

        [$sAhmed,   $pAhmed]   = $this->upsertStudent('ahmed@elite.edu',   'أحمد خالد',   '+201000000005', $g12->id, 'مدرسة المتفوقين STEM', true,  [$subPhysics->id, $subMath->id]);
        [$sMariam,  $pMariam]  = $this->upsertStudent('mariam@elite.edu',  'مريم خالد',   '+201000000006', $g11->id, 'مدرسة النيل الدولية',  true,  [$subChem->id, $subBio->id]);
        [$sOmar,    $pOmar]    = $this->upsertStudent('omar@elite.edu',    'عمر خالد',    '+201000000007', $g10->id, 'مدرسة الأورمان لغات',  false, [$subProg->id]);
        [$sNada,    $pNada]    = $this->upsertStudent('nada@elite.edu',    'ندى سامي',    '+201000000009', $g12->id, 'مدرسة المستقبل',       true,  [$subPhysics->id, $subChem->id, $subAr->id]);
        [$sYoussef, $pYoussef] = $this->upsertStudent('youssef@elite.edu', 'يوسف سامي',  '+201000000010', $g11->id, 'مدرسة الثروة الإسلامية',false, [$subMath->id]);
        [$sLaila,   $pLaila]   = $this->upsertStudent('laila@elite.edu',   'ليلى أحمد',   '+201000000011', $g9->id,  'مدرسة الإبداع',        false, [$subProg->id, $subEng->id]);

        $pp1->students()->syncWithoutDetaching([$sAhmed->id => ['relationship' => 'Son'], $sMariam->id => ['relationship' => 'Daughter'], $sOmar->id => ['relationship' => 'Son']]);
        $pp2->students()->syncWithoutDetaching([$sNada->id  => ['relationship' => 'Daughter'], $sYoussef->id => ['relationship' => 'Son']]);

        // 7. Student Packages
        $pkgAhmed   = $this->upsertStudentPackage($sAhmed->id,   $pkgM->id, 12, 5,  7,  'active', 10, 20);
        $pkgMariam  = $this->upsertStudentPackage($sMariam->id,  $pkgM->id, 12, 2,  10, 'active', 5,  25);
        $pkgOmar    = $this->upsertStudentPackage($sOmar->id,    $pkgS->id, 4,  1,  3,  'active', 2,  28);
        $pkgNada    = $this->upsertStudentPackage($sNada->id,    $pkgL->id, 24, 8,  16, 'active', 15, 15);
        $pkgYoussef = $this->upsertStudentPackage($sYoussef->id, $pkgS->id, 4,  0,  4,  'active', 1,  29);
        foreach ([$pkgAhmed, $pkgMariam, $pkgNada] as $pkg) {
            PackageTransaction::firstOrCreate(['student_package_id' => $pkg->id, 'type' => 'payment_activation'], ['sessions_delta' => $pkg->total_sessions, 'balance_before' => 0, 'balance_after' => $pkg->total_sessions, 'reason' => 'تفعيل الباقة', 'created_at' => now()->subDays(10)]);
        }

        // 8. Courses
        $cPhysics = Course::updateOrCreate(['slug' => 'physics-electricity-g12'], ['subject_id' => $subPhysics->id, 'teacher_id' => $tAhmed->id, 'grade_level_id' => $g12->id, 'title' => 'الفيزياء الكهربية والمغناطيسية والحديثة — الثالث الثانوي', 'description' => 'كورس متكامل يغطي قوانين كيرشوف والكهرومغناطيسية والفيزياء الحديثة النووية.', 'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 8, 'session_duration_minutes' => 60]);
        $cChem    = Course::updateOrCreate(['slug' => 'organic-chemistry-g12'],   ['subject_id' => $subChem->id,    'teacher_id' => $tSarah->id,  'grade_level_id' => $g12->id, 'title' => 'الكيمياء العضوية والتحليل الكيميائي — الثالث الثانوي',   'description' => 'هيدروكربونات، الألكانات والألكينات، المركبات الأروماتية.', 'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 7, 'session_duration_minutes' => 60]);
        $cBio     = Course::updateOrCreate(['slug' => 'biology-genetics-g12'],    ['subject_id' => $subBio->id,     'teacher_id' => $tFatma->id,  'grade_level_id' => $g12->id, 'title' => 'الأحياء وعلم الجينات والوراثة — الثالث الثانوي',           'description' => 'الخلية، DNA، قوانين مندل، الجهاز العصبي والبيئة.', 'is_active' => true, 'has_free_demo' => false, 'is_accredited' => true, 'sessions_count' => 6, 'session_duration_minutes' => 60]);
        $cMath    = Course::updateOrCreate(['slug' => 'calculus-algebra-g11'],    ['subject_id' => $subMath->id,    'teacher_id' => $tOmar->id,   'grade_level_id' => $g11->id, 'title' => 'التفاضل والتكامل والجبر والهندسة الفراغية — الثاني الثانوي', 'description' => 'النهايات، الاشتقاق، التكامل المحدود وغير المحدود.', 'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 8, 'session_duration_minutes' => 75]);
        $cProg    = Course::updateOrCreate(['slug' => 'python-ai-g10'],           ['subject_id' => $subProg->id,    'teacher_id' => $tKareem->id, 'grade_level_id' => $g10->id, 'title' => 'أساسيات البرمجة بـ Python والذكاء الاصطناعي — الأول الثانوي', 'description' => 'Python من الصفر: متغيرات، دوال، برمجة كائنية، وأول نموذج ذكاء اصطناعي.', 'is_active' => true, 'has_free_demo' => true, 'is_accredited' => false, 'sessions_count' => 6, 'session_duration_minutes' => 90]);
        $cEnglish = Course::updateOrCreate(['slug' => 'english-advanced-g11'],    ['subject_id' => $subEng->id,     'teacher_id' => $tHoda->id,   'grade_level_id' => $g11->id, 'title' => 'اللغة الإنجليزية المتقدمة — القواعد والبلاغة والأدب',       'description' => 'القواعد المتقدمة، النصوص الأدبية، التعبير الأكاديمي.', 'is_active' => true, 'has_free_demo' => false, 'is_accredited' => true, 'sessions_count' => 6, 'session_duration_minutes' => 60]);
        $cArabic  = Course::updateOrCreate(['slug' => 'arabic-grammar-g12'],      ['subject_id' => $subAr->id,      'teacher_id' => $tTarek->id,  'grade_level_id' => $g12->id, 'title' => 'اللغة العربية — النحو والصرف والبلاغة والأدب — الثالث الثانوي', 'description' => 'مراجعة شاملة للنحو والبلاغة والأدب مع نماذج الثانوية.', 'is_active' => true, 'has_free_demo' => true, 'is_accredited' => true, 'sessions_count' => 6, 'session_duration_minutes' => 60]);
        $cAI      = Course::updateOrCreate(['slug' => 'ai-robotics-g9'],          ['subject_id' => $subProg->id,    'teacher_id' => $tNouran->id, 'grade_level_id' => $g9->id,  'title' => 'مقدمة في الروبوتيك والذكاء الاصطناعي للناشئين',               'description' => 'الروبوتيك، Arduino، Scratch، ومقدمة Python للمرحلة الإعدادية.', 'is_active' => true, 'has_free_demo' => true, 'is_accredited' => false, 'sessions_count' => 5, 'session_duration_minutes' => 60]);

        // 9. Recorded Sessions
        $physSess = $this->createSessions($cPhysics->id, [
            [1, 'الجلسة 1: التيار الكهربي وقانون أوم',              'شحنة، تيار، جهد، مقاومة وتطبيقات الدوائر المبسطة.',                                                       true,  60],
            [2, 'الجلسة 2: توصيل المقاومات وكيرشوف الأول',          'توصيل التوالي والتوازي وقانون كيرشوف للتيارات عند نقاط التفرع.',                                          false, 60],
            [3, 'الجلسة 3: قانون كيرشوف الثاني والدوائر المركبة',   'تطبيق قانون الجهود على الحلقات وحل شبكات كهربية معقدة.',                                                 false, 75],
            [4, 'الجلسة 4: الطاقة والقدرة الكهربية',                'حساب الطاقة والقدرة وتطبيقات على الأجهزة الكهربية.',                                                      false, 60],
            [5, 'الجلسة 5: المجال المغناطيسي وقوة لورنتز',          'قوة لورنتز والتأثير المغناطيسي على الموصلات الحاملة للتيار.',                                             false, 75],
            [6, 'الجلسة 6: الحث الكهرومغناطيسي وقانون فاراداي',     'قانون فاراداي وقاعدة لينز وتشغيل المولدات والمحركات الكهربية.',                                          false, 75],
            [7, 'الجلسة 7: الفيزياء الحديثة — النسبية والكم',       'تقلص الطول وتمدد الزمن، الطاقة والكتلة، مبدأ عدم اليقين.',                                               false, 60],
            [8, 'الجلسة 8: الفيزياء النووية والانشطار والاندماج',    'التحلل الإشعاعي وقوانين الانشطار والاندماج النووي.',                                                      false, 60],
        ]);
        $chemSess = $this->createSessions($cChem->id, [
            [1, 'الجلسة 1: الهيدروكربونات ومقدمة الكيمياء العضوية',  'تصنيف الألكانات والألكينات والألكاينات وطرق تسميتها.',                                                   true,  60],
            [2, 'الجلسة 2: الألكانات — الخصائص والتفاعلات',         'الخصائص الفيزيائية للألكانات وتفاعل الاستبدال بالهالوجينات.',                                            false, 60],
            [3, 'الجلسة 3: الألكينات والألكاينات',                  'تفاعلات الإضافة وقاعدة ماركوفنيكوف.',                                                                     false, 60],
            [4, 'الجلسة 4: المركبات الأروماتية والبنزين',            'بنية البنزين وتفاعلات الاستبدال الإلكتروفيلي العطرية.',                                                   false, 75],
            [5, 'الجلسة 5: الأحماض الكربوكسيلية والإسترات',         'تكوين الإسترات والصابونة والدهون.',                                                                       false, 60],
            [6, 'الجلسة 6: الأمينات والأميدات',                    'خصائص الأمينات القاعدية وتطبيقاتها الصناعية.',                                                             false, 60],
            [7, 'الجلسة 7: مراجعة ونماذج امتحانات الثانوية',         'حل نماذج امتحانات ثانوية عامة في الكيمياء العضوية.',                                                     false, 75],
        ]);
        $bioSess = $this->createSessions($cBio->id, [
            [1, 'الجلسة 1: تركيب الخلية الحيوانية والنباتية',       'أجزاء الخلية وعضياتها والغشاء البلازمي.',                                                                  false, 60],
            [2, 'الجلسة 2: الانقسام الخلوي — Mitosis وMeiosis',    'مراحل الانقسام المتساوي والاختزالي وأهميتهما.',                                                            false, 60],
            [3, 'الجلسة 3: الحمض النووي DNA وبنية الجينوم',          'اكتشاف DNA وقاعدته الكيميائية ATGC وتضاعف DNA.',                                                         false, 75],
            [4, 'الجلسة 4: الوراثة وقوانين مندل',                  'المربع البانيتي والصفات السائدة والمتنحية.',                                                                false, 60],
            [5, 'الجلسة 5: الجهاز العصبي والهرموني',               'وظائف الدماغ والحبل الشوكي والغدد الصماء.',                                                                false, 60],
            [6, 'الجلسة 6: علم البيئة والنظم البيئية',              'السلاسل الغذائية والتوازن البيئي والتلوث.',                                                                false, 75],
        ]);
        $mathSess = $this->createSessions($cMath->id, [
            [1, 'الجلسة 1: النهايات وقواعد حسابها',                 'تعريف النهاية وقوانين النهايات في اللانهاية.',                                                             true,  75],
            [2, 'الجلسة 2: الاشتقاق ومفهومه الهندسي',              'ميل المماس وقواعد اشتقاق الدوال الجبرية.',                                                                 false, 75],
            [3, 'الجلسة 3: اشتقاق الدوال المثلثية والأسية',        'اشتقاق sin x وcos x وex وln x وقاعدة السلسلة.',                                                           false, 75],
            [4, 'الجلسة 4: الحد الأقصى والأدنى وتطبيقات التفاضل',  'نقاط الإنعطاف والقيم القصوى والمسائل التطبيقية.',                                                        false, 75],
            [5, 'الجلسة 5: التكامل غير المحدود وقاعدة الاستبدال',  'التكامل كعكس الاشتقاق وطريقة التعويض.',                                                                   false, 75],
            [6, 'الجلسة 6: التكامل المحدود والمساحات والأحجام',     'مبرهنة النهائية الأساسية وحساب المساحات والأحجام.',                                                       false, 75],
            [7, 'الجلسة 7: الجبر الخطي والمصفوفات',                 'جمع وضرب المصفوفات والمحددات وطريقة كرامر.',                                                              false, 60],
            [8, 'الجلسة 8: الهندسة الفراغية والإحداثيات',           'المستوى والمستقيم في الفضاء والمسافة بين نقطتين.',                                                         false, 75],
        ]);
        $progSess = $this->createSessions($cProg->id, [
            [1, 'الجلسة 1: مقدمة في Python — المتغيرات والأنواع',   'تثبيت Python وبيئة التطوير والمتغيرات والعمليات الحسابية.',                                               true,  90],
            [2, 'الجلسة 2: الجمل الشرطية والحلقات',                 'if-elif-else وحلقات for وwhile وrange.',                                                                  false, 90],
            [3, 'الجلسة 3: القوائم والقواميس والمجموعات',            'List وTuple وDict وSet والتعديل والبحث.',                                                                 false, 90],
            [4, 'الجلسة 4: الدوال والبرمجة الكائنية',               'تعريف الدوال والكلاسات والكائنات والوراثة.',                                                               false, 90],
            [5, 'الجلسة 5: ملفات البيانات ومكتبات Python',          'قراءة وكتابة الملفات ومكتبات os وjson وrandom.',                                                          false, 90],
            [6, 'الجلسة 6: مقدمة تعلم الآلة — sklearn',             'مفهوم التعلم الآلي ومكتبة scikit-learn ونموذج Logistic Regression.',                                     false, 90],
        ]);
        $engSess = $this->createSessions($cEnglish->id, [
            [1, 'Session 1: Advanced Grammar — Tenses & Conditionals', 'Complex tenses, mixed conditionals, inversion structures.',                                              false, 60],
            [2, 'Session 2: Reading Comprehension & Literary Prose',    'Unseen passages, inference, poetry analysis.',                                                          false, 60],
            [3, 'Session 3: Essay Writing — Argumentative & Descriptive', 'Essay structure, thesis, cohesive devices, IELTS style.',                                            false, 60],
            [4, 'Session 4: Vocabulary — Academic Word List',           'AWL words, collocations, word families.',                                                               false, 60],
            [5, 'Session 5: Shakespeare & Classic Literature',          'Macbeth & Romeo/Juliet themes and dramatic techniques.',                                                 false, 60],
            [6, 'Session 6: Full Mock Exam Paper',                      'Full practice exam with timed conditions and correction.',                                               false, 60],
        ]);
        $arSess = $this->createSessions($cArabic->id, [
            [1, 'الجلسة 1: النحو — الجملة الاسمية والفعلية وأقسامها', 'المبتدأ والخبر والفعل والفاعل والإعراب.',                                                               true,  60],
            [2, 'الجلسة 2: الإعراب — المضاف والصفة والموصوف',         'إعراب المضاف والمضاف إليه والصفة وعطف البيان.',                                                        false, 60],
            [3, 'الجلسة 3: البلاغة — التشبيه والاستعارة والكناية',    'أنواع التشبيه وأركانه والاستعارة والكناية.',                                                             false, 60],
            [4, 'الجلسة 4: البديع — الجناس والطباق والمقابلة',        'التورية والجناس والطباق في الشعر العربي.',                                                              false, 60],
            [5, 'الجلسة 5: الأدب الحديث — شعر المهجر والنهضة',       'جبران وأبو ماضي وأحمد شوقي وتحليل القصائد.',                                                           false, 60],
            [6, 'الجلسة 6: مراجعة نهائية ونماذج الثانوية',            'حل نماذج امتحانات اللغة العربية للثانوية العامة.',                                                      false, 60],
        ]);
        $aiSess = $this->createSessions($cAI->id, [
            [1, 'الجلسة 1: ما هو الذكاء الاصطناعي؟ الروبوتيك في حياتنا', 'مقدمة: تاريخ الروبوتيك وأنواعه وتطبيقاته العملية.',                                                 true,  60],
            [2, 'الجلسة 2: Arduino — الدوائر الإلكترونية الأساسية',   'بنية Arduino ومكوناتها وأول مشروع إضاءة LED.',                                                          false, 60],
            [3, 'الجلسة 3: البرمجة بـ Scratch — مشاريع تفاعلية',     'إنشاء مشاريع تفاعلية وألعاب بـ Scratch.',                                                               false, 60],
            [4, 'الجلسة 4: مقدمة في Python للناشئين',                 'متغيرات وأنواع وطباعة وآلة حاسبة.',                                                                    false, 60],
            [5, 'الجلسة 5: مشروع روبوت متكامل',                     'تصميم وبرمجة روبوت بالاستشعار التلقائي.',                                                                false, 60],
        ]);

        // 10. Assignments
        $aPhys1 = $this->makeAssignment($physSess[0]->id, $cPhysics->id, 'واجب 1 — قانون أوم وتوصيل المقاومات', 30, now()->subDays(9), [
            ['عند توصيل 3 مقاومات قيمة كل منها 6 Ω على التوازي، المقاومة المكافئة:', 3, ['2 Ω' => true, '18 Ω' => false, '6 Ω' => false]],
            ['العلاقة بين I وV وفق قانون أوم عند ثبوت الحرارة:', 3, ['طردية خطية' => true, 'عكسية' => false, 'لا توجد علاقة' => false]],
            ['مقاومتان 4 Ω و12 Ω على التوازي. المكافئة:', 4, ['3 Ω' => true, '16 Ω' => false, '8 Ω' => false]],
        ]);
        $aPhys2 = $this->makeAssignment($physSess[1]->id, $cPhysics->id, 'واجب 2 — قوانين كيرشوف', 30, now()->subDays(7), [
            ['KCL ينص على أن مجموع التيارات عند نقطة التفرع يساوي:', 5, ['صفر (واردة = صادرة)' => true, 'مجموع الجهد' => false, 'مجموع المقاومات' => false]],
            ['KVL ينص على أن مجموع الجهود في حلقة مغلقة يساوي:', 5, ['صفر' => true, 'مجموع القوى الكهرومحركة' => false, 'مجموع التيارات' => false]],
        ]);
        $aMath1 = $this->makeAssignment($mathSess[0]->id, $cMath->id, 'واجب 1 — النهايات وقواعدها', 30, now()->subDays(6), [
            ['ما قيمة: lim(x→2) [(x²-4)/(x-2)]', 5, ['4' => true, '0' => false, '∞' => false]],
            ['lim(x→0⁺) 1/x يساوي:', 5, ['+∞' => true, '0' => false, '-∞' => false]],
        ]);
        $aProg1 = $this->makeAssignment($progSess[0]->id, $cProg->id, 'واجب 1 — المتغيرات والأنواع في Python', 20, now()->subDays(2), [
            ['نوع المتغير: x = "مرحبا"', 5, ['str' => true, 'int' => false, 'bool' => false]],
            ['ناتج: 7 // 2 في Python', 5, ['3' => true, '3.5' => false, '1' => false]],
        ]);
        $aBio1 = $this->makeAssignment($bioSess[0]->id, $cBio->id, 'واجب 1 — تركيب الخلية', 25, now()->subDays(4), [
            ['العضية المسؤولة عن إنتاج الطاقة ATP:', 5, ['الميتوكوندريا' => true, 'النواة' => false, 'الريبوسومات' => false]],
            ['ما الجزء الموجود في الخلية النباتية فقط؟', 5, ['الجدار الخلوي والبلاستيدات الخضراء' => true, 'الميتوكوندريا' => false, 'الشبكة الإندوبلازمية' => false]],
        ]);

        // 11. Enrollments
        $enrAP  = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id,   'course_id' => $cPhysics->id], ['status' => 'active', 'enrolled_at' => now()->subDays(12)]);
        $enrAM  = CourseEnrollment::updateOrCreate(['student_user_id' => $sAhmed->id,   'course_id' => $cMath->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(8)]);
        $enrMC  = CourseEnrollment::updateOrCreate(['student_user_id' => $sMariam->id,  'course_id' => $cChem->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(7)]);
        $enrMB  = CourseEnrollment::updateOrCreate(['student_user_id' => $sMariam->id,  'course_id' => $cBio->id],     ['status' => 'active', 'enrolled_at' => now()->subDays(5)]);
        $enrOP  = CourseEnrollment::updateOrCreate(['student_user_id' => $sOmar->id,    'course_id' => $cProg->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(3)]);
        $enrNP  = CourseEnrollment::updateOrCreate(['student_user_id' => $sNada->id,    'course_id' => $cPhysics->id], ['status' => 'active', 'enrolled_at' => now()->subDays(14)]);
        $enrNC  = CourseEnrollment::updateOrCreate(['student_user_id' => $sNada->id,    'course_id' => $cChem->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(10)]);
        $enrNA  = CourseEnrollment::updateOrCreate(['student_user_id' => $sNada->id,    'course_id' => $cArabic->id],  ['status' => 'active', 'enrolled_at' => now()->subDays(6)]);
        $enrYM  = CourseEnrollment::updateOrCreate(['student_user_id' => $sYoussef->id, 'course_id' => $cMath->id],    ['status' => 'active', 'enrolled_at' => now()->subDays(2)]);
        $enrLA  = CourseEnrollment::updateOrCreate(['student_user_id' => $sLaila->id,   'course_id' => $cAI->id],      ['status' => 'active', 'enrolled_at' => now()->subDays(1)]);
        $enrLE  = CourseEnrollment::updateOrCreate(['student_user_id' => $sLaila->id,   'course_id' => $cEnglish->id], ['status' => 'active', 'enrolled_at' => now()->subDays(1)]);

        // 12. Session Progress
        foreach (array_slice($physSess, 0, 4) as $s) {
            CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAP->id, 'course_session_id' => $s->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(10), 'completed_at' => now()->subDays(9)]);
        }
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAP->id, 'course_session_id' => $physSess[4]->id], ['status' => SessionProgressStatus::UNLOCKED, 'unlocked_at' => now()->subDays(2)]);
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAM->id, 'course_session_id' => $mathSess[0]->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(7), 'completed_at' => now()->subDays(6)]);
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrAM->id, 'course_session_id' => $mathSess[1]->id], ['status' => SessionProgressStatus::UNLOCKED,  'unlocked_at' => now()->subDays(4)]);
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrMC->id, 'course_session_id' => $chemSess[0]->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(6), 'completed_at' => now()->subDays(5)]);
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrMC->id, 'course_session_id' => $chemSess[1]->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(4), 'completed_at' => now()->subDays(3)]);
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrMC->id, 'course_session_id' => $chemSess[2]->id], ['status' => SessionProgressStatus::UNLOCKED,  'unlocked_at' => now()->subDays(1)]);
        foreach ($physSess as $s) {
            CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrNP->id, 'course_session_id' => $s->id], ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(12), 'completed_at' => now()->subDays(11)]);
        }
        CourseSessionProgress::updateOrCreate(['course_enrollment_id' => $enrOP->id, 'course_session_id' => $progSess[0]->id], ['status' => SessionProgressStatus::UNLOCKED, 'unlocked_at' => now()->subDays(2)]);

        // 13. Submissions
        AssignmentSubmission::updateOrCreate(['assignment_id' => $aPhys1->id, 'student_user_id' => $sAhmed->id], ['course_enrollment_id' => $enrAP->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 95, 'grade' => 95, 'teacher_notes' => 'أداء ممتاز! حل دقيق مع توضيح كامل.', 'submitted_at' => now()->subDays(9)]);
        AssignmentSubmission::updateOrCreate(['assignment_id' => $aPhys2->id, 'student_user_id' => $sAhmed->id], ['course_enrollment_id' => $enrAP->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 88, 'grade' => 88, 'teacher_notes' => 'جيد جداً، راجع KVL.',              'submitted_at' => now()->subDays(7)]);
        AssignmentSubmission::updateOrCreate(['assignment_id' => $aBio1->id,  'student_user_id' => $sMariam->id],['course_enrollment_id' => $enrMB->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 78, 'grade' => 78, 'teacher_notes' => 'مقبول، راجع الجهاز العصبي.', 'submitted_at' => now()->subDays(4)]);
        AssignmentSubmission::updateOrCreate(['assignment_id' => $aPhys1->id, 'student_user_id' => $sNada->id],  ['course_enrollment_id' => $enrNP->id, 'status' => SubmissionStatus::COMPLETED, 'score' => 100, 'grade' => 100, 'teacher_notes' => 'ممتازة! إجابات مثالية.',      'submitted_at' => now()->subDays(11)]);
        AssignmentSubmission::updateOrCreate(['assignment_id' => $aProg1->id, 'student_user_id' => $sOmar->id],  ['course_enrollment_id' => $enrOP->id, 'status' => SubmissionStatus::SUBMITTED, 'submitted_at' => now()->subHours(3)]);

        // 14. Live Sessions — diverse states
        LiveSession::query()->forceDelete();
        $liveData = [
            ['title' => 'فيزياء — كيرشوف (LIVE الآن)',               'student_user_id' => $sAhmed->id,   'teacher_profile_id' => $tAhmed->id,  'subject_id' => $subPhysics->id, 'course_id' => $cPhysics->id, 'start_at' => now()->subMinutes(20), 'end_at' => now()->addMinutes(40),           'scheduled_at' => now()->subMinutes(20),             'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/phys-live',      'meeting_platform' => 'google_meet'],
            ['title' => 'فيزياء حديثة — النسبية (قريباً)',            'student_user_id' => $sNada->id,    'teacher_profile_id' => $tAhmed->id,  'subject_id' => $subPhysics->id, 'course_id' => $cPhysics->id, 'start_at' => now()->addMinutes(20), 'end_at' => now()->addMinutes(80),           'scheduled_at' => now()->addMinutes(20),             'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/phys-soon',      'meeting_platform' => 'google_meet'],
            ['title' => 'كيمياء — حصة تجريبية مجانية (هيدروكربونات)', 'student_user_id' => $sMariam->id,  'teacher_profile_id' => $tSarah->id,  'subject_id' => $subChem->id,    'course_id' => $cChem->id,    'start_at' => now()->addHours(2),    'end_at' => now()->addHours(3),              'scheduled_at' => now()->addHours(2),                'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => true,  'meeting_link' => 'https://meet.google.com/chem-demo',      'meeting_platform' => 'google_meet'],
            ['title' => 'كيمياء — الأروماتيات (منتهية منذ ساعتين)',   'student_user_id' => $sMariam->id,  'teacher_profile_id' => $tSarah->id,  'subject_id' => $subChem->id,    'course_id' => $cChem->id,    'start_at' => now()->subHours(3),    'end_at' => now()->subHours(2),              'scheduled_at' => now()->subHours(3),                'duration_minutes' => 60, 'status' => 'completed', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/chem-done',      'meeting_platform' => 'zoom',         'attendance_status' => 'present'],
            ['title' => 'أحياء — الخلية (انتهى وقت الدخول)',          'student_user_id' => $sMariam->id,  'teacher_profile_id' => $tFatma->id,  'subject_id' => $subBio->id,     'course_id' => $cBio->id,     'start_at' => now()->subMinutes(50), 'end_at' => now()->addMinutes(10),           'scheduled_at' => now()->subMinutes(50),             'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/bio-cutoff',     'meeting_platform' => 'google_meet'],
            ['title' => 'رياضيات — التكامل المحدود (بعد 4 ساعات)',    'student_user_id' => $sAhmed->id,   'teacher_profile_id' => $tOmar->id,   'subject_id' => $subMath->id,    'course_id' => $cMath->id,    'start_at' => now()->addHours(4),    'end_at' => now()->addHours(5)->addMinutes(15), 'scheduled_at' => now()->addHours(4),           'duration_minutes' => 75, 'status' => 'scheduled', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/math-upcoming',  'meeting_platform' => 'other'],
            ['title' => 'رياضيات — الجبر والدوال (غداً الساعة 5)',    'student_user_id' => $sYoussef->id, 'teacher_profile_id' => $tOmar->id,   'subject_id' => $subMath->id,    'course_id' => $cMath->id,    'start_at' => now()->addDay()->setTime(17, 0), 'end_at' => now()->addDay()->setTime(18, 15), 'scheduled_at' => now()->addDay()->setTime(17, 0), 'duration_minutes' => 75, 'status' => 'scheduled', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/math-tomorrow',  'meeting_platform' => 'google_meet'],
            ['title' => 'Python — حصة تجريبية مجانية (بعد ساعة)',    'student_user_id' => $sOmar->id,    'teacher_profile_id' => $tKareem->id, 'subject_id' => $subProg->id,    'course_id' => $cProg->id,    'start_at' => now()->addHour(),      'end_at' => now()->addHours(2)->addMinutes(30), 'scheduled_at' => now()->addHour(),             'duration_minutes' => 90, 'status' => 'scheduled', 'is_free_demo' => true,  'meeting_link' => 'https://meet.google.com/python-demo',    'meeting_platform' => 'google_meet'],
            ['title' => 'عربية — الإعراب والبلاغة (بعد 6 ساعات)',    'student_user_id' => $sNada->id,    'teacher_profile_id' => $tTarek->id,  'subject_id' => $subAr->id,      'course_id' => $cArabic->id,  'start_at' => now()->addHours(6),    'end_at' => now()->addHours(7),              'scheduled_at' => now()->addHours(6),                'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/arabic-upcoming', 'meeting_platform' => 'zoom'],
            ['title' => 'English Reading — (أمس، مكتملة)',            'student_user_id' => $sLaila->id,   'teacher_profile_id' => $tHoda->id,   'subject_id' => $subEng->id,     'course_id' => $cEnglish->id, 'start_at' => now()->subDay()->setTime(15, 0), 'end_at' => now()->subDay()->setTime(16, 0), 'scheduled_at' => now()->subDay()->setTime(15, 0), 'duration_minutes' => 60, 'status' => 'completed', 'is_free_demo' => false, 'meeting_link' => 'https://meet.google.com/eng-done',       'meeting_platform' => 'google_meet', 'attendance_status' => 'present'],
            ['title' => 'روبوتيك — حصة مجانية (بعد 3 ساعات)',         'student_user_id' => $sLaila->id,   'teacher_profile_id' => $tNouran->id, 'subject_id' => $subProg->id,    'course_id' => $cAI->id,      'start_at' => now()->addHours(3),    'end_at' => now()->addHours(4),              'scheduled_at' => now()->addHours(3),                'duration_minutes' => 60, 'status' => 'scheduled', 'is_free_demo' => true,  'meeting_link' => 'https://meet.google.com/ai-demo',        'meeting_platform' => 'google_meet'],
            ['title' => 'فيزياء — موجات (ملغاة)',                     'student_user_id' => $sNada->id,    'teacher_profile_id' => $tAhmed->id,  'subject_id' => $subPhysics->id, 'course_id' => $cPhysics->id, 'start_at' => now()->subDays(2)->setTime(10, 0), 'end_at' => now()->subDays(2)->setTime(11, 0), 'scheduled_at' => now()->subDays(2)->setTime(10, 0), 'duration_minutes' => 60, 'status' => 'cancelled_by_teacher', 'is_free_demo' => false, 'meeting_link' => '', 'meeting_platform' => 'google_meet'],
        ];
        foreach ($liveData as $d) {
            LiveSession::withoutEvents(fn() => LiveSession::create($d));
        }

        // 15. Exception Requests
        ExceptionRequest::updateOrCreate(['student_user_id' => $sAhmed->id,   'reason' => 'ظرف صحي طارئ ومستند طبي مرفق'],          ['course_id' => $cPhysics->id, 'scope' => 'course', 'is_global' => false, 'status' => 'approved', 'reviewed_at' => now()->subDays(5)]);
        ExceptionRequest::updateOrCreate(['student_user_id' => $sMariam->id,  'reason' => 'سفر عائلي مفاجئ بسبب حالة طارئة'],       ['course_id' => $cChem->id,    'scope' => 'course', 'is_global' => false, 'status' => 'pending',  'reviewed_at' => null]);
        ExceptionRequest::updateOrCreate(['student_user_id' => $sNada->id,    'reason' => 'امتحانات SAT تزامنت مع الحصص'],           ['course_id' => $cPhysics->id, 'scope' => 'course', 'is_global' => false, 'status' => 'approved', 'reviewed_at' => now()->subDays(3)]);
        ExceptionRequest::updateOrCreate(['student_user_id' => $sOmar->id,    'reason' => 'عطل مفاجئ في الإنترنت'],                  ['scope' => 'global',          'is_global' => true,  'status' => 'rejected', 'reviewed_at' => now()->subDay()]);

        // 16. Notifications
        $notifs = [
            [$sAhmed->id,   'session_reminder',     '⏰ حصة الفيزياء الكهربية بعد 30 دقيقة',          'حصة قوانين كيرشوف ستبدأ بعد 30 دقيقة مع د. أحمد.',                         false, now()->subMinutes(50)],
            [$sAhmed->id,   'assignment_graded',    '✅ تم تصحيح واجب الفيزياء 1 — درجتك: 95%',       'مبروك! 95/100 في واجب قانون أوم. أداء ممتاز!',                              true,  now()->subDays(8)],
            [$sAhmed->id,   'assignment_graded',    '📝 تم تصحيح واجب الجلسة 2 — درجتك: 88%',        '88/100. راجع تطبيقات قانون كيرشوف الثاني.',                                 true,  now()->subDays(6)],
            [$sAhmed->id,   'enrollment_confirmed', '🎓 تسجيلك في كورس التفاضل والتكامل مؤكد',        'تهانينا! تم تأكيد تسجيلك مع د. عمر خالد.',                                  true,  now()->subDays(8)],
            [$sAhmed->id,   'session_reminder',     '📅 حصة الرياضيات بعد 4 ساعات',                  'التكامل المحدود مع د. عمر. جهز أدواتك!',                                    false, now()->subHours(2)],
            [$sMariam->id,  'session_reminder',     '⏰ حصة الكيمياء التجريبية المجانية بعد ساعتين',  'استعدي للحصة المجانية مع أ. سارة في غضون ساعتين.',                          false, now()->subHour()],
            [$sMariam->id,  'exception_status',     '📋 طلب الاستثناء قيد المراجعة',                  'تم استلام طلب الاستثناء بسبب السفر وسيتم الرد خلال 24 ساعة.',               false, now()->subDays(1)],
            [$sMariam->id,  'assignment_graded',    '📖 تم تصحيح واجب الأحياء — 78%',                 'مقبول. راجع الجهاز العصبي.',                                                true,  now()->subDays(3)],
            [$sMariam->id,  'session_completed',    '✅ حضرتِ حصة كيمياء الأروماتيات',                'تم تسجيل حضورك. شكراً على الالتزام.',                                       true,  now()->subHours(2)],
            [$sNada->id,    'assignment_graded',    '🏆 واجب الفيزياء — 100%! إنجاز مثالي',           'ممتازة! علامة مثالية 100/100. إجابات مثالية وواضحة!',                       true,  now()->subDays(10)],
            [$sNada->id,    'exception_status',     '✅ تم قبول طلب الاستثناء (SAT)',                 'يمكنك حضور حصة تعويضية. تم قبول الطلب.',                                    true,  now()->subDays(2)],
            [$sNada->id,    'session_reminder',     '📅 حصة الفيزياء الحديثة بعد 20 دقيقة',          'سيبدأ رابط البث خلال 20 دقيقة. جهزي أسئلتك!',                               false, now()->subMinutes(30)],
            [$sNada->id,    'package_info',         '📦 باقتك نشطة — 16 حصة متبقية',                 '16 حصة متبقية، تنتهي بعد 15 يوماً. جددي مبكراً.',                           true,  now()->subDays(5)],
            [$sOmar->id,    'session_reminder',     '⏰ حصة Python المجانية بعد ساعة',                'جاهز لأول رحلتك في البرمجة؟ الحصة تبدأ بعد ساعة!',                          false, now()->subMinutes(30)],
            [$sOmar->id,    'exception_status',     '❌ تم رفض طلب الاستثناء',                        'تم رفض الطلب لعدم كفاية التوثيق. يمكنك إعادة التقديم.',                     true,  now()->subDay()],
            [$sOmar->id,    'enrollment_confirmed', '🎓 تسجيلك في كورس البرمجة مؤكد',                'تهانياً! تم التسجيل في كورس Python مع م. كريم زكي.',                         true,  now()->subDays(3)],
            [$sLaila->id,   'session_completed',    '✅ حضرتِ حصة الإنجليزية',                        'تم تسجيل حضورك في Reading Comprehension مع أ. هدى.',                        true,  now()->subDay()],
            [$sLaila->id,   'session_reminder',     '🤖 حصة الروبوتيك المجانية بعد 3 ساعات',         'استعدي لعالم الروبوتيك! الحصة مع أ. نوران.',                                false, now()->subHours(1)],
            [$sYoussef->id, 'enrollment_confirmed', '🎓 تسجيلك في كورس الرياضيات مؤكد',              'أهلاً يوسف! تم التسجيل في كورس التفاضل مع د. عمر.',                         true,  now()->subDays(2)],
            [$sYoussef->id, 'session_reminder',     '📅 حصة الرياضيات غداً الساعة 5 مساءاً',         'حصة الجبر والدوال الأسية ستبدأ غداً. جهز دفتر الرياضيات!',                  false, now()->subHours(3)],
        ];
        foreach ($notifs as [$uid, $type, $title, $body, $read, $at]) {
            UserNotification::firstOrCreate(['user_id' => $uid, 'title' => $title], ['type' => $type, 'body' => $body, 'is_read' => $read, 'created_at' => $at, 'updated_at' => $at]);
        }

        $this->command->info('🎉 All Egyptian Educational Data Seeded Successfully!');
    }

    private function upsertTeacher(string $slug, string $name, string $email, string $phone, string $title, string $spec, string $bio, int $exp, float $rating, int $students, bool $featured): TeacherProfile
    {
        $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'phone' => $phone, 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        return TeacherProfile::updateOrCreate(['slug' => $slug], ['user_id' => $user->id, 'title' => $title, 'specialization' => $spec, 'bio' => $bio, 'years_experience' => $exp, 'rating_avg' => $rating, 'students_count' => $students, 'photo' => 'images/instructor_portrait.png', 'is_featured' => $featured, 'is_public' => true, 'show_contact_info' => true]);
    }

    private function upsertStudent(string $email, string $name, string $phone, int $gradeId, string $school, bool $usedFree, array $subjectIds = []): array
    {
        $user    = User::updateOrCreate(['email' => $email], ['name' => $name, 'phone' => $phone, 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => now()]);
        $profile = StudentProfile::updateOrCreate(['user_id' => $user->id], ['grade_level_id' => $gradeId, 'school_name' => $school, 'has_used_free_session' => $usedFree]);
        if (!empty($subjectIds)) {
            $profile->subjects()->sync($subjectIds);
        }
        return [$user, $profile];
    }

    private function upsertStudentPackage(int $userId, int $tplId, int $total, int $used, int $remaining, string $status, int $daysAgo, int $expDays): StudentPackage
    {
        return StudentPackage::updateOrCreate(['student_user_id' => $userId, 'package_template_id' => $tplId], ['total_sessions' => $total, 'used_sessions' => $used, 'remaining_sessions' => $remaining, 'status' => $status, 'activated_at' => now()->subDays($daysAgo), 'expires_at' => now()->addDays($expDays)]);
    }

    private function createSessions(int $courseId, array $sessions): array
    {
        $result = [];
        foreach ($sessions as [$order, $title, $desc, $freeDemo, $duration]) {
            $result[] = CourseSession::updateOrCreate(
                ['course_id' => $courseId, 'sort_order' => $order],
                ['title' => $title, 'description' => $desc, 'duration_minutes' => $duration, 'is_free_demo' => $freeDemo, 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
            );
        }
        return $result;
    }

    private function makeAssignment(int $sessionId, int $courseId, string $title, int $durationMins, $startAt, array $questions): Assignment
    {
        $a = Assignment::updateOrCreate(
            ['course_session_id' => $sessionId],
            ['course_id' => $courseId, 'title' => $title, 'description' => 'أجب على الأسئلة التالية بدقة.', 'passing_grade' => 70, 'passing_score' => 70, 'duration_minutes' => $durationMins, 'status' => 'published', 'start_at' => $startAt]
        );
        $order = 1;
        foreach ($questions as [$text, $pts, $options]) {
            $q = AssignmentQuestion::updateOrCreate(
                ['assignment_id' => $a->id, 'sort_order' => $order++],
                ['question_text' => $text, 'question_type' => 'text', 'points' => $pts, 'is_multiple_choice' => false]
            );
            $optOrder = 1;
            foreach ($options as $optText => $isCorrect) {
                AssignmentQuestionOption::updateOrCreate(['question_id' => $q->id, 'option_text' => $optText], ['sort_order' => $optOrder++, 'is_correct' => $isCorrect]);
            }
        }
        return $a;
    }
}

<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\FcmToken;
use App\Models\GradeLevel;
use App\Models\LiveSession;
use App\Models\PackageTemplate;
use App\Models\PackageTransaction;
use App\Models\ParentProfile;
use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionsAndScheduleFullCycleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── 1. CLEANUP PREVIOUS TEST COLLECTIONS DATA ────────────────────────
        DB::table('package_transactions')->delete();
        StudentPackage::query()->forceDelete();
        
        // Remove old recurring schedule instances of live sessions
        LiveSession::whereNotNull('recurring_schedule_id')->forceDelete();
        RecurringSchedule::query()->forceDelete();

        // ── 2. ENSURE GRADE LEVELS ───────────────────────────────────────────
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

        // ── 3. ENSURE SUBJECTS & CATEGORIES ──────────────────────────────────
        $catSci = Category::firstOrCreate(['slug' => 'natural-sciences'], ['name' => 'العلوم الطبيعية', 'color_theme' => '#0D9488']);
        $catMath = Category::firstOrCreate(['slug' => 'math-and-tech'], ['name' => 'الرياضيات والتكنولوجيا', 'color_theme' => '#2563EB']);
        $catLang = Category::firstOrCreate(['slug' => 'languages-and-literature'], ['name' => 'اللغات والآداب', 'color_theme' => '#EA580C']);

        $subMath = Subject::updateOrCreate(['slug' => 'mathematics'], ['name' => 'الرياضيات البحتة والتطبيقية', 'category_id' => $catMath->id, 'sort_order' => 1]);
        $subPhysics = Subject::updateOrCreate(['slug' => 'physics'], ['name' => 'الفيزياء العامة والحديثة', 'category_id' => $catSci->id, 'sort_order' => 2]);
        $subChem = Subject::updateOrCreate(['slug' => 'chemistry'], ['name' => 'الكيمياء العضوية والتحليلية', 'category_id' => $catSci->id, 'sort_order' => 3]);
        $subEnglish = Subject::updateOrCreate(['slug' => 'english'], ['name' => 'اللغة الإنجليزية وآدابها', 'category_id' => $catLang->id, 'sort_order' => 4]);
        $subArabic = Subject::updateOrCreate(['slug' => 'arabic'], ['name' => 'اللغة العربية والبلاغة', 'category_id' => $catLang->id, 'sort_order' => 5]);

        // ── 4. RETRIEVE OR CREATE TEACHER PROFILES ──────────────────────────
        $tAhmed = TeacherProfile::where('slug', 'dr-ahmed-mahmoud')->first();
        if (! $tAhmed) {
            $tAhmedUser = User::updateOrCreate(
                ['email' => 't.ahmed@elite.edu'],
                ['name' => 'د. أحمد محمود', 'phone' => '+201001112221', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => $now]
            );
            $tAhmed = TeacherProfile::updateOrCreate(['user_id' => $tAhmedUser->id], ['slug' => 'dr-ahmed-mahmoud', 'bio' => 'معلم أول الرياضيات البحتة والتفاضل والتكامل']);
        }

        $tSarah = TeacherProfile::where('slug', 'sarah-mohamed')->first();
        if (! $tSarah) {
            $tSarahUser = User::updateOrCreate(
                ['email' => 't.sarah@elite.edu'],
                ['name' => 'أ. سارة محمد', 'phone' => '+201001112222', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => $now]
            );
            $tSarah = TeacherProfile::updateOrCreate(['user_id' => $tSarahUser->id], ['slug' => 'sarah-mohamed', 'bio' => 'خبيرة الآيلتس واللغة الإنجليزية']);
        }

        $tOmar = TeacherProfile::where('slug', 'dr-omar-khaled')->first();
        if (! $tOmar) {
            $tOmarUser = User::updateOrCreate(
                ['email' => 't.omar@elite.edu'],
                ['name' => 'د. عمر خالد', 'phone' => '+201001112223', 'password' => bcrypt('password'), 'status' => AccountStatus::APPROVED, 'email_verified_at' => $now]
            );
            $tOmar = TeacherProfile::updateOrCreate(['user_id' => $tOmarUser->id], ['slug' => 'dr-omar-khaled', 'bio' => 'مدرس الفيزياء والكيمياء']);
        }

        // ── 5. ENSURE PACKAGE TEMPLATES ──────────────────────────────────────
        $tpl12 = PackageTemplate::updateOrCreate(
            ['name' => 'باقة التميز الفصلي (12 حصة)'],
            ['sessions_count' => 12, 'price' => 600.00, 'description' => 'باقة متكاملة تغطي 12 حصة تفاعلية مباشرة مع المتابعة.', 'is_active' => true]
        );
        $tpl8 = PackageTemplate::updateOrCreate(
            ['name' => 'باقة المتابعة الشهرية (8 حصص)'],
            ['sessions_count' => 8, 'price' => 450.00, 'description' => 'باقة منتظمة تغطي 8 حصص شهرياً بمعدل حصتين أسبوعياً.', 'is_active' => true]
        );
        $tpl16 = PackageTemplate::updateOrCreate(
            ['name' => 'باقة الثانوية العامة المكثفة (16 حصة)'],
            ['sessions_count' => 16, 'price' => 900.00, 'description' => 'باقة مكثفة لمراجعة كافة فروع المادة ونماذج الامتحانات.', 'is_active' => true]
        );

        // ── 6. ENSURE COURSES ────────────────────────────────────────────────
        $cMath = Course::updateOrCreate(
            ['slug' => 'math-secondary-comprehensive'],
            ['title' => 'كورس الرياضيات البحتة والتفاضل والتكامل', 'subject_id' => $subMath->id, 'teacher_id' => $tAhmed->id, 'grade_level_id' => $g12->id, 'is_active' => true]
        );
        $cPhysics = Course::updateOrCreate(
            ['slug' => 'physics-secondary-mastery'],
            ['title' => 'كورس الفيزياء الكهربية والمغناطيسية الحديثة', 'subject_id' => $subPhysics->id, 'teacher_id' => $tOmar->id, 'grade_level_id' => $g12->id, 'is_active' => true]
        );
        $cEnglish = Course::updateOrCreate(
            ['slug' => 'english-advanced-secondary'],
            ['title' => 'كورس اللغة الإنجليزية التفاعلي وآدابها', 'subject_id' => $subEnglish->id, 'teacher_id' => $tSarah->id, 'grade_level_id' => $g11->id, 'is_active' => true]
        );
        $cChem = Course::updateOrCreate(
            ['slug' => 'chemistry-organic-masterclass'],
            ['title' => 'كورس الكيمياء العضوية والتحليل الكمي', 'subject_id' => $subChem->id, 'teacher_id' => $tOmar->id, 'grade_level_id' => $g10->id, 'is_active' => true]
        );

        // ── 7. PARENTS & STUDENTS CREATION ────────────────────────────────────
        $parentsData = [
            'khaled' => [
                'name' => 'أ. خالد محمود السعدني',
                'email' => 'khaled.parent@elite.edu',
                'phone' => '01000000004',
            ],
            'abdelrahman' => [
                'name' => 'م. عبد الرحمن سامي البدري',
                'email' => 'abdelrahman.parent@elite.edu',
                'phone' => '01112345678',
            ],
            'radwan' => [
                'name' => 'أ. أحمد رضوان القاضي',
                'email' => 'radwan.parent@elite.edu',
                'phone' => '01098765432',
            ],
            'elshafey' => [
                'name' => 'م. مصطفى الشافعي الأنصاري',
                'email' => 'elshafey.parent@elite.edu',
                'phone' => '01555554444',
            ],
            'fouad' => [
                'name' => 'أ. إبراهيم فؤاد عبد العال',
                'email' => 'fouad.parent@elite.edu',
                'phone' => '01011223344',
            ],
            'banna' => [
                'name' => 'د. حسن البنا المنياوي',
                'email' => 'banna.parent@elite.edu',
                'phone' => '01099887766',
            ],
        ];

        $parentUsers = [];
        foreach ($parentsData as $key => $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'name' => $p['name'],
                    'phone' => $p['phone'],
                    'password' => bcrypt('password'),
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => $now,
                ]
            );
            ParentProfile::updateOrCreate(['user_id' => $user->id]);

            // Register dummy FCM Token for real notification flow
            FcmToken::updateOrCreate(
                ['token' => "fcm_token_parent_{$key}"],
                ['user_id' => $user->id, 'device_type' => 'web', 'last_used_at' => $now]
            );

            $parentUsers[$key] = $user;
        }

        // ── 8. STUDENTS SEEDING WITH SPECIFIC LIFECYCLE TARGETS ──────────────
        $studentsData = [
            // Student 1: EXHAUSTED (0 sessions)
            [
                'key' => 'ahmed',
                'name' => 'أحمد خالد السعدني',
                'email' => 'ahmed.student@elite.edu',
                'phone' => '01012340001',
                'grade' => $g12->id,
                'parent' => 'khaled',
                'total' => 12,
                'used' => 12,
                'remaining' => 0,
                'status' => 'exhausted',
                'expires_at' => $now->copy()->addDays(20),
                'distribution' => ['الرياضيات' => 6, 'الفيزياء' => 6],
                'course' => $cMath->id,
                'template' => $tpl12->id,
            ],
            // Student 2: EXHAUSTED (0 sessions, status active)
            [
                'key' => 'kareem',
                'name' => 'كريم عبد الرحمن البدري',
                'email' => 'kareem.student@elite.edu',
                'phone' => '01012340002',
                'grade' => $g12->id,
                'parent' => 'abdelrahman',
                'total' => 8,
                'used' => 8,
                'remaining' => 0,
                'status' => 'active',
                'expires_at' => $now->copy()->addDays(15),
                'distribution' => ['الكيمياء' => 4, 'الفيزياء' => 4],
                'course' => $cChem->id,
                'template' => $tpl8->id,
            ],
            // Student 3: LOW BALANCE (1 session left)
            [
                'key' => 'mariam',
                'name' => 'مريم خالد السعدني',
                'email' => 'mariam.student@elite.edu',
                'phone' => '01012340003',
                'grade' => $g11->id,
                'parent' => 'khaled',
                'total' => 12,
                'used' => 11,
                'remaining' => 1,
                'status' => 'active',
                'expires_at' => $now->copy()->addDays(25),
                'distribution' => ['الفيزياء' => 6, 'اللغة الإنجليزية' => 6],
                'course' => $cPhysics->id,
                'template' => $tpl12->id,
            ],
            // Student 4: LOW BALANCE (2 sessions left)
            [
                'key' => 'sarah',
                'name' => 'سارة أحمد رضوان',
                'email' => 'sarah.student@elite.edu',
                'phone' => '01012340004',
                'grade' => $g11->id,
                'parent' => 'radwan',
                'total' => 16,
                'used' => 14,
                'remaining' => 2,
                'status' => 'active',
                'expires_at' => $now->copy()->addDays(18),
                'distribution' => ['اللغة الإنجليزية' => 8, 'اللغة العربية' => 8],
                'course' => $cEnglish->id,
                'template' => $tpl16->id,
            ],
            // Student 5: LOW BALANCE (3 sessions left)
            [
                'key' => 'omar',
                'name' => 'عمر مصطفى الشافعي',
                'email' => 'omar.student@elite.edu',
                'phone' => '01012340005',
                'grade' => $g10->id,
                'parent' => 'elshafey',
                'total' => 10,
                'used' => 7,
                'remaining' => 3,
                'status' => 'active',
                'expires_at' => $now->copy()->addDays(12),
                'distribution' => ['الكيمياء' => 5, 'الرياضيات' => 5],
                'course' => $cChem->id,
                'template' => $tpl12->id,
            ],
            // Student 6: EXPIRING IN 3 DAYS
            [
                'key' => 'youssef',
                'name' => 'يوسف إبراهيم فؤاد',
                'email' => 'youssef.student@elite.edu',
                'phone' => '01012340006',
                'grade' => $g12->id,
                'parent' => 'fouad',
                'total' => 12,
                'used' => 6,
                'remaining' => 6,
                'status' => 'active',
                'expires_at' => $now->copy()->addDays(3),
                'distribution' => ['الرياضيات' => 6, 'الفيزياء' => 6],
                'course' => $cMath->id,
                'template' => $tpl12->id,
            ],
            // Student 7: EXPIRING IN 6 DAYS
            [
                'key' => 'nour',
                'name' => 'نور الهدى حسن المنياوي',
                'email' => 'nour.student@elite.edu',
                'phone' => '01012340007',
                'grade' => $g11->id,
                'parent' => 'banna',
                'total' => 8,
                'used' => 4,
                'remaining' => 4,
                'status' => 'active',
                'expires_at' => $now->copy()->addDays(6),
                'distribution' => ['اللغة الإنجليزية' => 8],
                'course' => $cEnglish->id,
                'template' => $tpl8->id,
            ],
            // Student 8: HEALTHY ONGOING PACKAGE
            [
                'key' => 'mahmoud',
                'name' => 'محمود علي فهمي',
                'email' => 'mahmoud.student@elite.edu',
                'phone' => '01012340008',
                'grade' => $g10->id,
                'parent' => 'khaled',
                'total' => 20,
                'used' => 5,
                'remaining' => 15,
                'status' => 'active',
                'expires_at' => $now->copy()->addDays(45),
                'distribution' => ['الرياضيات' => 10, 'الفيزياء' => 10],
                'course' => $cMath->id,
                'template' => $tpl16->id,
            ],
        ];

        $studentUsers = [];
        foreach ($studentsData as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'phone' => $item['phone'],
                    'password' => bcrypt('password'),
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => $now,
                ]
            );
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                ['grade_level_id' => $item['grade'], 'has_used_free_session' => true]
            );

            // Register FCM Token for student
            FcmToken::updateOrCreate(
                ['token' => "fcm_token_student_{$item['key']}"],
                ['user_id' => $user->id, 'device_type' => 'web', 'last_used_at' => $now]
            );

            // Link to parent in parent_student
            $parentUser = $parentUsers[$item['parent']];
            DB::table('parent_student')->updateOrInsert(
                [
                    'parent_user_id' => $parentUser->id,
                    'student_user_id' => $user->id,
                ],
                [
                    'relationship' => 'Son/Daughter',
                    'is_primary' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            // Seed StudentPackage
            $pkg = StudentPackage::create([
                'student_user_id' => $user->id,
                'course_id' => $item['course'],
                'package_template_id' => $item['template'],
                'total_sessions' => $item['total'],
                'used_sessions' => $item['used'],
                'remaining_sessions' => $item['remaining'],
                'status' => $item['status'],
                'subject_distribution' => $item['distribution'],
                'is_distributed' => true,
                'activated_at' => $now->copy()->subDays(15),
                'expires_at' => $item['expires_at'],
            ]);

            // Seed initial activation transaction
            PackageTransaction::create([
                'student_package_id' => $pkg->id,
                'type' => 'payment_activation',
                'sessions_delta' => $item['total'],
                'balance_before' => 0,
                'balance_after' => $item['total'],
                'reason' => 'Package activated on enrollment',
                'created_at' => $now->copy()->subDays(15),
            ]);

            // Seed attendance deduction transactions if any used sessions
            if ($item['used'] > 0) {
                PackageTransaction::create([
                    'student_package_id' => $pkg->id,
                    'type' => 'session_deduct',
                    'sessions_delta' => -$item['used'],
                    'balance_before' => $item['total'],
                    'balance_after' => $item['remaining'],
                    'reason' => "Attended {$item['used']} live sessions during academic term",
                    'created_at' => $now->copy()->subDays(5),
                ]);
            }

            $studentUsers[$item['key']] = $user;
        }

        // ── 9. SEED RECURRING SCHEDULES (CYCLES ENDING & EXPIRED) ────────────
        // Schedule 1: Ending in 3 days (KPI 4 + Ending soon filter)
        $sch1 = RecurringSchedule::create([
            'teacher_profile_id' => $tAhmed->id,
            'course_id' => $cMath->id,
            'student_user_id' => $studentUsers['ahmed']->id,
            'title' => 'جدول الرياضيات للثانوية العامة — دورة أكتوبر',
            'recurrence_type' => 'weekly',
            'days_of_week' => [0, 2], // Sunday & Tuesday
            'start_time' => '16:00',
            'duration_minutes' => 60,
            'start_date' => $now->copy()->subMonth()->format('Y-m-d'),
            'end_date' => $now->copy()->addDays(3)->format('Y-m-d'),
            'status' => 'active',
            'meeting_link' => 'https://meet.google.com/xyz-oct-math',
            'meeting_platform' => 'google_meet',
            'notes' => 'الدورة الشهرية تنتهي قريباً وتتطلب تجديد الاشتراك للشهر الجديد',
        ]);

        // Schedule 2: Ending in 6 days (KPI 4 + Ending soon filter)
        $sch2 = RecurringSchedule::create([
            'teacher_profile_id' => $tOmar->id,
            'course_id' => $cPhysics->id,
            'student_user_id' => $studentUsers['mariam']->id,
            'title' => 'جدول الفيزياء المكثف — دورة الخريف',
            'recurrence_type' => 'weekly',
            'days_of_week' => [1, 3], // Monday & Wednesday
            'start_time' => '18:00',
            'duration_minutes' => 90,
            'start_date' => $now->copy()->subMonth()->format('Y-m-d'),
            'end_date' => $now->copy()->addDays(6)->format('Y-m-d'),
            'status' => 'active',
            'meeting_link' => 'https://meet.google.com/phy-fall-live',
            'meeting_platform' => 'google_meet',
        ]);

        // Schedule 3: Ending in 11 days (KPI 4 + Ending soon filter)
        $sch3 = RecurringSchedule::create([
            'teacher_profile_id' => $tSarah->id,
            'course_id' => $cEnglish->id,
            'student_user_id' => $studentUsers['sarah']->id,
            'title' => 'جدول اللغة الإنجليزية والتحضير المتقدم',
            'recurrence_type' => 'weekly',
            'days_of_week' => [2, 5], // Tuesday & Friday
            'start_time' => '19:30',
            'duration_minutes' => 60,
            'start_date' => $now->copy()->subMonth()->format('Y-m-d'),
            'end_date' => $now->copy()->addDays(11)->format('Y-m-d'),
            'status' => 'active',
            'meeting_link' => 'https://meet.google.com/eng-ielts-prep',
            'meeting_platform' => 'google_meet',
        ]);

        // Schedule 4: ENDED 2 DAYS AGO (Sub-filter: الدورة انتهت بالفعل)
        $sch4 = RecurringSchedule::create([
            'teacher_profile_id' => $tOmar->id,
            'course_id' => $cChem->id,
            'student_user_id' => $studentUsers['omar']->id,
            'title' => 'دورة الكيمياء العضوية — منتهية',
            'recurrence_type' => 'weekly',
            'days_of_week' => [4], // Thursday
            'start_time' => '15:00',
            'duration_minutes' => 60,
            'start_date' => $now->copy()->subMonths(2)->format('Y-m-d'),
            'end_date' => $now->copy()->subDays(2)->format('Y-m-d'),
            'status' => 'active',
            'meeting_link' => 'https://meet.google.com/chem-org-closed',
            'meeting_platform' => 'google_meet',
            'notes' => 'انتهت الدورة وتنتظر تجديد السداد لتوليد حصص الشهر الجديد',
        ]);

        // Schedule 5: ONGOING ACTIVE (Ends in 60 days)
        $sch5 = RecurringSchedule::create([
            'teacher_profile_id' => $tAhmed->id,
            'course_id' => $cMath->id,
            'student_user_id' => $studentUsers['youssef']->id,
            'title' => 'جدول الرياضيات التفاعلي للمتفوقين',
            'recurrence_type' => 'weekly',
            'days_of_week' => [1, 4],
            'start_time' => '20:00',
            'duration_minutes' => 60,
            'start_date' => $now->format('Y-m-d'),
            'end_date' => $now->copy()->addDays(60)->format('Y-m-d'),
            'status' => 'active',
            'meeting_link' => 'https://meet.google.com/math-super-60d',
            'meeting_platform' => 'google_meet',
        ]);

        // ── 10. SEED LIVE SESSIONS TO TEST REMINDING NOTIFICATION CYCLES ─────
        // Session 1: Starting in 14 minutes -> Triggers Tier '15m' reminder!
        LiveSession::create([
            'title' => 'حصة التفاضل والتكامل المباشرة — مراجعة قوانين الاشتقاق',
            'recurring_schedule_id' => $sch1->id,
            'course_id' => $cMath->id,
            'subject_id' => $subMath->id,
            'teacher_profile_id' => $tAhmed->id,
            'student_user_id' => $studentUsers['ahmed']->id,
            'scheduled_at' => $now->copy()->addMinutes(14),
            'start_at' => $now->copy()->addMinutes(14),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/xyz-oct-math',
            'meeting_platform' => 'google_meet',
            'reminders_sent' => [],
        ]);

        // Session 2: Starting in 55 minutes -> Triggers Tier '1h' reminder!
        LiveSession::create([
            'title' => 'حصة الفيزياء الكهربية — دوائر التيار المتردد والمكثفات',
            'recurring_schedule_id' => $sch2->id,
            'course_id' => $cPhysics->id,
            'subject_id' => $subPhysics->id,
            'teacher_profile_id' => $tOmar->id,
            'student_user_id' => $studentUsers['mariam']->id,
            'scheduled_at' => $now->copy()->addMinutes(55),
            'start_at' => $now->copy()->addMinutes(55),
            'duration_minutes' => 90,
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/phy-fall-live',
            'meeting_platform' => 'google_meet',
            'reminders_sent' => [],
        ]);

        // Session 3: Starting tomorrow in 23.5 hours -> Triggers Tier '24h' reminder!
        LiveSession::create([
            'title' => 'ورشة الكتابة الأكاديمية والمحادثة المتقدمة',
            'recurring_schedule_id' => $sch3->id,
            'course_id' => $cEnglish->id,
            'subject_id' => $subEnglish->id,
            'teacher_profile_id' => $tSarah->id,
            'student_user_id' => $studentUsers['sarah']->id,
            'scheduled_at' => $now->copy()->addHours(23)->addMinutes(30),
            'start_at' => $now->copy()->addHours(23)->addMinutes(30),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/eng-ielts-prep',
            'meeting_platform' => 'google_meet',
            'reminders_sent' => [],
        ]);

        // Session 4: Started 2 minutes ago -> Triggers Tier 'started' live reminder!
        LiveSession::create([
            'title' => 'جلسة حل التمارين التطبيقية للكيمياء',
            'course_id' => $cChem->id,
            'subject_id' => $subChem->id,
            'teacher_profile_id' => $tOmar->id,
            'student_user_id' => $studentUsers['omar']->id,
            'scheduled_at' => $now->copy()->subMinutes(2),
            'start_at' => $now->copy()->subMinutes(2),
            'duration_minutes' => 60,
            'status' => 'scheduled',
            'meeting_link' => 'https://meet.google.com/chem-live-started',
            'meeting_platform' => 'google_meet',
            'reminders_sent' => [],
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
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
use App\Models\GradeLevel;
use App\Models\HeroSlide;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
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

        $this->command?->info('🌱 Starting Elite Academy Demo Seeder (V2 Config-Driven)...');

        $demoPassword = config('demo.credentials.password', 'Demo@12345');
        $hashedDemoPassword = Hash::make($demoPassword);
        $seedVolume = config('demo.seed', [
            'teachers' => 6,
            'students' => 30,
            'parents' => 20,
            'courses' => 8,
            'sessions_per_course' => 12,
            'assignments_per_course' => 3,
            'packages' => 10,
            'articles' => 6,
            'hero_slides' => 3,
        ]);

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
        $supportUser = User::firstOrCreate(
            ['email' => 'support@trendk.com'],
            [
                'name' => 'Trendk Support',
                'phone' => '+201000000000',
                'password' => Hash::make('password'),
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        AdminProfile::firstOrCreate(['user_id' => $supportUser->id]);
        $supportUser->assignRole($roleSuperAdmin);

        // -------------------------------------------------------------
        // 2. Demo Target Accounts (Admin, Teacher, Student, Parent)
        // -------------------------------------------------------------
        // Admin
        $demoAdmin = User::updateOrCreate(
            ['email' => 'admin@demo.test'],
            [
                'name' => 'Demo Administrator — المدير التجريبي',
                'phone' => '+201000000099',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        AdminProfile::firstOrCreate(['user_id' => $demoAdmin->id]);
        $demoAdmin->syncRoles([$roleSuperAdmin, $roleAdmin]);

        // Teacher
        $demoTeacherUser = User::updateOrCreate(
            ['email' => 'teacher@demo.test'],
            [
                'name' => 'Demo Teacher — د. طارق الشناوي',
                'phone' => '+201000000098',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $demoTeacherUser->syncRoles([$roleTeacher]);
        $demoTeacherProfile = TeacherProfile::updateOrCreate(
            ['user_id' => $demoTeacherUser->id],
            [
                'slug' => 'demo-teacher-tarek',
                'title' => 'كبير معلمي الفيزياء والفيزياء الحديثة للثانوية العامة',
                'specialization' => 'Physics',
                'bio' => 'معلم تجريبي معتمد لأكاديمية إيليت. حاصل على دكتوراه في الفيزياء التطبيقية مع أكثر من 16 عاماً من الخبرة في تبسيط المفاهيم المعقدة وتأهيل أوائل الجمهورية.',
                'years_experience' => 16,
                'rating_avg' => 4.95,
                'students_count' => 1850,
                'photo' => 'images/instructor_portrait.png',
                'is_featured' => true,
                'is_public' => true,
            ]
        );

        // Student
        $demoStudentUser = User::updateOrCreate(
            ['email' => 'student@demo.test'],
            [
                'name' => 'Demo Student — يوسف إبراهيم',
                'phone' => '+201000000097',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $demoStudentUser->syncRoles([$roleStudent]);

        // Parent
        $demoParentUser = User::updateOrCreate(
            ['email' => 'parent@demo.test'],
            [
                'name' => 'Demo Parent — أ. إبراهيم يوسف',
                'phone' => '+201000000096',
                'password' => $hashedDemoPassword,
                'status' => AccountStatus::APPROVED,
                'email_verified_at' => now(),
            ]
        );
        $demoParentUser->syncRoles([$roleParent]);
        $demoParentProfile = ParentProfile::firstOrCreate(['user_id' => $demoParentUser->id]);

        // Link Demo Parent to Demo Student
        $demoParentProfile->students()->syncWithoutDetaching([
            $demoStudentUser->id => ['relationship' => 'Son'],
        ]);

        // -------------------------------------------------------------
        // 3. Egyptian Grade Levels (الصفوف الدراسية المصرية)
        // -------------------------------------------------------------
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
        $g9 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-9'],
            ['name' => 'الصف الثالث الإعدادي (الشهادة الإعدادية)', 'sort_order' => 4]
        );
        $g8 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-8'],
            ['name' => 'الصف الثاني الإعدادي', 'sort_order' => 5]
        );
        $g7 = GradeLevel::updateOrCreate(
            ['slug' => 'grade-7'],
            ['name' => 'الصف الأول الإعدادي', 'sort_order' => 6]
        );

        $allGrades = collect([$g12, $g11, $g10, $g9, $g8, $g7]);

        // Complete Student Profile for demo student
        StudentProfile::updateOrCreate(
            ['user_id' => $demoStudentUser->id],
            [
                'grade_level_id' => $g12->id,
                'school_name' => 'مدرسة المتفوقين للعلوم والتكنولوجيا (STEM Cairo)',
                'has_used_free_session' => true,
            ]
        );

        // -------------------------------------------------------------
        // 4. Categories & Subjects (الأقسام والمواد)
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
            ['name' => 'اللغات والآداب', 'color_theme' => '#EA580C']
        );

        $subPhysics = Subject::updateOrCreate(
            ['slug' => 'physics'],
            ['category_id' => $catScience->id, 'name' => 'الفيزياء العامة والحديثة', 'description' => 'شرح الكهربية وقوانين كيرشوف والفيزياء الذرية للمرحلة الثانوية.', 'sort_order' => 1]
        );
        $subChem = Subject::updateOrCreate(
            ['slug' => 'chemistry'],
            ['category_id' => $catScience->id, 'name' => 'الكيمياء العضوية والتحليلية', 'description' => 'دراسة الهيدروكربونات والاتزان الكيميائي والتحليل الكهربي.', 'sort_order' => 2]
        );
        $subBio = Subject::updateOrCreate(
            ['slug' => 'biology'],
            ['category_id' => $catScience->id, 'name' => 'الأحياء وعلم الوراثة', 'description' => 'دراسة الخلية، DNA، والتكاثر والمناعة في الإنسان.', 'sort_order' => 3]
        );
        $subMath = Subject::updateOrCreate(
            ['slug' => 'mathematics'],
            ['category_id' => $catMathTech->id, 'name' => 'الرياضيات البحتة والتطبيقية', 'description' => 'التفاضل والتكامل والجبر والهندسة الفراغية والاستاتيكا.', 'sort_order' => 4]
        );
        $subProg = Subject::updateOrCreate(
            ['slug' => 'programming-ai'],
            ['category_id' => $catMathTech->id, 'name' => 'البرمجة والذكاء الاصطناعي', 'description' => 'بايثون وتطوير تطبيقات الويب وخوارزميات تعلم الآلة.', 'sort_order' => 5]
        );
        $subEnglish = Subject::updateOrCreate(
            ['slug' => 'english'],
            ['category_id' => $catLanguages->id, 'name' => 'اللغة الإنجليزية المتقدمة', 'description' => 'إتقان القواعد، مهارات الكتابة الأكاديمية والمحادثة.', 'sort_order' => 6]
        );
        $subArabic = Subject::updateOrCreate(
            ['slug' => 'arabic'],
            ['category_id' => $catLanguages->id, 'name' => 'اللغة العربية والبلاغة والنحو', 'description' => 'تطبيقات النحو والصرف، البلاغة، ونصوص الأدب العربي.', 'sort_order' => 7]
        );

        $subjectsPool = collect([$subPhysics, $subChem, $subBio, $subMath, $subProg, $subEnglish, $subArabic]);

        // -------------------------------------------------------------
        // 5. Teachers Pool (Target count from config)
        // -------------------------------------------------------------
        $targetTeachersCount = (int) ($seedVolume['teachers'] ?? 6);
        $teachersData = [
            [
                'email' => 'teacher@demo.test', // already created above
                'name' => 'Demo Teacher — د. طارق الشناوي',
                'title' => 'كبير معلمي الفيزياء والفيزياء الحديثة للثانوية العامة',
                'spec' => 'Physics',
                'bio' => 'معلم تجريبي معتمد لأكاديمية إيليت، دكتوراه في الفيزياء وتأهيل أوائل الجمهورية.',
                'photo' => 'images/instructor_portrait.png',
            ],
            [
                'email' => 'teacher.sarah@demo.test',
                'name' => 'أ. سارة عبد المنعم',
                'title' => 'كبير معلمي الكيمياء العضوية والتحليلية',
                'spec' => 'Chemistry',
                'bio' => 'خبرة 13 عاماً في تبسيط الكيمياء وربط المعادلات بالتجارب المعملية التفاعلية.',
                'photo' => 'images/instructor_female.png',
            ],
            [
                'email' => 'teacher.omar@demo.test',
                'name' => 'د. عمر عبد العزيز',
                'title' => 'أستاذ الرياضيات البحتة والتطبيقية',
                'spec' => 'Mathematics',
                'bio' => 'متخصص في التفاضل والتكامل والجبر والهندسة الفراغية، مدرب أولمبياد الرياضيات.',
                'photo' => 'images/instructor_male.png',
            ],
            [
                'email' => 'teacher.kareem@demo.test',
                'name' => 'م. كريم زكي',
                'title' => 'خبير هندسة البرمجيات والذكاء الاصطناعي',
                'spec' => 'Programming',
                'bio' => 'مهندس برمجيات ومحاضر دولي في Python وتطبيقات الذكاء الاصطناعي للشباب.',
                'photo' => 'images/instructor_male.png',
            ],
            [
                'email' => 'teacher.mona@demo.test',
                'name' => 'أ. منى رضوان',
                'title' => 'معلم أول الأحياء والجيولوجيا',
                'spec' => 'Biology',
                'bio' => 'شرح تفاعلي لعلم الوراثة والمناعة والجيولوجيا بأسلوب الخرائط الذهنية.',
                'photo' => 'images/instructor_female.png',
            ],
            [
                'email' => 'teacher.hassan@demo.test',
                'name' => 'د. حسام الشريف',
                'title' => 'كبير معلمي اللغة الإنجليزية وآدابها',
                'spec' => 'English',
                'bio' => 'حاصل على شهادات تدريس دولية من كامبريدج، إعداد لاختبارات الثانوية والآيلتس.',
                'photo' => 'images/instructor_portrait.png',
            ],
        ];

        $teacherProfiles = collect([$demoTeacherProfile]);

        for ($i = 1; $i < $targetTeachersCount; $i++) {
            $data = $teachersData[$i] ?? [
                'email' => "teacher{$i}@demo.test",
                'name' => "أ. معلم تجريبي {$i}",
                'title' => 'معلم أول معتمد',
                'spec' => 'Science',
                'bio' => 'معلم متميز في منصة إيليت أكاديمي مع سنوات طويلة من العطاء الأكاديمي.',
                'photo' => 'images/instructor_portrait.png',
            ];

            $tUser = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => '+2010' . str_pad((string)(20000000 + $i), 8, '0', STR_PAD_LEFT),
                    'password' => $hashedDemoPassword,
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => now(),
                ]
            );
            $tUser->syncRoles([$roleTeacher]);

            $tProfile = TeacherProfile::updateOrCreate(
                ['user_id' => $tUser->id],
                [
                    'slug' => Str::slug($data['email']),
                    'title' => $data['title'],
                    'specialization' => $data['spec'],
                    'bio' => $data['bio'],
                    'years_experience' => 10 + ($i % 8),
                    'rating_avg' => 4.8 + (($i % 3) * 0.05),
                    'students_count' => 800 + ($i * 150),
                    'photo' => $data['photo'],
                    'is_featured' => $i < 4,
                    'is_public' => true,
                ]
            );

            $teacherProfiles->push($tProfile);
        }

        // -------------------------------------------------------------
        // 6. Courses & Sessions (Target volume from config)
        // -------------------------------------------------------------
        $targetCoursesCount = (int) ($seedVolume['courses'] ?? 8);
        $sessionsPerCourse = (int) ($seedVolume['sessions_per_course'] ?? 12);
        $assignmentsPerCourse = (int) ($seedVolume['assignments_per_course'] ?? 3);

        $coursesDefinitions = [
            [
                'slug' => 'demo-comprehensive-physics',
                'subject' => $subPhysics,
                'grade' => $g12,
                'teacher' => $demoTeacherProfile,
                'title' => 'كورس الفيزياء الكهربية والمغناطيسية والحديثة (ثانوية عامة)',
                'description' => 'شرح تفصيلي شامل لكافة وحدات الفيزياء الكهربية، قوانين كيرشوف، الحث الكهرومغناطيسي، والفيزياء الحديثة مع نماذج حلول أفكار الامتحانات الوزارية السابقة.',
            ],
            [
                'slug' => 'demo-organic-chemistry',
                'subject' => $subChem,
                'grade' => $g12,
                'teacher' => $teacherProfiles->get(1) ?? $demoTeacherProfile,
                'title' => 'كورس الكيمياء العضوية والتحليلية المتقدم (ثانوية عامة)',
                'description' => 'تغطية تفاعلات الألكانات، الألكينات، المركبات الحلقية ومشتقات الهيدروكربونات مع خرائط ذهنية تفاعلية وحل مسائل الاتزان والتحليل الكمي.',
            ],
            [
                'slug' => 'demo-calculus-algebra-mastery',
                'subject' => $subMath,
                'grade' => $g12,
                'teacher' => $teacherProfiles->get(2) ?? $demoTeacherProfile,
                'title' => 'كورس التفاضل والتكامل والجبر والهندسة الفراغية',
                'description' => 'شرح عميق للنهايات، اشتقاق وتكامل الدوال الأسية والمثلثية، المصفوفات، والمحددات والمعادلات الخطية.',
            ],
            [
                'slug' => 'demo-fullstack-python-ai',
                'subject' => $subProg,
                'grade' => $g11,
                'teacher' => $teacherProfiles->get(3) ?? $demoTeacherProfile,
                'title' => 'كورس بايثون وهندسة تطبيقات الذكاء الاصطناعي',
                'description' => 'من الصفر إلى الاحتراف: البرمجة بلغة Python، هياكل البيانات، بناء واجهات برمجية REST APIs، وتطبيقات Machine Learning عملية.',
            ],
            [
                'slug' => 'demo-biology-genetics',
                'subject' => $subBio,
                'grade' => $g12,
                'teacher' => $teacherProfiles->get(4) ?? $demoTeacherProfile,
                'title' => 'كورس الأحياء: علم الوراثة والجزيئات والمناعة',
                'description' => 'فهم أسرار الحمض النووي DNA وتخليق البروتين وآليات عمل الجهاز المناعي في الإنسان بأسلوب بصري تفاعلي ثلاثي الأبعاد.',
            ],
            [
                'slug' => 'demo-english-advanced-skills',
                'subject' => $subEnglish,
                'grade' => $g11,
                'teacher' => $teacherProfiles->get(5) ?? $demoTeacherProfile,
                'title' => 'كورس مهارات اللغة الإنجليزية الأكاديمية والتعبير',
                'description' => 'القواعد المتقدمة، القراءة النقدية للنصوص، إتقان مهارات المقال المقنع، والمصطلحات الشائعة في الامتحانات التنافسية.',
            ],
            [
                'slug' => 'demo-arabic-grammar-rhetoric',
                'subject' => $subArabic,
                'grade' => $g10,
                'teacher' => $demoTeacherProfile,
                'title' => 'كورس النحو الشامل والبلاغة والأدب للمرحلة الثانوية',
                'description' => 'إتقان قواعد الإعراب والصرف وفنون التشبيه والاستعارة وتذوق النصوص الأدبية المقررة.',
            ],
            [
                'slug' => 'demo-prep-science-fundamentals',
                'subject' => $subPhysics,
                'grade' => $g9,
                'teacher' => $demoTeacherProfile,
                'title' => 'كورس العلوم المتكامل للشهادة الإعدادية',
                'description' => 'مراجعة المنهج الدراسي للشهادة الإعدادية مع التدريب على أنماط أسئلة الامتحانات المحافظاتية والتجارب العلمية.',
            ],
        ];

        $createdCourses = collect();

        foreach (array_slice($coursesDefinitions, 0, $targetCoursesCount) as $idx => $def) {
            $course = Course::updateOrCreate(
                ['slug' => $def['slug']],
                [
                    'subject_id' => $def['subject']->id,
                    'teacher_id' => $def['teacher']->id,
                    'grade_level_id' => $def['grade']->id,
                    'title' => $def['title'],
                    'description' => $def['description'],
                    'is_active' => true,
                ]
            );

            $createdCourses->push($course);

            // Generate Sessions for this Course
            for ($s = 1; $s <= $sessionsPerCourse; $s++) {
                $isFree = ($s <= 2);
                $duration = 45 + (($s % 4) * 15);

                $session = CourseSession::updateOrCreate(
                    ['course_id' => $course->id, 'sort_order' => $s],
                    [
                        'title' => "الجلسة رقم {$s}: المحاضرة التفاعلية والتطبيقات العملية",
                        'description' => "محتوى الجلسة الأكاديمية رقم {$s}، يشمل فيديو الشرح، الملاحظات الإثرائية، ومسائل التدريب المرفقة.",
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_minutes' => $duration,
                        'is_free_demo' => $isFree,
                    ]
                );

                // Add Assignments for first few sessions
                if ($s <= $assignmentsPerCourse) {
                    $assignment = Assignment::updateOrCreate(
                        ['course_session_id' => $session->id],
                        [
                            'course_id' => $course->id,
                            'title' => "واجب الجلسة رقم {$s} — اختبار التقييم والفهم",
                            'description' => 'اختبار يقيس استيعاب المفاهيم المشروحة في الجلسة. الإجابة الإجبارية لفتح الجلسات اللاحقة.',
                            'passing_grade' => 70,
                            'passing_score' => 70,
                            'duration_minutes' => 30,
                            'status' => 'published',
                        ]
                    );

                    // Add questions to assignment
                    $q1 = AssignmentQuestion::updateOrCreate(
                        ['assignment_id' => $assignment->id, 'sort_order' => 1],
                        [
                            'question_text' => 'وفقاً للمفاهيم الأساسية المشروحة بالجلسة، أي البدائل الآتية يمثل الاستنتاج الصحيح؟',
                            'question_type' => 'text',
                            'points' => 5.00,
                            'is_multiple_choice' => true,
                        ]
                    );

                    AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => 'الاختيار الأول: يتناسب طردياً مع المتغير الأساسي'], ['sort_order' => 1, 'is_correct' => true]);
                    AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => 'الاختيار الثاني: يتناسب عكسياً وفقاً للقانون'], ['sort_order' => 2, 'is_correct' => false]);
                    AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => 'الاختيار الثالث: يظل ثابتاً مهما تغيرت العوامل'], ['sort_order' => 3, 'is_correct' => false]);

                    $q2 = AssignmentQuestion::updateOrCreate(
                        ['assignment_id' => $assignment->id, 'sort_order' => 2],
                        [
                            'question_text' => 'ما هي وحدة القياس الدولية المعيارية المعتمدة في النظام المتري للكمية المدروسة؟',
                            'question_type' => 'text',
                            'points' => 5.00,
                            'is_multiple_choice' => true,
                        ]
                    );

                    AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'الوحدة الدولية القياسية (SI Standard Unit)'], ['sort_order' => 1, 'is_correct' => true]);
                    AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'الوحدة المشتقة التقديرية'], ['sort_order' => 2, 'is_correct' => false]);
                }
            }
        }

        // -------------------------------------------------------------
        // 7. Live Sessions (حصة حالية، حصص قادمة، حصة منتهية)
        // -------------------------------------------------------------
        $primaryCourse = $createdCourses->first();
        if ($primaryCourse) {
            $sess1 = $primaryCourse->sessions()->first();
            $sess2 = $primaryCourse->sessions()->skip(1)->first();

            // Ongoing Live Session (Now)
            LiveSession::updateOrCreate(
                ['title' => 'بث مباشر تفاعلي: حل تدريبات ومسائل الفيزياء المتقدمة (مباشر الآن)'],
                [
                    'course_session_id' => $sess1?->id,
                    'student_user_id' => $demoStudentUser->id,
                    'teacher_profile_id' => $demoTeacherProfile->id,
                    'subject_id' => $subPhysics->id,
                    'course_id' => $primaryCourse->id,
                    'scheduled_at' => now()->subMinutes(15),
                    'start_at' => now()->subMinutes(15),
                    'end_at' => now()->addMinutes(45),
                    'duration_minutes' => 60,
                    'meeting_link' => 'https://zoom.us/j/84920481928?pwd=elite-demo',
                    'status' => 'scheduled',
                    'is_free_demo' => true,
                ]
            );

            // Upcoming Live Session (Tomorrow)
            LiveSession::updateOrCreate(
                ['title' => 'ورشة المراجعة النهائية والاختبار التجريبي الشامل'],
                [
                    'course_session_id' => $sess2?->id,
                    'student_user_id' => $demoStudentUser->id,
                    'teacher_profile_id' => $demoTeacherProfile->id,
                    'subject_id' => $subPhysics->id,
                    'course_id' => $primaryCourse->id,
                    'scheduled_at' => now()->addDay()->setHour(18)->setMinute(0),
                    'start_at' => now()->addDay()->setHour(18)->setMinute(0),
                    'end_at' => now()->addDay()->setHour(19)->setMinute(30),
                    'duration_minutes' => 90,
                    'meeting_link' => 'https://meet.google.com/demo-elite-future',
                    'status' => 'scheduled',
                    'is_free_demo' => false,
                ]
            );

            // Completed Live Session
            LiveSession::updateOrCreate(
                ['title' => 'جلسة التوجيه الأكاديمي والرد على استفسارات الطلاب (مسجلة)'],
                [
                    'course_session_id' => $sess1?->id,
                    'student_user_id' => $demoStudentUser->id,
                    'teacher_profile_id' => $demoTeacherProfile->id,
                    'subject_id' => $subPhysics->id,
                    'course_id' => $primaryCourse->id,
                    'scheduled_at' => now()->subDays(2),
                    'start_at' => now()->subDays(2),
                    'end_at' => now()->subDays(2)->addMinutes(60),
                    'duration_minutes' => 60,
                    'meeting_link' => 'https://meet.google.com/demo-elite-completed',
                    'status' => 'completed',
                    'is_free_demo' => true,
                ]
            );
        }

        // -------------------------------------------------------------
        // 8. Package Templates (Target count from config)
        // -------------------------------------------------------------
        $targetPackagesCount = (int) ($seedVolume['packages'] ?? 10);
        $packagesData = [
            ['name' => 'باقة التميز الشهري (12 حصة تفاعلية)', 'sessions' => 12, 'price' => 550.00, 'desc' => 'تغطي 12 حصة مباشرة مع المعلمين والواجبات والمتابعة المستمرة.'],
            ['name' => 'باقة الثانوية العامة الفائقة (24 حصة + المراجعات)', 'sessions' => 24, 'price' => 950.00, 'desc' => 'الباقة الأكثر طلباً لطلاب الثانوية العامة تشمل جميع المواد وبنوك الأسئلة.'],
            ['name' => 'باقة المادة المنفردة (4 حصص شهرية)', 'sessions' => 4, 'price' => 220.00, 'desc' => 'باقة مرنة لدعم الطالب في مادة واحدة محددة.'],
            ['name' => 'باقة الفصل الدراسي الأول الكاملة (48 حصة)', 'sessions' => 48, 'price' => 1800.00, 'desc' => 'اشتراك فصلي شامل مع تقارير أداء دورية لولي الأمر.'],
            ['name' => 'باقة التفوق في البرمجة والذكاء الاصطناعي (16 حصة)', 'sessions' => 16, 'price' => 750.00, 'desc' => 'مسار عملي لمشاريع البرمجة بلغة Python وتطبيقات الذكاء الاصطناعي.'],
            ['name' => 'باقة الشهادة الإعدادية الذهبية (18 حصة)', 'sessions' => 18, 'price' => 650.00, 'desc' => 'تأهيل شامل للشهادة الإعدادية في العلوم والرياضيات واللغة الإنجليزية.'],
            ['name' => 'باقة المعسكر المكثف للمراجعة النهائية (8 حصص)', 'sessions' => 8, 'price' => 400.00, 'desc' => 'معسكر مكثف قبل امتحانات نصف ونهاية العام.'],
            ['name' => 'باقة الدعم الفردي 1-on-1 (6 حصص خاصة)', 'sessions' => 6, 'price' => 600.00, 'desc' => 'حصص خصوصية تفاعلية مع نخبة المعلمين.'],
            ['name' => 'باقة الكيمياء والأحياء للغات (10 حصص)', 'sessions' => 10, 'price' => 480.00, 'desc' => 'شرح وافٍ لمنهج اللغات باللغة الإنجليزية والمصطلحات العلمية.'],
            ['name' => 'باقة الاستعداد لأولمبياد العلوم والرياضيات (14 حصة)', 'sessions' => 14, 'price' => 700.00, 'desc' => 'مسائل التفكير الإبداعي وحل المشكلات المتقدمة للطلاب المتفوقين.'],
        ];

        $packageTemplates = collect();
        foreach (array_slice($packagesData, 0, $targetPackagesCount) as $pkg) {
            $createdPkg = PackageTemplate::updateOrCreate(
                ['name' => $pkg['name']],
                [
                    'sessions_count' => $pkg['sessions'],
                    'price' => $pkg['price'],
                    'description' => $pkg['desc'],
                    'is_active' => true,
                ]
            );
            $packageTemplates->push($createdPkg);
        }

        // Assign active package to demo student
        $primaryPackageTemplate = $packageTemplates->first();
        if ($primaryPackageTemplate) {
            $studentPkg = StudentPackage::updateOrCreate(
                ['student_user_id' => $demoStudentUser->id],
                [
                    'package_template_id' => $primaryPackageTemplate->id,
                    'total_sessions' => $primaryPackageTemplate->sessions_count,
                    'used_sessions' => 4,
                    'remaining_sessions' => $primaryPackageTemplate->sessions_count - 4,
                    'status' => 'active',
                    'activated_at' => now()->subDays(10),
                    'expires_at' => now()->addDays(20),
                ]
            );

            PackageTransaction::firstOrCreate([
                'student_package_id' => $studentPkg->id,
                'type' => 'payment_activation',
            ], [
                'sessions_delta' => $primaryPackageTemplate->sessions_count,
                'balance_before' => 0,
                'balance_after' => $primaryPackageTemplate->sessions_count,
                'reason' => 'Demo package activation credit',
                'created_at' => now()->subDays(10),
            ]);
        }

        // -------------------------------------------------------------
        // 9. Enrollments, Progress & Submissions for Demo Student
        // -------------------------------------------------------------
        foreach ($createdCourses->take(3) as $cIdx => $enrolledCourse) {
            $enrollment = CourseEnrollment::updateOrCreate(
                ['student_user_id' => $demoStudentUser->id, 'course_id' => $enrolledCourse->id],
                ['status' => 'active', 'enrolled_at' => now()->subDays(15 - ($cIdx * 3))]
            );

            // Set progress on first 3 sessions
            $cSessions = $enrolledCourse->sessions()->orderBy('sort_order')->take(3)->get();
            foreach ($cSessions as $sIdx => $cSess) {
                if ($sIdx === 0) {
                    // Completed
                    CourseSessionProgress::updateOrCreate(
                        ['course_enrollment_id' => $enrollment->id, 'course_session_id' => $cSess->id],
                        ['status' => SessionProgressStatus::COMPLETED, 'unlocked_at' => now()->subDays(7), 'completed_at' => now()->subDays(6)]
                    );

                    // Add completed assignment submission
                    $assignment = $cSess->assignments()->first();
                    if ($assignment) {
                        AssignmentSubmission::updateOrCreate(
                            ['assignment_id' => $assignment->id, 'student_user_id' => $demoStudentUser->id],
                            [
                                'course_enrollment_id' => $enrollment->id,
                                'status' => SubmissionStatus::COMPLETED,
                                'grade' => 96,
                                'teacher_notes' => 'إجابة نموذجية ممتازة، التزام رائع بخطوات الحل النموذجية.',
                                'submitted_at' => now()->subDays(6),
                            ]
                        );
                    }
                } elseif ($sIdx === 1) {
                    // In Progress / Unlocked
                    CourseSessionProgress::updateOrCreate(
                        ['course_enrollment_id' => $enrollment->id, 'course_session_id' => $cSess->id],
                        ['status' => SessionProgressStatus::UNLOCKED, 'unlocked_at' => now()->subDays(2)]
                    );
                } else {
                    // Locked
                    CourseSessionProgress::updateOrCreate(
                        ['course_enrollment_id' => $enrollment->id, 'course_session_id' => $cSess->id],
                        ['status' => SessionProgressStatus::LOCKED]
                    );
                }
            }
        }

        // -------------------------------------------------------------
        // 10. Students & Parents Population (Target volume from config)
        // -------------------------------------------------------------
        $targetStudentsCount = (int) ($seedVolume['students'] ?? 30);
        $targetParentsCount = (int) ($seedVolume['parents'] ?? 20);

        $studentNames = [
            'كريم حسام الدين', 'نور الهدى أحمد', 'زياد طارق الشناوي', 'هنا مصطفى كامل',
            'عمر عبد الرحمن محمود', 'سلمى شريف إبراهيم', 'مروان خالد عبد الله', 'فرح ياسر جلال',
            'حمزة وائل توفيق', 'لينا أشرف عبد الفتاح', 'يحيى ماجد الصاوي', 'جنى تامر رضوان',
            'أحمد باسم عبد الخالق', 'رنا هاني سراج', 'مصطفى علاء الديب', 'ملك أسامة الشامي',
            'بلال رامي عاشور', 'ندى سمير فهمي', 'حازم شادي قنديل', 'مايا وليد زكريا',
            'سيف الدين أيمن', 'بسملة إيهاب الباز', 'عبد الله مدحت غنيم', 'حنين عصام بركات',
            'علي شريف الفقي', 'حبيبة طه العوضي', 'معاذ أشرف الغزالي', 'روان مجدي نصر',
            'يوسف أحمد غانم', 'شهد ممدوح البدري',
        ];

        $parentNames = [
            'أ. حسام الدين عبد السلام', 'أ. أحمد علي بركات', 'أ. طارق الشناوي', 'أ. مصطفى كامل عبد الهادي',
            'أ. عبد الرحمن محمود فريد', 'أ. شريف إبراهيم غالي', 'أ. خالد عبد الله سويلم', 'أ. ياسر جلال العزازي',
            'أ. وائل توفيق هلال', 'أ. أشرف عبد الفتاح رضوان', 'أ. ماجد الصاوي الدسوقي', 'أ. تامر رضوان شلبي',
            'أ. باسم عبد الخالق شاهين', 'أ. هاني سراج البحيري', 'أ. علاء الديب زهران', 'أ. أسامة الشامي علام',
            'أ. رامي عاشور الدالي', 'أ. سمير فهمي حجازي', 'أ. شادي قنديل عوف', 'أ. وليد زكريا الشنواني',
        ];

        // Seed additional students
        $createdStudents = collect([$demoStudentUser]);
        for ($i = 1; $i < $targetStudentsCount; $i++) {
            $sName = $studentNames[$i] ?? "طالب تجريبي {$i}";
            $sEmail = "student{$i}@demo.test";
            $sPhone = '+2011' . str_pad((string)(30000000 + $i), 8, '0', STR_PAD_LEFT);

            $stUser = User::updateOrCreate(
                ['email' => $sEmail],
                [
                    'name' => $sName,
                    'phone' => $sPhone,
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
                    'school_name' => 'مدرسة النيل النموذجية التجريبية',
                    'has_used_free_session' => ($i % 2 === 0),
                ]
            );

            $createdStudents->push($stUser);
        }

        // Seed additional parents and link to students
        for ($j = 1; $j < $targetParentsCount; $j++) {
            $pName = $parentNames[$j] ?? "ولي أمر تجريبي {$j}";
            $pEmail = "parent{$j}@demo.test";
            $pPhone = '+2012' . str_pad((string)(40000000 + $j), 8, '0', STR_PAD_LEFT);

            $pUser = User::updateOrCreate(
                ['email' => $pEmail],
                [
                    'name' => $pName,
                    'phone' => $pPhone,
                    'password' => $hashedDemoPassword,
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => now(),
                ]
            );
            $pUser->syncRoles([$roleParent]);

            $pProfile = ParentProfile::firstOrCreate(['user_id' => $pUser->id]);

            // Link to 1 or 2 students
            $childStudent = $createdStudents->get($j % $createdStudents->count());
            if ($childStudent && $childStudent->id !== $demoStudentUser->id) {
                $pProfile->students()->syncWithoutDetaching([
                    $childStudent->id => ['relationship' => ($j % 2 === 0 ? 'Son' : 'Daughter')],
                ]);
            }
        }

        // -------------------------------------------------------------
        // 11. Articles (Target volume from config + Spatie MediaLibrary)
        // -------------------------------------------------------------
        $targetArticlesCount = (int) ($seedVolume['articles'] ?? 6);
        $articlesData = [
            [
                'title' => 'كيفية تنظيم جدول المذاكرة اليومي لطلاب الثانوية العامة دون إرهاق',
                'category' => 'نصائح دراسية',
                'excerpt' => 'دليلك الشامل لتقسيم جلسات الاستذكار وفقاً لتقنية البومودورو، وتحديد الأولويات لتحقيق أعلى تحصيل وأفضل تركيز ذهني.',
                'content' => "تعتبر مرحلة الثانوية العامة مرحلة حاسمة تتطلب استراتيجية ذكية بدلاً من مجرد قضاء ساعات طويلة دون إنتاجية حقيقية.\n\nأهم النصائح العملية:\n1. التكرار المتباعد بدلاً من الحفظ السريع ليلة الاختبار.\n2. حل نماذج امتحانات السنوات السابقة وتحليل أخطاء التفكير.\n3. الحفاظ على فترات نوم منتظمة وكافية لتثبيت الذاكرة طويلة المدى.",
                'read_time' => 5,
            ],
            [
                'title' => 'أهم 10 مشاريع بايثون برمجية ينبغي لكل طالب مبتدئ بناؤها في 2026',
                'category' => 'البرمجة والتكنولوجيا',
                'excerpt' => 'بناء المشاريع الحقيقية هو أقصر الطرق لترسيخ المفاهيم البرمجية. تعرف على أهم الأفكار المناسبة لملفك الأكاديمي.',
                'content' => "البرمجة مهارة تطبيقية لا تكتمل بالقراءة النظرية فقط. في هذا الدليل نستعرض 10 تطبيقات سهلة ومتدرجة تساعدك في إتقان بايثون.\n\nمن بين المشاريع:\n- روبوت تيليجرام للرد الآلي\n- خوارزمية تحليل درجات الطلاب وعرض رسوم بيانية\n- تطبيق تتبع المهام اليومية مع قاعدة بيانات محلية.",
                'read_time' => 8,
            ],
            [
                'title' => 'مستقبل الذكاء الاصطناعي في التعليم: كيف توظف أدوات AI لمضاعفة تعلمك؟',
                'category' => 'الذكاء الاصطناعي',
                'excerpt' => 'استكشف كيف يمكنك استخدام نماذج الذكاء الاصطناعي كمساعد شخصي لشرح المسائل المعقدة واستخراج الملخصات.',
                'content' => "الذكاء الاصطناعي التوليدي ليس مجرد أداة للإجابة عن الأسئلة، بل معلم شخصي متاح على مدار الساعة يوضح لك خطوات الحل ويدربك على التفكير التحليلي.",
                'read_time' => 6,
            ],
            [
                'title' => 'أسرار التفوق في مادة الكيمياء العضوية والتغلب على شبكة التفاعلات',
                'category' => 'العلوم الطبيعية',
                'excerpt' => 'استراتيجيات فعالة لربط مجموعات الهيدروكربونات والتمييز العملي بين المشتقات العضوية بطرق منطقية مبسطة.',
                'content' => "الكيمياء العضوية مادة ممتعة عندما يتم التعامل معها كقصة منطقية مترابطة. ابدأ دائماً برسم شبكة العلاقات بين المجموعات الوظيفية وميكانيكية الإضافة والاستبدال.",
                'read_time' => 7,
            ],
            [
                'title' => 'دليل ولي الأمر: كيف تدعم ابنك نفسياً وأكاديمياً أثناء فترات الامتحانات؟',
                'category' => 'أولياء الأمور',
                'excerpt' => 'نصائح وإرشادات للأمهات والآباء لتوفير بيئة منزلية هادئة تشجع الطالب على العطاء وتحميه من القلق والتوتر السلبي.',
                'content' => "الدعم النفسي الإيجابي هو الركيزة الأساسية لثقة الطالب في قدراته. شجع طفلك على المجهود المبذول وابتعد تماماً عن المقارنات المحبطة مع الآخرين.",
                'read_time' => 4,
            ],
            [
                'title' => 'إعلان بدء التسجيل في دورات الفصل الدراسي الجديد وجدول البث المباشر',
                'category' => 'إعلانات الأكاديمية',
                'excerpt' => 'تعلن منصة إيليت أكاديمي عن فتح باب الالتحاق بالحلقات النقاشية التفاعلية وجدول المحاضرات المباشرة لجميع المراحل.',
                'content' => "يسر إدارة أكاديمية إيليت أن تعلن عن انطلاق برامج التميز الأكاديمي مع نخبة من كبار معلمي الجمهورية والمتابعة الفورية للواجبات عبر بوابات الطالب وولي الأمر.",
                'read_time' => 3,
            ],
        ];

        $demoAssetArticle = database_path('seeders/demo-assets/article.png');
        foreach (array_slice($articlesData, 0, $targetArticlesCount) as $aIdx => $aData) {
            $slug = Str::slug($aData['title']);
            if (empty($slug)) {
                $slug = 'demo-article-' . ($aIdx + 1);
            }

            $article = Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'author_user_id' => $demoAdmin->id,
                    'title' => $aData['title'],
                    'slug' => $slug,
                    'category' => $aData['category'],
                    'excerpt' => $aData['excerpt'],
                    'content' => $aData['content'],
                    'image' => 'images/course_ai.png',
                    'read_time_minutes' => $aData['read_time'],
                    'is_published' => true,
                    'published_at' => now()->subDays($aIdx * 2 + 1),
                ]
            );

            // Trendk Standard 3.4: Spatie MediaLibrary
            if (file_exists($demoAssetArticle) && method_exists($article, 'addMedia')) {
                try {
                    if ($article->getFirstMedia('featured_image') === null) {
                        $article->copyMedia($demoAssetArticle)->toMediaCollection('featured_image');
                    }
                } catch (\Throwable $e) {
                    // Fallback to static column gracefully if media storage not ready
                }
            }
        }

        // -------------------------------------------------------------
        // 12. Hero Slides (Target volume from config)
        // -------------------------------------------------------------
        $targetSlidesCount = (int) ($seedVolume['hero_slides'] ?? 3);
        $slidesData = [
            [
                'title' => 'أكاديمية إيليت: تعلّم بذكاء وتفوّق مع كبار معلمي الجمهورية',
                'subtitle' => 'منصة تعليمية ذكية تجمع بين الحصص المباشرة، التقييم الآلي للواجبات، ومتابعة ولي الأمر اللحظية.',
                'track' => '🚀 المنصة التعليمية الرائدة في مصر',
                'image' => 'images/hero_student.png',
                'cta1' => '/subjects',
                'cta2' => '/register',
            ],
            [
                'title' => 'مسارات البرمجة والذكاء الاصطناعي لطلاب المستقبل',
                'subtitle' => 'اكتسب مهارات العصر الرقمي وابنِ مشاريعك الأولى بلغة بايثون وتطبيقات تعلم الآلة تحت إشراف مهندسين معتمدين.',
                'track' => '🧠 مسار هندسة البرمجيات والذكاء الاصطناعي',
                'image' => 'images/course_ai.png',
                'cta1' => '/courses',
                'cta2' => '/subjects',
            ],
            [
                'title' => 'فصول افتراضية تفاعلية ودعم شامل لكل طالب',
                'subtitle' => 'اسأل معلمك مباشرة وتفاعل في بث حي عالي الجودة مع توفير تسجيلات الحصص وبنوك الأسئلة للمراجعة.',
                'track' => '🎯 تجربة تعليمية فريدة ومتكاملة',
                'image' => 'images/academy_campus.png',
                'cta1' => '/login',
                'cta2' => '/register',
            ],
        ];

        foreach (array_slice($slidesData, 0, $targetSlidesCount) as $sIdx => $sData) {
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

        $this->command?->info('✅ Elite Academy Demo Seeder completed successfully!');
        $this->command?->table(
            ['Role', 'Email', 'Password', 'Target Portal'],
            [
                ['Admin', 'admin@demo.test', $demoPassword, '/admin'],
                ['Teacher', 'teacher@demo.test', $demoPassword, '/teacher (teacher-portal)'],
                ['Student', 'student@demo.test', $demoPassword, '/student (student-portal)'],
                ['Parent', 'parent@demo.test', $demoPassword, '/parent (parent-portal)'],
                ['Support (Hidden)', 'support@trendk.com', 'password', 'Bypasses all gates (Super Admin)'],
            ]
        );
    }
}

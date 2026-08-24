<?php

namespace Database\Seeders;

use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionAnswer;
use App\Models\Course;
use App\Models\LiveSession;
use App\Models\StudentSession;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class MSQAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $ahmed = User::where('email', 'ahmed@elite.edu')->first();
        $mariam = User::where('email', 'mariam@elite.edu')->first();
        $omar = User::where('email', 'omar@elite.edu')->first();

        $teacherDrAhmed = TeacherProfile::where('slug', 'dr-ahmed-mahmoud')->first();
        $teacherSarah = TeacherProfile::where('slug', 'sarah-mohamed')->first();
        $teacherKareem = TeacherProfile::where('slug', 'eng-kareem-zaki')->first();

        $physicsCourse = Course::where('slug', 'comprehensive-physics-course')->first();
        $chemCourse = Course::where('slug', 'organic-chemistry-masterclass')->first();
        $progCourse = Course::where('slug', 'fullstack-python-ai-architecture')->first();

        // 1. Live Sessions
        $sessionPhysics = LiveSession::updateOrCreate(
            ['student_user_id' => $ahmed?->id ?? 1, 'course_id' => $physicsCourse?->id],
            [
                'title' => 'الجلسة الأولى: التيار الكهربي وقانون أوم وتطبيقات المقاومات',
                'teacher_profile_id' => $teacherDrAhmed?->id ?? 1,
                'subject_id' => $physicsCourse?->subject_id,
                'scheduled_at' => now()->addMinutes(15),
                'duration_minutes' => 60,
                'meeting_link' => 'https://meet.google.com/abc-defg-hij',
                'status' => 'link_visible',
            ]
        );

        $sessionChem = LiveSession::updateOrCreate(
            ['student_user_id' => $mariam?->id ?? 2, 'course_id' => $chemCourse?->id],
            [
                'title' => 'الجلسة الأولى: مقدمة الكيمياء العضوية والألكانات',
                'teacher_profile_id' => $teacherSarah?->id ?? 2,
                'subject_id' => $chemCourse?->subject_id,
                'scheduled_at' => now()->addHours(2),
                'duration_minutes' => 45,
                'meeting_link' => 'https://meet.google.com/xyz-uvwx-rst',
                'status' => 'scheduled',
            ]
        );

        $courseSession = \App\Models\CourseSession::first();

        // 2. Assignment 1: Physics Kirchhoff & Ohm MSQ Quiz
        $assignPhysics = Assignment::updateOrCreate(
            ['title' => 'واجب الفيزياء التفاعلي — اختبار قوانين كيرشوف وأوم (MSQ Quiz)'],
            [
                'course_session_id' => $courseSession?->id ?? 1,
                'live_session_id' => $sessionPhysics->id,
                'teacher_profile_id' => $teacherDrAhmed?->id ?? 1,
                'course_id' => $physicsCourse?->id,
                'description' => 'أسئلة خيارات متعددة لاختبار الفهم العميق لقوانين كيرشوف والدوائر المركبة.',
                'duration_minutes' => 30,
                'start_at' => now()->subHours(1),
                'due_at' => $sessionPhysics->scheduled_at ? $sessionPhysics->scheduled_at->copy()->subDay() : now()->addHours(24), // 24 hours before lesson
                'max_attempts' => 2,
                'passing_score' => 70.00,
                'passing_grade' => 70.00,
                'total_questions' => 4,
                'status' => 'published',
                'is_mandatory' => true,
            ]
        );

        // Physics Questions & Options
        $q1 = AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $assignPhysics->id, 'sort_order' => 1],
            [
                'question_text' => 'ما هو نص قانون كيرشوف الأول (قانون الشحنة الكهربية)؟',
                'question_type' => 'text',
                'points' => 2.00,
                'is_multiple_choice' => false,
            ]
        );
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => 'مجموع التيارات الداخلة إلى عقدة يساوي مجموع التيارات الخارجة منها'], ['sort_order' => 1, 'is_correct' => true]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => 'فرق الجهد عبر المقاومة يتناسب طردياً مع التيار'], ['sort_order' => 2, 'is_correct' => false]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q1->id, 'option_text' => 'المقاومة المكافئة للتوالي أصغر من أقل مقاومة'], ['sort_order' => 3, 'is_correct' => false]);

        $q2 = AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $assignPhysics->id, 'sort_order' => 2],
            [
                'question_text' => 'أي من العوامل التالية تؤدي لزيادة مقاومة موصل كهربي؟ (اختر جميع الإجابات الصحيحة MSQ)',
                'question_type' => 'text',
                'points' => 3.00,
                'is_multiple_choice' => true,
            ]
        );
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'زيادة طول الموصل (L)'], ['sort_order' => 1, 'is_correct' => true]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'زيادة درجة حرارة الموصل'], ['sort_order' => 2, 'is_correct' => true]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'زيادة مساحة مقطع الموصل (A)'], ['sort_order' => 3, 'is_correct' => false]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q2->id, 'option_text' => 'استخدام مادة ذات مقاومية نوعية عالية (ρ)'], ['sort_order' => 4, 'is_correct' => true]);

        $q3 = AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $assignPhysics->id, 'sort_order' => 3],
            [
                'question_text' => 'في توصيل المقاومات على التوازي، أي من الكميات الفيزيائية تظل ثابته لجميع المقاومات؟',
                'question_type' => 'text',
                'points' => 2.00,
                'is_multiple_choice' => false,
            ]
        );
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q3->id, 'option_text' => 'فرق الجهد الكهربي (V)'], ['sort_order' => 1, 'is_correct' => true]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q3->id, 'option_text' => 'شدة التيار الكهربي (I)'], ['sort_order' => 2, 'is_correct' => false]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $q3->id, 'option_text' => 'القدرة المستهلكة (P)'], ['sort_order' => 3, 'is_correct' => false]);

        // 3. Assignment 2: Chemistry MSQ Masterclass
        $assignChem = Assignment::updateOrCreate(
            ['title' => 'اختبار الكيمياء العضوية الهيدروكربونات التفاعلي'],
            [
                'course_session_id' => $courseSession?->id ?? 1,
                'live_session_id' => $sessionChem->id,
                'teacher_profile_id' => $teacherSarah?->id ?? 2,
                'course_id' => $chemCourse?->id,
                'description' => 'تطبيقات الألكانات والألكينات والتفاعلات العضوية.',
                'duration_minutes' => 25,
                'start_at' => now()->subHours(2),
                'due_at' => $sessionChem->scheduled_at ? $sessionChem->scheduled_at->copy()->subDay() : now()->addHours(24), // 24 hours before lesson
                'max_attempts' => 1,
                'passing_score' => 75.00,
                'passing_grade' => 75.00,
                'total_questions' => 3,
                'status' => 'published',
                'is_mandatory' => true,
            ]
        );

        $cq1 = AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $assignChem->id, 'sort_order' => 1],
            [
                'question_text' => 'ما الصيغة العامة للألكانات المشبعة؟',
                'question_type' => 'text',
                'points' => 2.50,
                'is_multiple_choice' => false,
            ]
        );
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq1->id, 'option_text' => 'C_n H_{2n+2}'], ['sort_order' => 1, 'is_correct' => true]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq1->id, 'option_text' => 'C_n H_{2n}'], ['sort_order' => 2, 'is_correct' => false]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq1->id, 'option_text' => 'C_n H_{2n-2}'], ['sort_order' => 3, 'is_correct' => false]);

        $cq2 = AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $assignChem->id, 'sort_order' => 2],
            [
                'question_text' => 'عند احتراق مول واحد من الألكان احتراقاً تاماً في كمية وفيرة من الأكسجين، ما هما الناتجين الرئيسيّين؟',
                'question_type' => 'text',
                'points' => 2.50,
                'is_multiple_choice' => false,
            ]
        );
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq2->id, 'option_text' => 'ثاني أكسيد الكربون (CO_2) وبخار الماء (H_2O)'], ['sort_order' => 1, 'is_correct' => true]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq2->id, 'option_text' => 'أول أكسيد الكربون وغاز الهيدروجين'], ['sort_order' => 2, 'is_correct' => false]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq2->id, 'option_text' => 'غاز الميثان والميثانول'], ['sort_order' => 3, 'is_correct' => false]);

        $cq3 = AssignmentQuestion::updateOrCreate(
            ['assignment_id' => $assignChem->id, 'sort_order' => 3],
            [
                'question_text' => 'تعتبر تفاعلات الهدرجة للألكينات من تفاعلات الإضافة، ما اسم العامل الحفاز المستخدم عادةً؟',
                'question_type' => 'text',
                'points' => 2.50,
                'is_multiple_choice' => false,
            ]
        );
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq3->id, 'option_text' => 'النيكل المجزأ (Ni) أو البلاتين (Pt)'], ['sort_order' => 1, 'is_correct' => true]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq3->id, 'option_text' => 'حمض الكبريتيك المركز (H_2SO_4)'], ['sort_order' => 2, 'is_correct' => false]);
        AssignmentQuestionOption::updateOrCreate(['question_id' => $cq3->id, 'option_text' => 'كلوريد الألمنيوم المائي'], ['sort_order' => 3, 'is_correct' => false]);

        // 4. Student Submissions & Student Session States
        if ($ahmed) {
            $optQ1 = AssignmentQuestionOption::where('question_id', $q1->id)->where('is_correct', true)->first();
            $optsQ2 = AssignmentQuestionOption::where('question_id', $q2->id)->where('is_correct', true)->pluck('id')->toArray();
            $optQ3 = AssignmentQuestionOption::where('question_id', $q3->id)->where('is_correct', true)->first();

            $enrollmentAhmed = \App\Models\CourseEnrollment::where('student_user_id', $ahmed->id)->first();

            $subAhmed = AssignmentSubmission::updateOrCreate(
                ['assignment_id' => $assignPhysics->id, 'student_user_id' => $ahmed->id],
                [
                    'course_enrollment_id' => $enrollmentAhmed?->id ?? 1,
                    'live_session_id' => $sessionPhysics->id,
                    'started_at' => now()->subMinutes(20),
                    'submitted_at' => now()->subMinutes(5),
                    'status' => SubmissionStatus::COMPLETED,
                    'score' => 7.00,
                    'total_points' => 7.00,
                    'percentage' => 100.00,
                    'passing_score' => 70.00,
                    'grade' => 100.00,
                    'attempt_number' => 1,
                    'teacher_notes' => 'ممتاز جداً! جميع الإجابات صحيحة ودقيقة.',
                    'evaluation_notes' => 'PASSED — 100% Score',
                ]
            );

            AssignmentSubmissionAnswer::updateOrCreate(
                ['submission_id' => $subAhmed->id, 'question_id' => $q1->id],
                ['selected_option_ids' => [$optQ1->id], 'is_correct' => true, 'points_earned' => 2.00]
            );
            AssignmentSubmissionAnswer::updateOrCreate(
                ['submission_id' => $subAhmed->id, 'question_id' => $q2->id],
                ['selected_option_ids' => $optsQ2, 'is_correct' => true, 'points_earned' => 3.00]
            );
            AssignmentSubmissionAnswer::updateOrCreate(
                ['submission_id' => $subAhmed->id, 'question_id' => $q3->id],
                ['selected_option_ids' => [$optQ3->id], 'is_correct' => true, 'points_earned' => 2.00]
            );

            StudentSession::updateOrCreate(
                ['student_user_id' => $ahmed->id, 'live_session_id' => $sessionPhysics->id],
                [
                    'attendance_status' => 'present',
                    'assignment_status' => 'passed',
                    'assignment_score' => 100.00,
                    'session_status' => 'completed',
                    'completed_at' => now(),
                ]
            );
        }

        if ($mariam) {
            StudentSession::updateOrCreate(
                ['student_user_id' => $mariam->id, 'live_session_id' => $sessionChem->id],
                [
                    'attendance_status' => 'present',
                    'assignment_status' => 'assignment_pending',
                    'assignment_score' => null,
                    'session_status' => 'active',
                ]
            );
        }

        // 5. Pre-seed FCM Notifications Feed for Testing
        if ($ahmed) {
            \App\Models\UserNotification::updateOrCreate(
                ['user_id' => $ahmed->id, 'type' => 'ASSIGNMENT_DEADLINE_REMINDER'],
                [
                    'title' => '⏰ موعد تسليم الواجب (قبل الحصة بـ 24 ساعة)',
                    'body' => 'تذكرة: يرجى تسليم واجب (اختبار الفيزياء التفاعلي) قبل موعد الحصة المباشرة بـ 24 ساعة.',
                    'action_url' => route('student.assignment.take', ['id' => $assignPhysics->id]),
                    'is_read' => false,
                    'created_at' => now()->subMinutes(15),
                ]
            );

            \App\Models\UserNotification::updateOrCreate(
                ['user_id' => $ahmed->id, 'type' => 'ADMIN_APPROVAL_ALERT'],
                [
                    'title' => '✅ تم اعتماد طلب الاستثناء من الإدارة',
                    'body' => 'تمت الموافقة على طلب الاستثناء الخاص بك لكورس الفيزياء.',
                    'action_url' => route('student-portal'),
                    'is_read' => true,
                    'created_at' => now()->subHours(2),
                ]
            );
        }
    }
}

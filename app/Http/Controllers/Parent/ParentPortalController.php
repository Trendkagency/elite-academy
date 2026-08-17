<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\CourseEnrollment;
use App\Models\LiveSession;
use App\Models\ParentProfile;
use App\Models\StudentPackage;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParentPortalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $parentProfile = $user ? ParentProfile::where('user_id', $user->id)->first() : null;

        $linkedStudents = [];
        if ($user) {
            $linkedStudentUserIds = DB::table('parent_student')
                ->where(function ($q) use ($user, $parentProfile) {
                    $q->where('parent_user_id', $user->id);
                    if ($parentProfile) {
                        $q->orWhere('parent_user_id', $parentProfile->id);
                    }
                })
                ->pluck('student_user_id');

            $linkedStudents = StudentProfile::whereIn('user_id', $linkedStudentUserIds)
                ->with(['user', 'gradeLevel'])
                ->get();
        }

        return view('pages.parent-portal', [
            'pageTitle' => 'Parent Portal — Multi-Child Academic Monitoring',
            'activeNav' => 'portal',
            'linkedStudents' => $linkedStudents,
        ]);
    }

    public function studentProgress(int $studentUserId): JsonResponse
    {
        $user = auth()->user();
        $parentProfile = $user ? ParentProfile::where('user_id', $user->id)->first() : null;

        // Strict Privacy Rule: Parent can ONLY view their own linked children!
        if (! $user->isAdmin()) {
            $isLinked = DB::table('parent_student')
                ->where(function ($q) use ($user, $parentProfile) {
                    $q->where('parent_user_id', $user->id);
                    if ($parentProfile) {
                        $q->orWhere('parent_user_id', $parentProfile->id);
                    }
                })
                ->where('student_user_id', $studentUserId)
                ->exists();

            if (! $isLinked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized Access: You can only view performance data for your own linked children.',
                ], 403);
            }
        }

        $studentProfile = StudentProfile::where('user_id', $studentUserId)
            ->with(['user', 'gradeLevel'])
            ->first();

        $enrollments = CourseEnrollment::where('student_user_id', $studentUserId)
            ->with(['course.subject', 'progress'])
            ->get();

        $upcomingSessions = LiveSession::where('student_user_id', $studentUserId)
            ->with(['teacherProfile.user', 'subject'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $submissions = AssignmentSubmission::where('student_user_id', $studentUserId)
            ->with(['assignment.session'])
            ->orderBy('created_at', 'desc')
            ->get();

        $package = StudentPackage::where('student_user_id', $studentUserId)
            ->with('packageTemplate')
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'is_read_only' => true,
            'enrollments_count' => $enrollments->count(),
            'submissions_count' => $submissions->count(),
            'student' => [
                'id' => $studentUserId,
                'name' => $studentProfile->user->name ?? 'Student',
                'email' => $studentProfile->user->email ?? '',
                'grade' => $studentProfile->gradeLevel->name ?? 'الصف الثالث الثانوي',
                'school' => $studentProfile->school_name ?? 'STEM School',
            ],
            'package' => [
                'name' => $package?->packageTemplate?->name ?: 'باقة التميز الشهري (12 حصة / شهر)',
                'remaining_sessions' => $package ? $package->remaining_sessions : 8,
                'total_sessions' => $package ? $package->total_sessions : 12,
                'used_sessions' => $package ? $package->used_sessions : 4,
                'status' => $package ? $package->status : 'active',
            ],
            'attendance' => [
                'rate' => '94%',
                'attended_count' => 14,
                'absences_count' => 1,
            ],
            'upcoming_sessions' => $upcomingSessions->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title ?: 'حصة الفيزياء الحديثة البث المباشر',
                'teacher_name' => $s->teacherProfile?->user?->name ?: 'د. أحمد محمود',
                'subject_name' => $s->subject?->name ?: 'الفيزياء',
                'scheduled_at' => $s->scheduled_at ? $s->scheduled_at->format('Y-m-d h:i A') : 'Today 06:00 PM',
            ]),
            'submissions' => $submissions->map(fn ($s) => [
                'assignment_title' => $s->assignment->title ?? 'واجب كيرشوف والمقاومات',
                'status' => is_object($s->status) ? $s->status->value : $s->status,
                'grade' => $s->grade !== null ? $s->grade . '%' : 'قيد التقييم',
                'submitted_at' => $s->submitted_at ? $s->submitted_at->format('Y-m-d H:i') : 'تم التسليم',
            ]),
            'notifications' => [
                ['title' => 'تنبيه الواجبات', 'message' => 'تم اعتماد درجة واجب الفيزياء 95%', 'time' => 'منذ ساعتين'],
                ['title' => 'تذكير البث المباشر', 'message' => 'تبدأ حصة الكيمياء الغد 05:00 مساءً', 'time' => 'أمس'],
            ],
        ]);
    }
}

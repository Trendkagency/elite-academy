<?php

namespace App\Services\Notification;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ExceptionRequest;
use App\Models\FcmToken;
use App\Models\LiveSession;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    /**
     * Register or update an FCM token for a user.
     */
    public function registerToken(User $user, string $token, string $deviceType = 'web'): FcmToken
    {
        Log::info("[FCM TOKEN REGISTERED] User #{$user->id} ({$user->name}) Token: {$token}");

        return FcmToken::updateOrCreate(
            ['token' => $token],
            [
                'user_id'     => $user->id,
                'device_type' => $deviceType,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Store a UserNotification record and dispatch FCM push to all user devices.
     */
    public function sendNotification(User $user, string $type, string $title, string $body, ?string $actionUrl = null): UserNotification
    {
        $notification = UserNotification::create([
            'user_id'    => $user->id,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'action_url' => $actionUrl ?: route('student-portal'),
            'is_read'    => false,
        ]);

        $tokens = FcmToken::where('user_id', $user->id)->pluck('token')->toArray();

        Log::info("=================================================================");
        Log::info("[FCM NOTIFICATION SENDING] User #{$user->id} ({$user->email})", [
            'user_id'        => $user->id,
            'user_name'      => $user->name,
            'title'          => $title,
            'body'           => $body,
            'fcm_token_count'=> count($tokens),
            'fcm_tokens'     => $tokens,
        ]);
        Log::info("=================================================================");

        if (! empty($tokens)) {
            $this->dispatchFcmPayload($tokens, $title, $body, $actionUrl);
        }

        return $notification;
    }

    /**
     * Broadcast FCM push notification to a target audience.
     * Target: 'all' | 'students' | 'teachers' | 'parents'
     */
    public function broadcastNotification(string $targetAudience, string $title, string $body, ?string $actionUrl = null): int
    {
        $query = User::query();

        match ($targetAudience) {
            'students' => $query->whereHas('studentProfile'),
            'teachers' => $query->whereHas('teacherProfile'),
            'parents'  => $query->whereHas('parentProfile'),
            default    => null,
        };

        $dispatchedCount = 0;

        $query->each(function (User $user) use ($title, $body, $actionUrl, &$dispatchedCount) {
            $this->sendNotification($user, 'BROADCAST_ALERT', $title, $body, $actionUrl);
            $dispatchedCount++;
        });

        return $dispatchedCount;
    }

    /**
     * 1. قبول الحساب
     */
    public function notifyAccountApproved(User $user): UserNotification
    {
        $title = app()->getLocale() === 'ar'
            ? '🎉 تم قبول تفعيل حسابك'
            : '🎉 Account Approved';

        $body = app()->getLocale() === 'ar'
            ? "مرحباً بك {$user->name}! تم قبول وتفعيل حسابك بنجاح في المنصة، يمكنك الآن الوصول إلى كورساتك وحصصك."
            : "Welcome {$user->name}! Your account has been approved and activated successfully. You can now access your courses and sessions.";

        return $this->sendNotification($user, 'ACCOUNT_APPROVED', $title, $body, route('student-portal'));
    }

    /**
     * 2. قرب موعد الحصة - scan sessions starting in 15-45 minutes
     */
    public function sendUpcomingSessionReminders(): int
    {
        $windowStart = now();
        $windowEnd   = now()->addMinutes(45);

        $sessions = LiveSession::whereIn('status', ['scheduled', 'link_visible'])
            ->whereNull('reminder_sent_at')
            ->where(function ($query) use ($windowStart, $windowEnd) {
                $query->whereBetween('start_at', [$windowStart, $windowEnd])
                      ->orWhereBetween('scheduled_at', [$windowStart, $windowEnd]);
            })
            ->with(['student', 'subject'])
            ->get();

        $count = 0;
        foreach ($sessions as $session) {
            if ($session->student || $session->teacherProfile) {
                $this->notifyUpcomingSession($session);
                $this->notifyTeacherUpcomingSession($session);
                $session->update(['reminder_sent_at' => now()]);
                $count++;
            }
        }

        return $count;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Teacher Notification Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * 1a. إشعار المعلم بتكليف/جدولة حصة جديدة
     */
    public function notifyTeacherSessionAssigned(LiveSession $session): ?UserNotification
    {
        $teacherUser = $session->teacherProfile?->user;
        if (! $teacherUser) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');
        $timeStr     = $session->effective_start_at ? $session->effective_start_at->format('Y-m-d H:i') : '';

        $title = app()->getLocale() === 'ar'
            ? '📅 جدولة حصة جديدة'
            : '📅 New Session Scheduled';

        $body = app()->getLocale() === 'ar'
            ? "تم جدولة حصة جديدة لك لمادة ({$subjectName}) بتاريخ {$timeStr}."
            : "A new session ({$subjectName}) has been scheduled for you at {$timeStr}.";

        return $this->sendNotification($teacherUser, 'TEACHER_SESSION_ASSIGNED', $title, $body, route('teachers'));
    }

    /**
     * 1b. إشعار المعلم بقرب موعد الحصة
     */
    public function notifyTeacherUpcomingSession(LiveSession $session): ?UserNotification
    {
        $teacherUser = $session->teacherProfile?->user;
        if (! $teacherUser) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');
        $timeStr     = $session->effective_start_at ? $session->effective_start_at->format('H:i') : '';

        $title = app()->getLocale() === 'ar'
            ? '⏰ قرب موعد حصتك'
            : '⏰ Upcoming Teaching Session';

        $body = app()->getLocale() === 'ar'
            ? "تذكير: حصتك القادمة ({$subjectName}) ستبدأ قريباً في تمام الساعة {$timeStr}."
            : "Reminder: Your upcoming session ({$subjectName}) is starting soon at {$timeStr}.";

        return $this->sendNotification($teacherUser, 'TEACHER_SESSION_UPCOMING', $title, $body, route('teachers'));
    }

    /**
     * 1c. إشعار المعلم بفتح الحصة
     */
    public function notifyTeacherSessionOpened(LiveSession $session): ?UserNotification
    {
        $teacherUser = $session->teacherProfile?->user;
        if (! $teacherUser) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');

        $title = app()->getLocale() === 'ar'
            ? '🟢 تم فتح الحصة المباشرة'
            : '🟢 Live Session Started';

        $body = app()->getLocale() === 'ar'
            ? "بدأت الآن حصة ({$subjectName}). يمكنك إدارة البث المباشر ومتابعة الطلاب."
            : "Session ({$subjectName}) is live. You can now manage the stream.";

        return $this->sendNotification($teacherUser, 'TEACHER_SESSION_OPENED', $title, $body, route('teachers'));
    }

    /**
     * 2. إشعار المعلم بتسليم واجب من طالب
     */
    public function notifyTeacherAssignmentSubmitted(AssignmentSubmission $submission): ?UserNotification
    {
        $assignment = $submission->assignment;
        $teacherUser = $assignment?->teacherProfile?->user ?: $submission->liveSession?->teacherProfile?->user;
        if (! $teacherUser) return null;

        $studentName     = $submission->studentUser?->name ?: 'طالب';
        $assignmentTitle = $assignment?->title ?: 'الواجب الأكاديمي';

        $title = app()->getLocale() === 'ar'
            ? '📝 تسليم واجب جديد'
            : '📝 Homework Submitted';

        $body = app()->getLocale() === 'ar'
            ? "قام الطالب ({$studentName}) بتسليم إجابات واجب ({$assignmentTitle})."
            : "Student ({$studentName}) submitted answers for assignment ({$assignmentTitle}).";

        return $this->sendNotification($teacherUser, 'TEACHER_ASSIGNMENT_SUBMITTED', $title, $body, route('teachers'));
    }

    /**
     * 3. إشعار المعلم بتقديم طلب استثناء من طالب
     */
    public function notifyTeacherExceptionRequested(ExceptionRequest $request): ?UserNotification
    {
        $session = $request->liveSession;
        $teacherUser = $session?->teacherProfile?->user ?: $request->course?->teacherProfile?->user;
        if (! $teacherUser) return null;

        $studentName = $request->studentUser?->name ?: 'طالب';
        $scopeName   = $request->is_global || $request->scope === 'global' ? 'استثناء عام' : 'عذر حصة';

        $title = app()->getLocale() === 'ar'
            ? '📩 طلب استثناء جديد'
            : '📩 New Exception Request';

        $body = app()->getLocale() === 'ar'
            ? "قدّم الطالب ({$studentName}) طلب ({$scopeName}) جديد. السبب: " . ($request->reason ?: 'بدون سبب مذكور')
            : "Student ({$studentName}) submitted a new ({$scopeName}) request. Reason: " . ($request->reason ?: 'None specified');

        return $this->sendNotification($teacherUser, 'TEACHER_EXCEPTION_REQUESTED', $title, $body, route('teachers'));
    }

    /**
     * 4. إشعار المعلم بغياب طالب عن الحصة
     */
    public function notifyTeacherStudentAbsent(LiveSession $session, User $student): ?UserNotification
    {
        $teacherUser = $session->teacherProfile?->user;
        if (! $teacherUser) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');

        $title = app()->getLocale() === 'ar'
            ? '⚠️ تسجيل غياب طالب'
            : '⚠️ Student Absence Alert';

        $body = app()->getLocale() === 'ar'
            ? "تم تسجيل غياب الطالب ({$student->name}) عن حصة ({$subjectName})."
            : "Student ({$student->name}) was marked absent for session ({$subjectName}).";

        return $this->sendNotification($teacherUser, 'TEACHER_STUDENT_ABSENT', $title, $body, route('teachers'));
    }

    /**
     * 5a. إشعار المعلم بتغيير موعد الحصة
     */
    public function notifyTeacherSessionRescheduled(LiveSession $session): ?UserNotification
    {
        $teacherUser = $session->teacherProfile?->user;
        if (! $teacherUser) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');
        $newTimeStr  = $session->effective_start_at ? $session->effective_start_at->format('Y-m-d H:i') : 'الموعد الجديد';

        $title = app()->getLocale() === 'ar'
            ? '🗓️ تغيير موعد الحصة'
            : '🗓️ Teaching Session Rescheduled';

        $body = app()->getLocale() === 'ar'
            ? "تم تعديل موعد حصة ({$subjectName}) إلى: {$newTimeStr}."
            : "Session ({$subjectName}) has been rescheduled to: {$newTimeStr}.";

        return $this->sendNotification($teacherUser, 'TEACHER_SESSION_RESCHEDULED', $title, $body, route('teachers'));
    }

    /**
     * 5b. إشعار المعلم بإلغاء الحصة
     */
    public function notifyTeacherSessionCancelled(LiveSession $session): ?UserNotification
    {
        $teacherUser = $session->teacherProfile?->user;
        if (! $teacherUser) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');
        $reason      = $session->cancellation_reason ? " (السبب: {$session->cancellation_reason})" : '';

        $title = app()->getLocale() === 'ar'
            ? '❌ إلغاء الحصة'
            : '❌ Teaching Session Cancelled';

        $body = app()->getLocale() === 'ar'
            ? "تنويه: تم إلغاء حصة ({$subjectName}){$reason}."
            : "Notice: Your session ({$subjectName}) was cancelled{$reason}.";

        return $this->sendNotification($teacherUser, 'TEACHER_SESSION_CANCELLED', $title, $body, route('teachers'));
    }

    public function notifyUpcomingSession(LiveSession $session): ?UserNotification
    {
        $student = $session->studentUser ?: $session->student;
        if (! $student) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');
        $timeStr = $session->effective_start_at ? $session->effective_start_at->format('H:i') : '';

        $title = app()->getLocale() === 'ar'
            ? '⏰ قرب موعد الحصة'
            : '⏰ Upcoming Live Session';

        $body = app()->getLocale() === 'ar'
            ? "تذكير: حصتك ({$subjectName}) ستبدأ قريباً في تمام الساعة {$timeStr}."
            : "Reminder: Your session ({$subjectName}) is starting soon at {$timeStr}.";

        return $this->sendNotification($student, 'SESSION_UPCOMING', $title, $body, route('student-portal'));
    }

    /**
     * 3. فتح الحصة
     */
    public function notifySessionOpened(LiveSession $session): ?UserNotification
    {
        $student = $session->studentUser ?: $session->student;
        if (! $student) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');

        $title = app()->getLocale() === 'ar'
            ? '🟢 تم فتح الحصة الان'
            : '🟢 Live Session Started';

        $body = app()->getLocale() === 'ar'
            ? "بدأت الآن حصة ({$subjectName})! اضغط هنا للانضمام إلى البث المباشر."
            : "Session ({$subjectName}) has started! Click here to join the live stream.";

        return $this->sendNotification($student, 'SESSION_OPENED', $title, $body, route('student-portal'));
    }

    /**
     * 4. إضافة واجب
     */
    public function notifyAssignmentAdded(Assignment $assignment): int
    {
        $dispatchedCount = 0;

        $session = $assignment->liveSession ?: ($assignment->live_session_id ? LiveSession::find($assignment->live_session_id) : null);
        if ($session && $session->student) {
            $students = collect([$session->student]);
        } elseif ($assignment->course_id) {
            $students = User::whereHas('studentProfile')->get();
        } else {
            $students = collect();
        }

        $title = app()->getLocale() === 'ar'
            ? '📝 تم إضافة واجب جديد'
            : '📝 New Assignment Added';

        $body = app()->getLocale() === 'ar'
            ? "تم إضافة واجب جديد: ({$assignment->title}). يرجى الدخول والمبادرة بالحل."
            : "A new assignment ({$assignment->title}) has been assigned. Please submit your answers.";

        $actionUrl = route('student.assignment.take', ['id' => $assignment->id]);

        foreach ($students as $student) {
            $this->sendNotification($student, 'ASSIGNMENT_ADDED', $title, $body, $actionUrl);
            $dispatchedCount++;
        }

        return $dispatchedCount;
    }

    /**
     * 5b. إشعار الطالب بتقييم الواجب ورصد الدرجة
     */
    public function notifyStudentSubmissionGraded(AssignmentSubmission $submission): ?UserNotification
    {
        $student = $submission->studentUser ?: User::find($submission->student_user_id);
        if (! $student) return null;

        $assignmentTitle = $submission->assignment?->title ?: 'الواجب';
        $score = $submission->score !== null ? number_format($submission->score, 1) : '';

        $title = app()->getLocale() === 'ar'
            ? '📊 تم رصد درجة الواجب'
            : '📊 Assignment Graded';

        $body = app()->getLocale() === 'ar'
            ? "تم تقييم إجاباتك في واجب ({$assignmentTitle}) وحصلت على درجة ({$score})."
            : "Your submission for ({$assignmentTitle}) has been evaluated. Score: ({$score}).";

        return $this->sendNotification($student, 'SUBMISSION_GRADED', $title, $body, route('student-portal'));
    }

    /**
     * 5. عدم تسليم الواجب
     */
    public function notifyAssignmentOverdue(Assignment $assignment, User $student): UserNotification
    {
        $title = app()->getLocale() === 'ar'
            ? '⚠️ تنبيه: عدم تسليم الواجب'
            : '⚠️ Overdue Assignment Alert';

        $body = app()->getLocale() === 'ar'
            ? "لم تقم بتسليم واجب ({$assignment->title}). يرجى تسليمه في أقرب وقت لتجنب حظر دخول الحصص القادمة."
            : "You have not submitted the assignment ({$assignment->title}). Please submit it as soon as possible.";

        return $this->sendNotification(
            $student,
            'ASSIGNMENT_DEADLINE_REMINDER',
            $title,
            $body,
            route('student.assignment.take', ['id' => $assignment->id])
        );
    }

    /**
     * 6. قفل الحصة
     */
    public function notifySessionClosed(LiveSession $session): ?UserNotification
    {
        $student = $session->studentUser ?: $session->student;
        if (! $student) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');

        $title = app()->getLocale() === 'ar'
            ? '⏹️ تم إغلاق الحصة'
            : '⏹️ Session Ended';

        $body = app()->getLocale() === 'ar'
            ? "انتهت حصة ({$subjectName}). شكراً لحضورك ويمكنك الاطلاع على تسجيل الحصة إذا كان متاحاً."
            : "Session ({$subjectName}) has ended. Thank you for participating.";

        return $this->sendNotification($student, 'SESSION_CLOSED', $title, $body, route('student-portal'));
    }

    /**
     * 7. قبول أو رفض الاستثناء
     */
    public function notifyExceptionStatus(ExceptionRequest $request): ?UserNotification
    {
        $student = $request->studentUser ?: User::find($request->student_user_id);
        if (! $student) return null;

        $isApproved = ($request->status === 'approved');
        $scopeName = $request->is_global || $request->scope === 'global' ? 'طلب الاستثناء العام' : 'طلب استثناء الحصة';

        $title = $isApproved
            ? (app()->getLocale() === 'ar' ? '✅ تم قبول طلب الاستثناء' : '✅ Exception Request Approved')
            : (app()->getLocale() === 'ar' ? '❌ تم رفض طلب الاستثناء' : '❌ Exception Request Rejected');

        $body = $isApproved
            ? (app()->getLocale() === 'ar'
                ? "تمت الموافقة على ({$scopeName}) الخاص بك بنجاح. يمكنك الآن الانضمام للبث."
                : "Your exception request ({$scopeName}) has been approved.")
            : (app()->getLocale() === 'ar'
                ? "عذراً، تم رفض ({$scopeName}) الخاص بك. السبب: " . ($request->reason ?: 'عدم استيفاء الشروط')
                : "Your exception request ({$scopeName}) was rejected. Reason: " . ($request->reason ?: 'Requirements not met'));

        $type = $isApproved ? 'EXCEPTION_APPROVED' : 'EXCEPTION_REJECTED';

        return $this->sendNotification($student, $type, $title, $body, route('student-portal'));
    }

    /**
     * 8. إلغاء الحصة
     */
    public function notifySessionCancelled(LiveSession $session): ?UserNotification
    {
        $student = $session->studentUser ?: $session->student;
        if (! $student) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');

        $title = app()->getLocale() === 'ar'
            ? '❌ تم إلغاء الحصة'
            : '❌ Session Cancelled';

        $reason = $session->cancellation_reason ? " (السبب: {$session->cancellation_reason})" : '';

        $body = app()->getLocale() === 'ar'
            ? "تنويه: تم إلغاء حصة ({$subjectName}){$reason}."
            : "Notice: Your session ({$subjectName}) has been cancelled{$reason}.";

        return $this->sendNotification($student, 'SESSION_CANCELLED', $title, $body, route('student-portal'));
    }

    /**
     * 9. تغيير موعد الحصة
     */
    public function notifySessionRescheduled(LiveSession $session): ?UserNotification
    {
        $student = $session->studentUser ?: $session->student;
        if (! $student) return null;

        $subjectName = $session->subject?->name ?: ($session->title ?: 'الحصة التفاعلية');
        $newTimeStr  = $session->effective_start_at ? $session->effective_start_at->format('Y-m-d H:i') : 'الموعد الجديد';

        $title = app()->getLocale() === 'ar'
            ? '🗓️ تم تغيير موعد الحصة'
            : '🗓️ Session Rescheduled';

        $body = app()->getLocale() === 'ar'
            ? "تم تغيير موعد حصة ({$subjectName}) إلى: {$newTimeStr}."
            : "Session ({$subjectName}) has been rescheduled to: {$newTimeStr}.";

        return $this->sendNotification($student, 'SESSION_RESCHEDULED', $title, $body, route('student-portal'));
    }

    /**
     * Scan sessions starting in ~24h and remind students with unsubmitted assignments.
     */
    public function sendAssignmentDeadlineReminders(): int
    {
        $windowStart = now()->addHours(23);
        $windowEnd   = now()->addHours(25);

        $sessions = LiveSession::where('status', 'scheduled')
            ->where(function ($query) use ($windowStart, $windowEnd) {
                $query->whereBetween('start_at', [$windowStart, $windowEnd])
                      ->orWhereBetween('scheduled_at', [$windowStart, $windowEnd]);
            })
            ->with(['assignments', 'student'])
            ->get();

        $reminderCount = 0;

        foreach ($sessions as $session) {
            $student = $session->student;
            if (! $student) continue;

            foreach ($session->assignments as $assignment) {
                $hasSubmitted = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_user_id', $student->id)
                    ->exists();

                if (! $hasSubmitted) {
                    $this->notifyAssignmentOverdue($assignment, $student);
                    $reminderCount++;
                }
            }
        }

        return $reminderCount;
    }

    /**
     * Dispatch notification when admin approves a student request.
     */
    public function notifyAdminApproval(User $user, string $requestType, string $details = ''): UserNotification
    {
        $title = app()->getLocale() === 'ar'
            ? '✅ تم اعتماد طلبك من قبل الإدارة'
            : '✅ Request Approved by Administration';

        $body = app()->getLocale() === 'ar'
            ? "تمت الموافقة على طلب ({$requestType}) الخاص بك بنجاح. يمكنك الآن المتابعة في Portal."
            : "Your ({$requestType}) request has been approved by the admin team. Details: {$details}";

        return $this->sendNotification($user, 'ADMIN_APPROVAL_ALERT', $title, $body, route('student-portal'));
    }

    /**
     * 10. إشعار إضافة ملاحظة تربوية / توجيه أكاديمي من المعلم للطالب
     */
    public function notifyStudentEducationalNote(\App\Models\StudentEducationalNote $note): ?UserNotification
    {
        $studentUser = $note->studentUser ?: User::find($note->student_user_id);
        if (! $studentUser) {
            return null;
        }

        $teacherName = $note->teacherProfile?->user?->name ?: __('Academic Teacher');
        $categoryLabel = match($note->category) {
            'academic' => app()->getLocale() === 'ar' ? 'أكاديمية' : 'Academic',
            'homework' => app()->getLocale() === 'ar' ? 'حول الواجبات' : 'Homework',
            'participation' => app()->getLocale() === 'ar' ? 'حول المشاركة والتفاعل' : 'Participation',
            'behavior' => app()->getLocale() === 'ar' ? 'حول السلوك والانضباط' : 'Behavior',
            default => app()->getLocale() === 'ar' ? 'توجيهية عامة' : 'General Guidance',
        };

        $title = app()->getLocale() === 'ar'
            ? "💬 ملاحظة جديدة من المعلم ({$teacherName})"
            : "💬 New Teacher Note from ({$teacherName})";

        $body = app()->getLocale() === 'ar'
            ? "أضاف المعلم {$teacherName} ملاحظة {$categoryLabel}: \"{$note->note}\""
            : "Teacher {$teacherName} added a {$categoryLabel} note: \"{$note->note}\"";

        return $this->sendNotification(
            $studentUser,
            'TEACHER_NOTE_ADDED',
            $title,
            $body,
            route('student-portal') . '#teacher-notes'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal Dispatch Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Route to the correct FCM API based on FCM_MODE config.
     */
    protected function dispatchFcmPayload(array $tokens, string $title, string $body, ?string $actionUrl = null): void
    {
        $mode = config('fcm.mode', 'legacy');

        if ($mode === 'v1') {
            $this->dispatchV1($tokens, $title, $body, $actionUrl);
        } else {
            $this->dispatchLegacy($tokens, $title, $body, $actionUrl);
        }
    }

    /**
     * FCM Legacy HTTP API (v0) — Server Key based.
     * Batches tokens in chunks of config('fcm.batch_size').
     */
    protected function dispatchLegacy(array $tokens, string $title, string $body, ?string $actionUrl = null): void
    {
        $serverKey = config('fcm.legacy.key');

        if (! $serverKey) {
            $this->logFallback('legacy', $title, $body, count($tokens));
            return;
        }

        $defaults   = config('fcm.defaults');
        $android    = config('fcm.android');
        $batchSize  = config('fcm.batch_size', 500);
        $clickUrl   = $actionUrl ?: $defaults['click_action'];
        $endpoint   = config('fcm.legacy.endpoint', 'https://fcm.googleapis.com/fcm/send');

        foreach (array_chunk($tokens, $batchSize) as $chunk) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $serverKey,
                    'Content-Type'  => 'application/json',
                ])->post($endpoint, [
                    'registration_ids' => $chunk,
                    'notification' => [
                        'title'        => $title,
                        'body'         => $body,
                        'icon'         => $defaults['icon'],
                        'color'        => $defaults['color'],
                        'sound'        => $defaults['sound'],
                        'click_action' => $clickUrl,
                        'badge'        => $defaults['badge'],
                    ],
                    'data' => [
                        'title' => $title,
                        'body'  => $body,
                        'url'   => $clickUrl,
                    ],
                    'android' => [
                        'priority' => $android['priority'],
                        'ttl'      => $android['ttl'] . 's',
                        'notification' => [
                            'channel_id' => $android['channel_id'],
                            'sound'      => $defaults['sound'],
                            'color'      => $defaults['color'],
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => config('fcm.apns.sound', 'default'),
                                'badge' => config('fcm.apns.badge', 1),
                            ],
                        ],
                    ],
                ]);

                if (config('fcm.logging.enabled') && $response->failed()) {
                    Log::channel(config('fcm.logging.channel'))->warning('FCM Legacy: Push batch failed', [
                        'status'  => $response->status(),
                        'body'    => $response->body(),
                        'tokens'  => count($chunk),
                    ]);
                }

            } catch (\Throwable $e) {
                if (config('fcm.logging.on_error')) {
                    Log::channel(config('fcm.logging.channel'))->error('FCM Legacy dispatch error: ' . $e->getMessage(), [
                        'title'  => $title,
                        'tokens' => count($chunk),
                    ]);
                }
            }
        }
    }

    /**
     * FCM HTTP v1 API — OAuth2 Service Account based (recommended).
     * Sends one message at a time (v1 does not support multi-cast natively).
     */
    protected function dispatchV1(array $tokens, string $title, string $body, ?string $actionUrl = null): void
    {
        $projectId          = config('fcm.v1.project_id');
        $serviceAccountPath = config('fcm.v1.service_account_path');

        if ($serviceAccountPath && ! file_exists($serviceAccountPath) && file_exists(base_path($serviceAccountPath))) {
            $serviceAccountPath = base_path($serviceAccountPath);
        }

        if (! $projectId || ! $serviceAccountPath || ! file_exists($serviceAccountPath)) {
            $this->logFallback('v1', $title, $body, count($tokens));
            return;
        }

        $accessToken = $this->getV1AccessToken($serviceAccountPath);
        if (! $accessToken) {
            Log::channel(config('fcm.logging.channel'))->error('FCM v1: Failed to obtain OAuth2 access token.');
            return;
        }

        $defaults  = config('fcm.defaults');
        $android   = config('fcm.android');
        $endpoint  = sprintf(config('fcm.v1.endpoint'), $projectId);
        $clickUrl  = $actionUrl ?: $defaults['click_action'];

        foreach ($tokens as $token) {
            try {
                $response = Http::withToken($accessToken)
                    ->post($endpoint, [
                        'message' => [
                            'token'        => $token,
                            'notification' => [
                                'title' => $title,
                                'body'  => $body,
                                'image' => $defaults['icon'],
                            ],
                            'android' => [
                                'priority' => strtoupper($android['priority']),
                                'ttl'      => $android['ttl'] . 's',
                                'notification' => [
                                    'channel_id' => $android['channel_id'],
                                    'sound'      => $defaults['sound'],
                                    'color'      => $defaults['color'],
                                    'click_action' => $clickUrl,
                                ],
                            ],
                            'apns' => [
                                'payload' => [
                                    'aps' => [
                                        'sound' => config('fcm.apns.sound', 'default'),
                                        'badge' => config('fcm.apns.badge', 1),
                                    ],
                                ],
                            ],
                            'webpush' => [
                                'notification' => [
                                    'title' => $title,
                                    'body'  => $body,
                                    'icon'  => $defaults['icon'],
                                ],
                                'fcm_options' => [
                                    'link' => $clickUrl,
                                ],
                            ],
                            'data' => [
                                'url'  => $clickUrl,
                                'type' => 'push',
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    Log::channel(config('fcm.logging.channel'))->info("✅ [FCM v1 SUCCESS] Target Token: {$token}", [
                        'status' => $response->status(),
                        'response' => $response->json(),
                    ]);
                } else {
                    Log::channel(config('fcm.logging.channel'))->error("❌ [FCM v1 RESPONSE ERROR] Target Token: {$token}", [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }

            } catch (\Throwable $e) {
                if (config('fcm.logging.on_error')) {
                    Log::channel(config('fcm.logging.channel'))->error('FCM v1 dispatch error: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Obtain a short-lived OAuth2 access token from a Firebase service account JSON file.
     */
    protected function getV1AccessToken(string $serviceAccountPath): ?string
    {
        try {
            $credentials = json_decode(file_get_contents($serviceAccountPath), true);

            $now = time();
            $header  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            $signature = '';
            openssl_sign("{$header}.{$payload}", $signature, $credentials['private_key'], 'SHA256');
            $jwt = "{$header}.{$payload}." . base64_encode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            return $response->json('access_token');

        } catch (\Throwable $e) {
            Log::error('FCM v1 OAuth token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log a fallback message when FCM credentials are not configured.
     */
    protected function logFallback(string $mode, string $title, string $body, int $tokenCount): void
    {
        if (config('fcm.logging.enabled')) {
            Log::channel(config('fcm.logging.channel'))->info("FCM [{$mode}] not configured — notification logged only", [
                'title'  => $title,
                'body'   => $body,
                'tokens' => $tokenCount,
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\RecurringSchedule;
use App\Models\StudentPackage;
use App\Models\User;
use App\Services\Notification\FcmNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendCollectionRemindersCommand extends Command
{
    protected $signature = 'collections:send-reminders {--dry-run : Simulate notifications without dispatching}';

    protected $description = 'Scan exhausted/low balance student packages and expiring recurring schedules to dispatch automated reminders to students and parents';

    public function handle(FcmNotificationService $fcmService): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $now = Carbon::now();

        $this->info("=================================================================");
        $this->info("   ELITE ACADEMY — AUTOMATED COLLECTIONS & RENEWALS REMINDERS");
        $this->info("=================================================================");
        if ($isDryRun) {
            $this->warn("MODE: DRY RUN (Simulating notifications without dispatching)");
        }

        // ── 1. SCAN EXHAUSTED & LOW BALANCE PACKAGES ──────────────────────────
        $packages = StudentPackage::with(['studentUser', 'course'])
            ->whereIn('status', ['active', 'exhausted'])
            ->where(function ($q) use ($now) {
                $q->where('remaining_sessions', '<=', 3)
                  ->orWhere(function ($eq) use ($now) {
                      $eq->whereNotNull('expires_at')->where('expires_at', '<=', $now->copy()->addDays(7));
                  });
            })
            ->get();

        $this->line("\n[1/2] Processing Package Payment & Balance Reminders (" . $packages->count() . " candidates)...");

        $pkgTableRows = [];
        $pkgDispatchedCount = 0;

        foreach ($packages as $pkg) {
            $student = $pkg->studentUser;
            if (! $student) continue;

            $remaining = $pkg->remaining_sessions;
            $urgency = $remaining <= 0 ? 'حرج (0 حصة)' : ($remaining <= 3 ? 'منخفض (1-3)' : 'صلاحية قريبة');

            // Find parent
            $parent = DB::table('parent_student')
                ->join('users', 'users.id', '=', 'parent_student.parent_user_id')
                ->where('parent_student.student_user_id', $student->id)
                ->select('users.id', 'users.name')
                ->first();

            $parentName = $parent ? $parent->name : 'لا يوجد';

            $title = 'تنبيه الأكاديمية: تجديد رصيد الباقة التعليمية';
            $studentBody = "مرحباً {$student->name}، رصيد باقتك الحالية متبقي به ({$remaining}) حصص فقط. يرجى مراجعة إدارة الأكاديمية للتجديد وسداد الاشتراك لضمان استمرار حضور الحصص.";
            $parentBody = "تنبيه لولي الأمر: رصيد باقة الطالب {$student->name} أوشك على الانتهاء ({$remaining} حصص متبقية). يرجى التجديد لضمان استمرار الجدول الدراسي.";

            if (! $isDryRun) {
                // Dispatch to student
                $fcmService->sendNotification($student, 'package_reminder', $title, $studentBody, '/student-portal');
                $pkgDispatchedCount++;

                // Dispatch to parent if exists
                if ($parent) {
                    $parentUser = User::find($parent->id);
                    if ($parentUser) {
                        $fcmService->sendNotification($parentUser, 'package_reminder', $title, $parentBody, '/parent-portal');
                        $pkgDispatchedCount++;
                    }
                }
            }

            $pkgTableRows[] = [
                $student->name,
                $parentName,
                $pkg->course?->title ?? 'باقة عامة',
                $remaining . ' حصة',
                $pkg->expires_at ? $pkg->expires_at->format('Y-m-d') : 'بدون انتهاء',
                $urgency,
            ];
        }

        $this->table(
            ['الطالب', 'ولي الأمر', 'المقرر / الباقة', 'الرصيد المتبقي', 'تاريخ الصلاحية', 'الحالة'],
            $pkgTableRows
        );

        // ── 2. SCAN EXPIRING RECURRING SCHEDULES ──────────────────────────────
        $schedules = RecurringSchedule::with(['student', 'teacher.user', 'course'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $now->copy()->addDays(14))
            ->get();

        $this->line("\n[2/2] Processing Recurring Schedule Renewal Reminders (" . $schedules->count() . " candidates)...");

        $schTableRows = [];
        $schDispatchedCount = 0;

        foreach ($schedules as $sch) {
            $student = $sch->student;
            $studentName = $student ? $student->name : 'جدول جماعي';
            $endDateStr = $sch->end_date ? Carbon::parse($sch->end_date)->format('Y-m-d') : '';
            $isEnded = $sch->end_date && Carbon::parse($sch->end_date)->isPast();
            $statusLabel = $isEnded ? 'الدورة انتهت بالفعل' : 'تنتهي خلال ' . round($now->diffInDays($sch->end_date, false)) . ' يوم';

            $title = 'تنبيه: اقتراب انتهاء دورة الجدول الدراسي وتجديد الاشتراك';
            $studentBody = "مرحباً {$studentName}، نود تذكيركم باقتراب موعد نهاية دورة الجدول الدراسي ({$sch->title}) في تاريخ {$endDateStr}. يرجى التواصل مع الأكاديمية لتأكيد تجديد الاشتراك وتوليد حصص الشهر الجديد.";

            if (! $isDryRun && $student) {
                // Dispatch to student
                $fcmService->sendNotification($student, 'schedule_reminder', $title, $studentBody, '/student-portal');
                $schDispatchedCount++;

                // Dispatch to parent if exists
                $parent = DB::table('parent_student')
                    ->join('users', 'users.id', '=', 'parent_student.parent_user_id')
                    ->where('parent_student.student_user_id', $student->id)
                    ->select('users.id')
                    ->first();

                if ($parent) {
                    $parentUser = User::find($parent->id);
                    if ($parentUser) {
                        $parentBody = "تنبيه لولي الأمر: تقترب دورة جدول الطالب {$studentName} ({$sch->title}) من الانتهاء في {$endDateStr}. يرجى مراجعة إدارة الأكاديمية لتجديد الاشتراك الشهري.";
                        $fcmService->sendNotification($parentUser, 'schedule_reminder', $title, $parentBody, '/parent-portal');
                        $schDispatchedCount++;
                    }
                }
            }

            $schTableRows[] = [
                $sch->title,
                $studentName,
                $sch->teacher?->user?->name ?? 'غير محدد',
                $endDateStr,
                $statusLabel,
            ];
        }

        $this->table(
            ['عنوان الجدول الدوري', 'الطالب', 'المعلم', 'تاريخ نهاية الدورة', 'الحالة الحالية'],
            $schTableRows
        );

        $totalDispatched = $pkgDispatchedCount + $schDispatchedCount;
        $this->info("\n=================================================================");
        if ($isDryRun) {
            $this->info("DRY RUN FINISHED: All candidate records evaluated successfully.");
        } else {
            $this->info("COMPLETED SUCCESSFULLY! Dispatched {$totalDispatched} reminder notifications.");
            $this->line("• Package balance reminders: {$pkgDispatchedCount} notifications");
            $this->line("• Schedule renewal reminders: {$schDispatchedCount} notifications");
        }
        $this->info("=================================================================\n");

        return self::SUCCESS;
    }
}

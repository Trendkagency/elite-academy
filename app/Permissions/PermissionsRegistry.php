<?php

namespace App\Permissions;

use App\Enums\Role;

class PermissionsRegistry
{
    // 1. Dashboard & Core System
    public const DASHBOARD_ADMIN_VIEW   = 'dashboard.admin.view';
    public const DASHBOARD_TEACHER_VIEW = 'dashboard.teacher.view';
    public const DASHBOARD_STUDENT_VIEW = 'dashboard.student.view';
    public const DASHBOARD_PARENT_VIEW  = 'dashboard.parent.view';

    // 2. Student Management
    public const STUDENTS_VIEW      = 'students.view';
    public const STUDENTS_CREATE    = 'students.create';
    public const STUDENTS_UPDATE    = 'students.update';
    public const STUDENTS_DELETE    = 'students.delete';
    public const STUDENTS_VIEW_OWN  = 'students.view-own';

    // 3. Teacher Management
    public const TEACHERS_VIEW      = 'teachers.view';
    public const TEACHERS_CREATE    = 'teachers.create';
    public const TEACHERS_UPDATE    = 'teachers.update';
    public const TEACHERS_DELETE    = 'teachers.delete';

    // 4. Parent Management
    public const PARENTS_VIEW       = 'parents.view';
    public const PARENTS_CREATE     = 'parents.create';
    public const PARENTS_UPDATE     = 'parents.update';
    public const PARENTS_DELETE     = 'parents.delete';

    // 5. Sessions & Live Streams
    public const SESSIONS_VIEW               = 'sessions.view';
    public const SESSIONS_CREATE             = 'sessions.create';
    public const SESSIONS_UPDATE             = 'sessions.update';
    public const SESSIONS_DELETE             = 'sessions.delete';
    public const SESSIONS_CANCEL             = 'sessions.cancel';
    public const SESSIONS_RESCHEDULE         = 'sessions.reschedule';
    public const SESSIONS_LINK_MANAGE        = 'sessions.link.manage';
    public const SESSIONS_JOIN               = 'sessions.join';
    public const SESSIONS_VIEW_OWN_STUDENTS  = 'sessions.view-own-students';

    // 6. Assignments
    public const ASSIGNMENTS_VIEW               = 'assignments.view';
    public const ASSIGNMENTS_CREATE             = 'assignments.create';
    public const ASSIGNMENTS_UPDATE             = 'assignments.update';
    public const ASSIGNMENTS_DELETE             = 'assignments.delete';
    public const ASSIGNMENTS_ANSWER             = 'assignments.answer';
    public const ASSIGNMENTS_SUBMIT             = 'assignments.submit';
    public const ASSIGNMENTS_VIEW_OWN_STUDENTS  = 'assignments.view-own-students';

    // 7. Submissions & Grading
    public const SUBMISSIONS_VIEW               = 'submissions.view';
    public const SUBMISSIONS_REVIEW             = 'submissions.review';
    public const SUBMISSIONS_VIEW_OWN          = 'submissions.view-own';
    public const SUBMISSIONS_VIEW_OWN_STUDENTS  = 'submissions.view-own-students';

    // 8. Attendance Tracking
    public const ATTENDANCE_VIEW               = 'attendance.view';
    public const ATTENDANCE_MANAGE             = 'attendance.manage';
    public const ATTENDANCE_VIEW_OWN          = 'attendance.view-own';
    public const ATTENDANCE_VIEW_OWN_STUDENTS  = 'attendance.view-own-students';

    // 9. Notifications
    public const NOTIFICATIONS_VIEW       = 'notifications.view';
    public const NOTIFICATIONS_MANAGE     = 'notifications.manage';
    public const NOTIFICATIONS_MARK_READ  = 'notifications.mark-read';

    // 10. Profiles
    public const PROFILE_VIEW_OWN    = 'profile.view-own';
    public const PROFILE_UPDATE_OWN  = 'profile.update-own';

    // 11. Translation Management
    public const TRANSLATIONS_VIEW      = 'translations.view';
    public const TRANSLATIONS_CREATE    = 'translations.create';
    public const TRANSLATIONS_UPDATE    = 'translations.update';
    public const TRANSLATIONS_DELETE    = 'translations.delete';
    public const TRANSLATIONS_TRANSLATE = 'translations.translate';
    public const TRANSLATIONS_BULK      = 'translations.bulk';

    /**
     * Get list of all permissions defined in system.
     */
    public static function all(): array
    {
        return [
            self::DASHBOARD_ADMIN_VIEW,
            self::DASHBOARD_TEACHER_VIEW,
            self::DASHBOARD_STUDENT_VIEW,
            self::DASHBOARD_PARENT_VIEW,

            self::STUDENTS_VIEW,
            self::STUDENTS_CREATE,
            self::STUDENTS_UPDATE,
            self::STUDENTS_DELETE,
            self::STUDENTS_VIEW_OWN,

            self::TEACHERS_VIEW,
            self::TEACHERS_CREATE,
            self::TEACHERS_UPDATE,
            self::TEACHERS_DELETE,

            self::PARENTS_VIEW,
            self::PARENTS_CREATE,
            self::PARENTS_UPDATE,
            self::PARENTS_DELETE,

            self::SESSIONS_VIEW,
            self::SESSIONS_CREATE,
            self::SESSIONS_UPDATE,
            self::SESSIONS_DELETE,
            self::SESSIONS_CANCEL,
            self::SESSIONS_RESCHEDULE,
            self::SESSIONS_LINK_MANAGE,
            self::SESSIONS_JOIN,
            self::SESSIONS_VIEW_OWN_STUDENTS,

            self::ASSIGNMENTS_VIEW,
            self::ASSIGNMENTS_CREATE,
            self::ASSIGNMENTS_UPDATE,
            self::ASSIGNMENTS_DELETE,
            self::ASSIGNMENTS_ANSWER,
            self::ASSIGNMENTS_SUBMIT,
            self::ASSIGNMENTS_VIEW_OWN_STUDENTS,

            self::SUBMISSIONS_VIEW,
            self::SUBMISSIONS_REVIEW,
            self::SUBMISSIONS_VIEW_OWN,
            self::SUBMISSIONS_VIEW_OWN_STUDENTS,

            self::ATTENDANCE_VIEW,
            self::ATTENDANCE_MANAGE,
            self::ATTENDANCE_VIEW_OWN,
            self::ATTENDANCE_VIEW_OWN_STUDENTS,

            self::NOTIFICATIONS_VIEW,
            self::NOTIFICATIONS_MANAGE,
            self::NOTIFICATIONS_MARK_READ,

            self::PROFILE_VIEW_OWN,
            self::PROFILE_UPDATE_OWN,

            self::TRANSLATIONS_VIEW,
            self::TRANSLATIONS_CREATE,
            self::TRANSLATIONS_UPDATE,
            self::TRANSLATIONS_DELETE,
            self::TRANSLATIONS_TRANSLATE,
            self::TRANSLATIONS_BULK,
        ];
    }

    /**
     * Default permission matrix per Role.
     */
    public static function defaultPermissionsForRole(string $role): array
    {
        return match ($role) {
            Role::ADMIN->value => self::all(),
            Role::TEACHER->value => [
                self::DASHBOARD_TEACHER_VIEW,
                self::SESSIONS_VIEW,
                self::SESSIONS_CREATE,
                self::SESSIONS_UPDATE,
                self::SESSIONS_CANCEL,
                self::SESSIONS_RESCHEDULE,
                self::SESSIONS_LINK_MANAGE,
                self::STUDENTS_VIEW,
                self::ASSIGNMENTS_VIEW,
                self::ASSIGNMENTS_CREATE,
                self::ASSIGNMENTS_UPDATE,
                self::SUBMISSIONS_VIEW,
                self::SUBMISSIONS_REVIEW,
                self::ATTENDANCE_VIEW,
                self::ATTENDANCE_MANAGE,
                self::NOTIFICATIONS_VIEW,
                self::NOTIFICATIONS_MARK_READ,
                self::PROFILE_VIEW_OWN,
                self::PROFILE_UPDATE_OWN,
            ],
            Role::STUDENT->value => [
                self::DASHBOARD_STUDENT_VIEW,
                self::SESSIONS_VIEW,
                self::SESSIONS_JOIN,
                self::ASSIGNMENTS_VIEW,
                self::ASSIGNMENTS_ANSWER,
                self::ASSIGNMENTS_SUBMIT,
                self::SUBMISSIONS_VIEW_OWN,
                self::ATTENDANCE_VIEW_OWN,
                self::NOTIFICATIONS_VIEW,
                self::NOTIFICATIONS_MARK_READ,
                self::PROFILE_VIEW_OWN,
                self::PROFILE_UPDATE_OWN,
            ],
            Role::PARENT->value => [
                self::DASHBOARD_PARENT_VIEW,
                self::STUDENTS_VIEW_OWN,
                self::SESSIONS_VIEW_OWN_STUDENTS,
                self::ASSIGNMENTS_VIEW_OWN_STUDENTS,
                self::SUBMISSIONS_VIEW_OWN_STUDENTS,
                self::ATTENDANCE_VIEW_OWN_STUDENTS,
                self::NOTIFICATIONS_VIEW,
                self::NOTIFICATIONS_MARK_READ,
                self::PROFILE_VIEW_OWN,
                self::PROFILE_UPDATE_OWN,
            ],
            default => [],
        };
    }
}

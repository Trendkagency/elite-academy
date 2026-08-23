<?php

namespace App\Policies;

use App\Models\LiveSession;
use App\Models\User;
use App\Permissions\PermissionsRegistry;

class TeacherSessionPolicy
{
    public function view(User $user, LiveSession $session): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::SESSIONS_VIEW)) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $teacherProfile = $user->teacherProfile;
        if (! $teacherProfile) {
            return false;
        }

        return (int) $session->teacher_profile_id === (int) $teacherProfile->id;
    }

    public function update(User $user, LiveSession $session): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::SESSIONS_UPDATE)) {
            return false;
        }

        return $this->view($user, $session);
    }

    public function cancel(User $user, LiveSession $session): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::SESSIONS_CANCEL)) {
            return false;
        }

        return $this->view($user, $session);
    }

    public function reschedule(User $user, LiveSession $session): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::SESSIONS_RESCHEDULE)) {
            return false;
        }

        return $this->view($user, $session);
    }

    public function manageLink(User $user, LiveSession $session): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::SESSIONS_LINK_MANAGE)) {
            return false;
        }

        return $this->view($user, $session);
    }

    public function manageAttendance(User $user, LiveSession $session): bool
    {
        if (! $user->hasPermission(PermissionsRegistry::ATTENDANCE_MANAGE)) {
            return false;
        }

        return $this->view($user, $session);
    }
}

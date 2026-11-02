<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MeetingAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingAttendancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MeetingAttendance');
    }

    public function view(AuthUser $authUser, MeetingAttendance $meetingAttendance): bool
    {
        return $authUser->can('View:MeetingAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MeetingAttendance');
    }

    public function update(AuthUser $authUser, MeetingAttendance $meetingAttendance): bool
    {
        return $authUser->can('Update:MeetingAttendance');
    }

    public function delete(AuthUser $authUser, MeetingAttendance $meetingAttendance): bool
    {
        return $authUser->can('Delete:MeetingAttendance');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MeetingAttendance');
    }

    public function restore(AuthUser $authUser, MeetingAttendance $meetingAttendance): bool
    {
        return $authUser->can('Restore:MeetingAttendance');
    }

    public function forceDelete(AuthUser $authUser, MeetingAttendance $meetingAttendance): bool
    {
        return $authUser->can('ForceDelete:MeetingAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MeetingAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MeetingAttendance');
    }

    public function replicate(AuthUser $authUser, MeetingAttendance $meetingAttendance): bool
    {
        return $authUser->can('Replicate:MeetingAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MeetingAttendance');
    }

}
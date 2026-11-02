<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MeetingProvider;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingProviderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MeetingProvider');
    }

    public function view(AuthUser $authUser, MeetingProvider $meetingProvider): bool
    {
        return $authUser->can('View:MeetingProvider');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MeetingProvider');
    }

    public function update(AuthUser $authUser, MeetingProvider $meetingProvider): bool
    {
        return $authUser->can('Update:MeetingProvider');
    }

    public function delete(AuthUser $authUser, MeetingProvider $meetingProvider): bool
    {
        return $authUser->can('Delete:MeetingProvider');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MeetingProvider');
    }

    public function restore(AuthUser $authUser, MeetingProvider $meetingProvider): bool
    {
        return $authUser->can('Restore:MeetingProvider');
    }

    public function forceDelete(AuthUser $authUser, MeetingProvider $meetingProvider): bool
    {
        return $authUser->can('ForceDelete:MeetingProvider');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MeetingProvider');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MeetingProvider');
    }

    public function replicate(AuthUser $authUser, MeetingProvider $meetingProvider): bool
    {
        return $authUser->can('Replicate:MeetingProvider');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MeetingProvider');
    }

}
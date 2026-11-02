<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TeacherProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherProfilePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TeacherProfile');
    }

    public function view(AuthUser $authUser, TeacherProfile $teacherProfile): bool
    {
        return $authUser->can('View:TeacherProfile');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TeacherProfile');
    }

    public function update(AuthUser $authUser, TeacherProfile $teacherProfile): bool
    {
        return $authUser->can('Update:TeacherProfile');
    }

    public function delete(AuthUser $authUser, TeacherProfile $teacherProfile): bool
    {
        return $authUser->can('Delete:TeacherProfile');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TeacherProfile');
    }

    public function restore(AuthUser $authUser, TeacherProfile $teacherProfile): bool
    {
        return $authUser->can('Restore:TeacherProfile');
    }

    public function forceDelete(AuthUser $authUser, TeacherProfile $teacherProfile): bool
    {
        return $authUser->can('ForceDelete:TeacherProfile');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TeacherProfile');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TeacherProfile');
    }

    public function replicate(AuthUser $authUser, TeacherProfile $teacherProfile): bool
    {
        return $authUser->can('Replicate:TeacherProfile');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TeacherProfile');
    }

}
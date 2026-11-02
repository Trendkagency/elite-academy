<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ParentProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParentProfilePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParentProfile');
    }

    public function view(AuthUser $authUser, ParentProfile $parentProfile): bool
    {
        return $authUser->can('View:ParentProfile');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParentProfile');
    }

    public function update(AuthUser $authUser, ParentProfile $parentProfile): bool
    {
        return $authUser->can('Update:ParentProfile');
    }

    public function delete(AuthUser $authUser, ParentProfile $parentProfile): bool
    {
        return $authUser->can('Delete:ParentProfile');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParentProfile');
    }

    public function restore(AuthUser $authUser, ParentProfile $parentProfile): bool
    {
        return $authUser->can('Restore:ParentProfile');
    }

    public function forceDelete(AuthUser $authUser, ParentProfile $parentProfile): bool
    {
        return $authUser->can('ForceDelete:ParentProfile');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParentProfile');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParentProfile');
    }

    public function replicate(AuthUser $authUser, ParentProfile $parentProfile): bool
    {
        return $authUser->can('Replicate:ParentProfile');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParentProfile');
    }

}
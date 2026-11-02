<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StudentPackage;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPackagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentPackage');
    }

    public function view(AuthUser $authUser, StudentPackage $studentPackage): bool
    {
        return $authUser->can('View:StudentPackage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentPackage');
    }

    public function update(AuthUser $authUser, StudentPackage $studentPackage): bool
    {
        return $authUser->can('Update:StudentPackage');
    }

    public function delete(AuthUser $authUser, StudentPackage $studentPackage): bool
    {
        return $authUser->can('Delete:StudentPackage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudentPackage');
    }

    public function restore(AuthUser $authUser, StudentPackage $studentPackage): bool
    {
        return $authUser->can('Restore:StudentPackage');
    }

    public function forceDelete(AuthUser $authUser, StudentPackage $studentPackage): bool
    {
        return $authUser->can('ForceDelete:StudentPackage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentPackage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentPackage');
    }

    public function replicate(AuthUser $authUser, StudentPackage $studentPackage): bool
    {
        return $authUser->can('Replicate:StudentPackage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentPackage');
    }

}
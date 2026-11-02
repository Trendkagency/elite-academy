<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PackageTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackageTemplatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PackageTemplate');
    }

    public function view(AuthUser $authUser, PackageTemplate $packageTemplate): bool
    {
        return $authUser->can('View:PackageTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PackageTemplate');
    }

    public function update(AuthUser $authUser, PackageTemplate $packageTemplate): bool
    {
        return $authUser->can('Update:PackageTemplate');
    }

    public function delete(AuthUser $authUser, PackageTemplate $packageTemplate): bool
    {
        return $authUser->can('Delete:PackageTemplate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PackageTemplate');
    }

    public function restore(AuthUser $authUser, PackageTemplate $packageTemplate): bool
    {
        return $authUser->can('Restore:PackageTemplate');
    }

    public function forceDelete(AuthUser $authUser, PackageTemplate $packageTemplate): bool
    {
        return $authUser->can('ForceDelete:PackageTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PackageTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PackageTemplate');
    }

    public function replicate(AuthUser $authUser, PackageTemplate $packageTemplate): bool
    {
        return $authUser->can('Replicate:PackageTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PackageTemplate');
    }

}
<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RecurringSchedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringSchedulePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RecurringSchedule');
    }

    public function view(AuthUser $authUser, RecurringSchedule $recurringSchedule): bool
    {
        return $authUser->can('View:RecurringSchedule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RecurringSchedule');
    }

    public function update(AuthUser $authUser, RecurringSchedule $recurringSchedule): bool
    {
        return $authUser->can('Update:RecurringSchedule');
    }

    public function delete(AuthUser $authUser, RecurringSchedule $recurringSchedule): bool
    {
        return $authUser->can('Delete:RecurringSchedule');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RecurringSchedule');
    }

    public function restore(AuthUser $authUser, RecurringSchedule $recurringSchedule): bool
    {
        return $authUser->can('Restore:RecurringSchedule');
    }

    public function forceDelete(AuthUser $authUser, RecurringSchedule $recurringSchedule): bool
    {
        return $authUser->can('ForceDelete:RecurringSchedule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RecurringSchedule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RecurringSchedule');
    }

    public function replicate(AuthUser $authUser, RecurringSchedule $recurringSchedule): bool
    {
        return $authUser->can('Replicate:RecurringSchedule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RecurringSchedule');
    }

}
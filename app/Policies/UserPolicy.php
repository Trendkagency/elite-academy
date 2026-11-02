<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        // Trendk Standard 3.1: support@trendk.com cannot be viewed by other users
        if ($model->email === 'support@trendk.com' && $user->email !== 'support@trendk.com') {
            return false;
        }

        return $user->isAdmin() || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        // Trendk Standard 3.1: support@trendk.com cannot be edited by other users
        if ($model->email === 'support@trendk.com' && $user->email !== 'support@trendk.com') {
            return false;
        }

        return $user->isAdmin() || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        // Trendk Standard 3.1: support@trendk.com cannot be deleted by other users
        if ($model->email === 'support@trendk.com') {
            return false;
        }

        return $user->isAdmin();
    }
}

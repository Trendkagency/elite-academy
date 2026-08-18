<?php

namespace App\Policies;

use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LiveSessionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, LiveSession $session): bool
    {
        if ($user->isAdmin() || $user->isTeacher()) {
            return true;
        }

        return (int) $session->student_user_id === (int) $user->id;
    }

    public function join(User $user, LiveSession $session): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Student can only join their own assigned live session
        if ((int) $session->student_user_id !== (int) $user->id) {
            return false;
        }

        // Status must be active (scheduled or link_visible or in_progress)
        if (in_array($session->status, ['cancelled', 'completed'])) {
            return false;
        }

        return true;
    }
}

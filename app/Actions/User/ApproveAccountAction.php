<?php

namespace App\Actions\User;

use App\Enums\AccountStatus;
use App\Models\AccountStatusLog;
use App\Models\User;

class ApproveAccountAction
{
    public function execute(User $user, User $actor, ?string $reason = null): User
    {
        $oldStatus = $user->status;

        $user->update([
            'status' => AccountStatus::APPROVED,
        ]);

        AccountStatusLog::create([
            'user_id' => $user->id,
            'actor_user_id' => $actor->id,
            'old_status' => $oldStatus?->value ?? (string) $oldStatus,
            'new_status' => AccountStatus::APPROVED->value,
            'reason' => $reason ?? 'Account approved by administrator.',
        ]);

        return $user;
    }
}

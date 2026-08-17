<?php

namespace App\Actions\User;

use App\Enums\AccountStatus;
use App\Models\AccountStatusLog;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class ApproveAccountAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $targetUser, User $adminUser, ?string $reason = 'Approved by administrator'): User
    {
        $previousStatus = $targetUser->status;

        $updatedUser = $this->userRepository->update($targetUser, [
            'status' => AccountStatus::APPROVED->value,
            'email_verified_at' => $targetUser->email_verified_at ?? now(),
        ]);

        AccountStatusLog::create([
            'user_id' => $targetUser->id,
            'changed_by_user_id' => $adminUser->id,
            'previous_status' => $previousStatus,
            'new_status' => AccountStatus::APPROVED->value,
            'reason' => $reason,
        ]);

        return $updatedUser;
    }
}

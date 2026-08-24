<?php

namespace App\Services\Meeting\Contracts;

use App\Models\LiveSession;
use App\Models\User;

interface MeetingProviderInterface
{
    public function getName(): string;

    public function getSlug(): string;

    public function supportsEmbedding(): bool;

    /**
     * Generate secure client payload required to initialize embedded meeting or secure view.
     */
    public function generateAccessPayload(LiveSession $session, User $user): array;

    /**
     * Create meeting resource with provider API if applicable.
     */
    public function createMeeting(LiveSession $session, array $options = []): array;
}

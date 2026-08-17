<?php

namespace App\Actions\Session;

use App\Models\ExceptionRequest;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SubmitExceptionRequestAction
{
    public function execute(User $student, LiveSession $liveSession, string $reason, ?string $attachmentPath = null): ExceptionRequest
    {
        // Rule: Exception requests must be submitted at least 2 hours (120 minutes) prior to scheduled session time
        $minutesUntilStart = now()->diffInMinutes($liveSession->scheduled_at, false);

        if ($minutesUntilStart < 120) {
            throw ValidationException::withMessages([
                'live_session_id' => ['Requests for absence excuses must be submitted at least 2 hours prior to the live session scheduled time.'],
            ]);
        }

        return ExceptionRequest::create([
            'student_user_id' => $student->id,
            'live_session_id' => $liveSession->id,
            'reason' => $reason,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);
    }
}

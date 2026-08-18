<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case SUBMITTED = 'submitted';
    case COMPLETED = 'completed';
    case REVIEWED = 'reviewed';
    case LATE = 'late';
}

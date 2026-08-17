<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case COMPLETED = 'completed';
    case LATE = 'late';
}

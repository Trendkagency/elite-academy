<?php

namespace App\Enums;

enum SessionProgressStatus: string
{
    case LOCKED = 'locked';
    case UNLOCKED = 'unlocked';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
}

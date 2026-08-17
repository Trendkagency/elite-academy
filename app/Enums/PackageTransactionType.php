<?php

namespace App\Enums;

enum PackageTransactionType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
}

<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountStatusLog extends Model
{
    protected $fillable = [
        'user_id',
        'changed_by_user_id',
        'previous_status',
        'new_status',
        'reason',
    ];

    protected $casts = [
        'previous_status' => AccountStatus::class,
        'new_status' => AccountStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

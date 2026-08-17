<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageTransaction extends Model
{
    protected $table = 'package_transactions';

    public $timestamps = false;

    protected $fillable = [
        'student_package_id',
        'live_session_id',
        'type',
        'sessions_delta',
        'balance_before',
        'balance_after',
        'reason',
        'performed_by',
        'created_at',
    ];

    protected $casts = [
        'sessions_delta' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'created_at' => 'datetime',
    ];

    public function studentPackage(): BelongsTo
    {
        return $this->belongsTo(StudentPackage::class, 'student_package_id');
    }

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageVersion extends Model
{
    protected $fillable = [
        'landing_page_id',
        'version_number',
        'snapshot_json',
        'created_by',
        'status',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'snapshot_json' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingProvider extends Model
{
    use HasFactory;

    protected $table = 'meeting_providers';

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'supports_embedding',
        'encrypted_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'supports_embedding' => 'boolean',
        'encrypted_settings' => 'encrypted:array',
    ];

    public function sessionMeetings(): HasMany
    {
        return $this->hasMany(SessionMeeting::class, 'meeting_provider_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationValue extends Model
{
    protected $table = 'translation_values';

    protected $fillable = [
        'translation_key_id',
        'locale',
        'value',
        'source',
        'status',
        'is_locked',
        'translated_by',
        'reviewed_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class, 'translation_key_id');
    }

    public function translatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'translated_by');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TranslationHistory::class, 'translation_value_id')->orderBy('created_at', 'desc');
    }
}

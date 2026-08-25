<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationHistory extends Model
{
    protected $table = 'translation_histories';

    public $timestamps = false;

    protected $fillable = [
        'translation_value_id',
        'old_value',
        'new_value',
        'locale',
        'source',
        'changed_by_user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function translationValue(): BelongsTo
    {
        return $this->belongsTo(TranslationValue::class, 'translation_value_id');
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

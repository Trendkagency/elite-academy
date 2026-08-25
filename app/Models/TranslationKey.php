<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationKey extends Model
{
    protected $table = 'translation_keys';

    protected $fillable = [
        'key',
        'group',
        'description',
        'context',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(TranslationValue::class, 'translation_key_id');
    }

    public function getValueForLocale(string $locale): ?string
    {
        $val = $this->values->firstWhere('locale', $locale);
        return $val ? $val->value : null;
    }

    public function getTranslationValueModel(string $locale): ?TranslationValue
    {
        return $this->values->firstWhere('locale', $locale);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageTemplate extends Model
{
    protected $table = 'package_templates';

    protected $fillable = [
        'name',
        'sessions_count',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'sessions_count' => 'integer',
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    public function studentPackages(): HasMany
    {
        return $this->hasMany(StudentPackage::class, 'package_template_id');
    }
}

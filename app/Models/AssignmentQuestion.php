<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'question_text',
        'image_path',
        'question_type',
        'points',
        'sort_order',
        'is_multiple_choice',
    ];

    protected $casts = [
        'points' => 'float',
        'sort_order' => 'integer',
        'is_multiple_choice' => 'boolean',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssignmentQuestionOption::class, 'question_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Get options safe for student view (stripping is_correct flag)
     */
    public function safeOptionsForStudent(): array
    {
        return $this->options->map(function ($opt) {
            return [
                'id' => $opt->id,
                'option_text' => $opt->option_text,
                'image_path' => $opt->image_path ? asset('storage/' . $opt->image_path) : null,
                'sort_order' => $opt->sort_order,
            ];
        })->toArray();
    }
}

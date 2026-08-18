<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'image_path',
        'sort_order',
        'is_correct',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_correct' => 'boolean',
    ];

    protected $hidden = [
        // Ensure is_correct is never accidentally serialized to JSON for API calls unless explicit
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssignmentQuestion::class, 'question_id');
    }
}

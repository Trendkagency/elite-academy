<?php

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isTeacher() || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'grade' => ['required', 'integer', 'min:0', 'max:100'],
            'teacher_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

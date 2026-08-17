<?php

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'assignment_id' => ['required', 'integer', 'exists:assignments,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'content' => ['nullable', 'string'],
            'file_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}

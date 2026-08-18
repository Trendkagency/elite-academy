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
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'answers' => ['sometimes', 'nullable', 'array'],
            'answers.*' => ['nullable'],
        ];
    }
}

<?php

namespace App\Http\Requests\Session;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExceptionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('session_id') && ! $this->has('live_session_id')) {
            $merge['live_session_id'] = $this->input('session_id') ?: null;
        }
        if ($this->has('assignment_id') && ! $this->has('homework_assignment_id')) {
            $merge['homework_assignment_id'] = $this->input('assignment_id') ?: null;
        }
        if ($this->has('homework_id') && ! $this->has('homework_assignment_id')) {
            $merge['homework_assignment_id'] = $this->input('homework_id') ?: null;
        }
        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'live_session_id' => ['nullable', 'integer', 'exists:live_sessions,id'],
            'homework_assignment_id' => ['nullable', 'integer', 'exists:homework_assignments,id'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'scope' => ['nullable', 'string', 'in:course,global'],
            'is_global' => ['nullable', 'boolean'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'attachment_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}

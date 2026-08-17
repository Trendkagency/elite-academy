<?php

namespace App\Http\Requests\Session;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExceptionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'live_session_id' => ['nullable', 'integer', 'exists:live_sessions,id'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'scope' => ['nullable', 'string', 'in:course,global'],
            'is_global' => ['nullable', 'boolean'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
            'attachment_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}

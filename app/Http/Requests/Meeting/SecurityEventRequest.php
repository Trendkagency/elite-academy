<?php

namespace App\Http\Requests\Meeting;

use Illuminate\Foundation\Http\FormRequest;

class SecurityEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}

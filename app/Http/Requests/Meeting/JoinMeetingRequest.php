<?php

namespace App\Http\Requests\Meeting;

use Illuminate\Foundation\Http\FormRequest;

class JoinMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'provider_slug' => ['nullable', 'string', 'in:zoom,google_meet,teams,custom'],
        ];
    }
}

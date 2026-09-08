<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => __('app.auth.invalid_email_format'),
            'email.email'       => __('app.auth.invalid_email_format'),
            'password.required' => __('app.auth.password_min_length'),
        ];
    }

    public function attributes(): array
    {
        return [
            'email'    => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
        ];
    }
}

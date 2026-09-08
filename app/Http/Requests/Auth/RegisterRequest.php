<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'user_type' => ['nullable', 'string', 'in:student,teacher,parent'],
            'grade_level_id' => ['nullable', 'integer', 'exists:grade_levels,id'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer', 'exists:subjects,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => __('app.auth.name_required'),
            'name.min'                => __('app.auth.name_required'),
            'email.required'          => __('app.auth.invalid_email_format'),
            'email.email'             => __('app.auth.invalid_email_format'),
            'email.unique'            => __('app.auth.email_already_registered'),
            'phone.required'          => __('app.auth.invalid_phone_format'),
            'phone.unique'            => __('app.auth.phone_already_registered'),
            'password.required'       => __('app.auth.password_min_length'),
            'password.min'            => __('app.auth.password_min_length'),
            'grade_level_id.required' => __('app.auth.grade_level_required'),
            'grade_level_id.exists'   => __('app.auth.grade_level_required'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => __('validation.attributes.name'),
            'email'          => __('validation.attributes.email'),
            'phone'          => __('validation.attributes.phone'),
            'password'       => __('validation.attributes.password'),
            'user_type'      => __('validation.attributes.role'),
            'grade_level_id' => __('validation.attributes.grade_level_id'),
            'school_name'    => __('validation.attributes.school_name'),
        ];
    }
}

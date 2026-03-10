<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone'    => ['sometimes', 'nullable', 'string', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
        ];
    }
}

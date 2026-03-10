<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'phone'       => ['sometimes', 'nullable', 'string', 'max:30'],
            'whatsapp'    => ['sometimes', 'nullable', 'string', 'max:30'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}

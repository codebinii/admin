<?php

declare(strict_types=1);

namespace App\Http\Requests\Saas;

use Illuminate\Foundation\Http\FormRequest;

final class SyncModulosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modulos'   => ['present', 'array'],
            'modulos.*' => ['integer', 'exists:04modulos,id'],
        ];
    }
}

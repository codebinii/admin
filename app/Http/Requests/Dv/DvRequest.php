<?php

declare(strict_types=1);

namespace App\Http\Requests\Dv;

use Illuminate\Foundation\Http\FormRequest;

final class DvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nits' => ['required', 'array', 'min:1', 'max:100'],
            'nits.*' => ['required', 'string', 'max:20'],
        ];
    }
}

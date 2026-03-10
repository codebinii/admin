<?php

declare(strict_types=1);

namespace App\Http\Requests\Hash;

use Illuminate\Foundation\Http\FormRequest;

final class GenerarClaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'longitud' => ['sometimes', 'integer', 'min:1', 'max:256'],
            'numeros' => ['sometimes', 'boolean'],
            'minusculas' => ['sometimes', 'boolean'],
            'mayusculas' => ['sometimes', 'boolean'],
            'especiales' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $v): void {
            $numeros = filter_var($this->input('numeros', true), FILTER_VALIDATE_BOOLEAN);
            $minusculas = filter_var($this->input('minusculas', true), FILTER_VALIDATE_BOOLEAN);
            $mayusculas = filter_var($this->input('mayusculas', true), FILTER_VALIDATE_BOOLEAN);
            $especiales = filter_var($this->input('especiales', true), FILTER_VALIDATE_BOOLEAN);

            if (! $numeros && ! $minusculas && ! $mayusculas && ! $especiales) {
                $v->errors()->add('opciones', 'Al menos un conjunto de caracteres debe estar activo.');
            }
        });
    }
}

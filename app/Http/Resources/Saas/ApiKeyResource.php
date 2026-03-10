<?php

declare(strict_types=1);

namespace App\Http\Resources\Saas;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ApiKeyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nombre'     => $this->nombre,
            'key_prefix' => $this->key_prefix,
            'activo'     => $this->activo,
            'created_at' => $this->created_at,
        ];
    }
}

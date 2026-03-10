<?php

declare(strict_types=1);

namespace App\Http\Resources\Saas;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ModuloResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'nombre_modulo' => $this->nombre_modulo,
            'activo'        => (bool) $this->activo,
            // pivot present when loaded via empresa->modulos relationship
            'activo_empresa' => $this->whenPivotLoaded('empresa_modulos', fn () => (bool) $this->pivot->activo),
            'created_at'    => $this->created_at,
        ];
    }
}

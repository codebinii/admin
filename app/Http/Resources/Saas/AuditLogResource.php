<?php

declare(strict_types=1);

namespace App\Http\Resources\Saas;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'accion'     => $this->accion,
            'empresa_id' => $this->empresa_id,
            'usuario'    => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
            'datos'      => $this->datos,
            'created_at' => $this->created_at,
        ];
    }
}

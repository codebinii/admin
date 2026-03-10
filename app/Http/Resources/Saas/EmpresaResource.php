<?php

declare(strict_types=1);

namespace App\Http\Resources\Saas;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EmpresaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'cod_empresa'  => $this->cod_empresa,
            'nombre'       => $this->nombre,
            'sigla'        => $this->sigla,
            'pais'         => $this->pais,
            'estado'       => $this->estado,
            'email_admin'  => $this->email_admin,
            'no_celular'   => $this->no_celular,
            'created_at'   => $this->created_at,
        ];
    }
}

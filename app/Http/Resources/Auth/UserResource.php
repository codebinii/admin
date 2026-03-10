<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'email'                => $this->email,
            'email_verified_at'    => $this->email_verified_at?->toIso8601String(),
            'phone'                => $this->phone,
            'phone_verified_at'    => $this->phone_verified_at?->toIso8601String(),
            'whatsapp'             => $this->whatsapp,
            'whatsapp_verified_at' => $this->whatsapp_verified_at?->toIso8601String(),
            'created_at'           => $this->created_at?->toIso8601String(),
        ];
    }
}

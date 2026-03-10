<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hash;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hash\HashRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class HashController extends Controller
{
    public function __invoke(HashRequest $request): JsonResponse
    {
        $texto = $request->string('texto')->toString();

        // All algorithms natively supported by the current PHP installation
        $hashes = collect(hash_algos())
            ->map(fn (string $algo): array => [
                'algoritmo' => $algo,
                'valor'     => hash($algo, $texto),
            ])
            ->values()
            ->concat([
                // Password hashing — result varies per execution (salt)
                ['algoritmo' => 'bcrypt', 'valor' => bcrypt($texto)],
                // Base64 encoding — not a hash, included as utility
                ['algoritmo' => 'base64', 'valor' => base64_encode($texto)],
            ]);

        return ApiResponse::ok([
            'texto'   => $texto,
            'total'   => $hashes->count(),
            'hashes'  => $hashes,
        ]);
    }
}

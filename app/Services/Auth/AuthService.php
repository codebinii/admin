<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

final class AuthService
{
    /**
     * @throws AuthenticationException
     */
    public function login(string $email, string $password, string $deviceName): string
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return $user->createToken($deviceName)->plainTextToken;
    }

    public function register(
        string $name,
        string $email,
        string $password,
        string $deviceName,
        ?string $phone = null,
        ?string $whatsapp = null,
    ): array {
        $user = User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => $password,
            'phone'     => $phone,
            'whatsapp'  => $whatsapp,
        ]);

        $token = $user->createToken($deviceName)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }
}

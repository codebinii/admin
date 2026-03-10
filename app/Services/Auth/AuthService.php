<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

final class AuthService
{
    /** @throws AuthenticationException */
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
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'phone'    => $phone,
            'whatsapp' => $whatsapp,
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken($deviceName)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function updateProfile(User $user, array $data): User
    {
        $emailChanged = isset($data['email']) && $data['email'] !== $user->email;

        $user->fill($data);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return $user->fresh();
    }

    /** @throws AuthenticationException */
    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw new AuthenticationException('Current password is incorrect.');
        }

        $user->update(['password' => $newPassword]);
    }

    public function sendVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    public function verifyEmail(User $user, string $hash): bool
    {
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true;
        }

        $user->markEmailAsVerified();

        return true;
    }

    /** Returns Password::RESET_LINK_SENT or Password::INVALID_USER */
    public function sendPasswordResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /** Returns Password::PASSWORD_RESET or a failure status string */
    public function resetPassword(string $email, string $token, string $password): string
    {
        return Password::reset(
            ['email' => $email, 'token' => $token, 'password' => $password],
            function (User $user, string $newPassword): void {
                $user->update(['password' => $newPassword]);
                $user->tokens()->delete(); // revoke all Sanctum tokens on password reset
            },
        );
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

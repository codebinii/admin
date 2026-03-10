<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

final class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $token)
    {
        // Send synchronously on local/testing — use queue on production
        if (! app()->isProduction()) {
            $this->onConnection('sync');
        }
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expiresMins = Config::get('auth.passwords.users.expire', 60);
        $email       = $notifiable->getEmailForPasswordReset();
        $frontendUrl = config('app.frontend_url');

        $resetUrl = $frontendUrl
            ? rtrim($frontendUrl, '/') . '/reset-password?token=' . urlencode($this->token) . '&email=' . urlencode($email)
            : null;

        return (new MailMessage())
            ->subject(trans('auth.reset_subject', ['app' => config('app.name')]))
            ->view('emails.auth.reset-password', [
                'user'        => $notifiable,
                'token'       => $this->token,
                'email'       => $email,
                'resetUrl'    => $resetUrl,
                'appName'     => config('app.name'),
                'expiresMins' => $expiresMins,
            ]);
    }
}

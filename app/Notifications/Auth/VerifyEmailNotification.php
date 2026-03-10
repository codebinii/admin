<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

final class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
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
        $url = $this->buildVerificationUrl($notifiable);

        return (new MailMessage())
            ->subject(trans('auth.verify_subject', ['app' => config('app.name')]))
            ->view('emails.auth.verify', [
                'user'        => $notifiable,
                'verifyUrl'   => $url,
                'appName'     => config('app.name'),
                'expiresMins' => Config::get('auth.verification.expire', 60),
            ]);
    }

    private function buildVerificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'auth.email.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}

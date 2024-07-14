<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60), // URL expires in 60 minutes
                ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
            );
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->greeting('Hello, ' . $notifiable->name . "!")
                ->line('Please, click the button below to verify your email address.')
                ->action('Verify Email Address', $signedUrl)
                ->salutation('Best Regards, event-planner!');
        });
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'https://event-planner-api.onrender.com/api/reset-password?token=' . $token;
        });
    }
}

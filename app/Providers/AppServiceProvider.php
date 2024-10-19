<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Routing\UrlGenerator;
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
        if (config('app.env') == 'production') {
            $url->forceScheme('https');
        }
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->greeting('Hello, ' . $notifiable->name . "!")
                ->line('Please, click the button below to verify your email address.')
                ->action('Verify Email Address', $url)
                ->salutation('Best Regards, event-planner!');
        });
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'https://event-planner-api.onrender.com/api/reset-password?token=' . $token;
        });
    }
}

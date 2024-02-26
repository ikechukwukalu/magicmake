<?php

namespace App\Providers;

use App\Events\ForgotPassword;
use App\Events\EmailVerification;
use App\Events\SocialiteLogin;
use App\Events\TwoFactorLogin;
use App\Listeners\SendResetLink;
use App\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        ForgotPassword::class => [
            SendResetLink::class
        ],
        EmailVerification::class => [
            SendEmailVerificationNotification::class
        ],
        TwoFactorLogin::class => [
        ],
        SocialiteLogin::class => [
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

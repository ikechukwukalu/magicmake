<?php

namespace App\Listeners;

use App\Events\EmailVerification;

class SendEmailVerificationNotification extends UserEventListener
{
    private EmailVerification $event;

    public function handle($event)
    {
        $this->event = $event;
        $this->event->user->sendEmailVerificationNotification();
    }
}

<?php

namespace App\Listeners;

use App\Events\PhoneVerification;

class SendPhoneVerificationNotification extends UserEventListener
{
    private PhoneVerification $event;

    public function handle($event)
    {
        $this->event = $event;
        $this->event->user->sendPhoneVerificationNotification($this->event->otp, $this->event->user);
    }
}
